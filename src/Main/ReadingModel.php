<?php

namespace App\Main;

use App\AppUtils;
use Pebble\Exception\JSONException;
use Diversen\Lang;

class ReadingModel extends AppUtils
{
    public function __construct()
    {
        parent::__construct();

        // get php timezone
        $timezone = date_default_timezone_get();
        $this->db->getDbh()->exec("SET time_zone='$timezone'");
    }


    private function validateReading()
    {

        $aliases = $this->getUserAliases();
        $alias_ids = array_column($aliases, 'id');
        if (!in_array((int)$_POST['user_alias_id'], $alias_ids)) {
            throw new JSONException(Lang::translate('You are not allowed to use this alias or it does not exist'));
        }
        
        if (!filter_var((int)$_POST['systolic'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 50, 'max_range' => 300]])) {
            throw new JSONException(Lang::translate('Systolic is not valid. It must be between 50 and 300'));
        }
        if (!filter_var((int)$_POST['diastolic'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 50, 'max_range' => 300]])) {
            throw new JSONException(Lang::translate('Diastolic is not valid. It must be between 50 and 300'));
        }
        if (!filter_var((int)$_POST['pulse'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 30, 'max_range' => 300]])) {
            throw new JSONException(Lang::translate('Pulse is not valid. It must be between 30 and 300'));
        }

    }

    public function getReadingByAuthID(int $reading_id): array
    {
        $sql = "SELECT * FROM reading WHERE auth_id = ? AND id = ?";
        $row = $this->db->prepareFetch($sql, [$this->auth->getAuthId(), $reading_id]);
        return $row;
    }

    public function getUserAliases()
    {
        $sql = "SELECT * FROM user_alias WHERE auth_id = ? ORDER BY alias";
        $rows = $this->db->prepareFetchAll($sql, [$this->auth->getAuthId()]);
        return $rows;
    }

    public function insertAlias()
    {
        if (strlen($_POST["alias"]) < 1) {
            throw new JSONException(Lang::translate('Alias must be at least one character'));
        }

        $values = ["alias" => $_POST["alias"], "auth_id" => $this->auth->getAuthId()];
        $this->db->insert('user_alias', $values);
    }

    public function deleteAlias($alias_id)
    {

        $this->db->delete('user_alias', ['id' => $alias_id, 'auth_id' => $this->auth->getAuthId()]);
        $affected = $this->db->rowCount();
        if ($affected == 0) {
            throw new JSONException(Lang::translate('You are not allowed to delete this alias'));
        }
    }

    public function addReadingByAuthID()
    {
        $this->validateReading();

        $auth_id = $this->auth->getAuthId();
        $_POST['auth_id'] = $auth_id;

        $date_formatted = date('Y-m-d H:i:s');
        $_POST['date_created'] = $date_formatted;
        $_POST['user_alias_id'] = (int)$_POST['user_alias_id'];
        unset($_POST['csrf_token']);

        $this->db->insert('reading', $_POST);
    }

    public function deleteReading($reading_id)
    {
        $this->db->delete('reading', ['id' => $reading_id, 'auth_id' => $this->auth->getAuthId()]);
    }


    public function getDailyReadingsByAliasID($alias_id)
    {

        $date = date('Y-m-d');
        $sql = "SELECT * FROM reading WHERE auth_id = ? and user_alias_id = ? AND DATE(date_created) = ? ORDER BY date_created DESC LIMIT 10";
        $rows = $this->db->prepareFetchAll($sql, [$this->auth->getAuthId(), $alias_id, $date]);
        return $rows;
    }

    /**
     * Get the weekly average for a given user over the last year
     */
    public function getWeeklyAverageByAliasID($alias_id, string $interval = '1 YEAR')
    {
        $sql = "
        SELECT 
            WEEK(date_created, 1) AS week_number, 
            AVG(systolic) AS systolic, 
            AVG(diastolic) AS diastolic,
            AVG(pulse) AS pulse  
        FROM reading WHERE auth_id = ? and user_alias_id = ? AND date_created > DATE_SUB(NOW(), INTERVAL $interval) 
        GROUP BY WEEK(date_created, 1) 
        ORDER BY date_created DESC";
        $rows = $this->db->prepareFetchAll($sql, [$this->auth->getAuthId(), $alias_id]);
        return $rows;
    }

    /**
     * Get the daily average for a given user over the last month
     */
    public function getDailyAverageByAliasID($alias_id, string $interval = '1 MONTH')
    {
        $sql = "
        SELECT 
            DATE(date_created) AS date, 
            AVG(systolic) AS systolic, 
            AVG(diastolic) AS diastolic,
            AVG(pulse) AS pulse
        FROM reading WHERE auth_id = ? AND user_alias_id = ? AND date_created > DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY DATE(date_created)
        ORDER BY date_created DESC";
        $rows = $this->db->prepareFetchAll($sql, [$this->auth->getAuthId(), $alias_id]);
        return $rows;
    }
}
