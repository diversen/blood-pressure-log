<?php

declare(strict_types=1);

namespace App\Main;

use Diversen\Lang;
use Pebble\Attributes\Route;
use Pebble\Router\Request;
use App\AppUtils;
use App\Main\ReadingModel;

class Controller extends AppUtils
{
    private $reading_model;
    public function __construct()
    {
        parent::__construct();
        $this->reading_model = new ReadingModel();
    }

    #[Route(path: '/')]
    public function index()
    {
        if ($this->auth->getAuthId()) {
            header('Location: /main/reading');
        }

        $data = ['title' => Lang::translate('Home')];
        echo $this->twig->render('main/home.twig', $this->getContext($data));
    }

    #[Route(path: '/main/alias')]
    public function alias()
    {
        $this->acl->isAuthenticatedOrThrow();
        $user_aliases = $this->reading_model->getUserAliases();

        $data = ['title' => Lang::translate('Alias'), 'user_aliases' => $user_aliases];
        echo $this->twig->render('main/alias.twig', $this->getContext($data));
    }

    #[Route(path: '/main/alias/set', verbs: ['POST'])]
    public function alias_set()
    {
        $this->acl->isAuthenticatedOrThrowJSONException();
        $auth_id = $this->auth->getAuthId();
        $cache_id = ["last_user_alias_id", $auth_id];
        $this->db_cache->set($cache_id, (int)$_POST['user_alias_id']);
        $this->flash->setMessage(Lang::translate('Alias updated'), 'success', ['flash_remove' => true]);
        $this->json->renderSuccess();
    }

    #[Route(path: '/main/alias_post', verbs: ['POST'])]
    public function alias_post()
    {
        $this->acl->isAuthenticatedOrThrowJSONException();
        $this->reading_model->insertAlias();
        $this->flash->setMessage(Lang::translate('Alias inserted'), 'success', ['flash_remove' => true]);
        $this->json->renderSuccess();
    }

    #[Route(path: '/main/alias/delete/:id', verbs: ['POST'], cast: ['id' => 'int'])]
    public function alias_delete(Request $request)
    {
        $this->acl->isAuthenticatedOrThrowJSONException();
        $this->reading_model->deleteAlias($request->param('id'));
        $this->flash->setMessage(Lang::translate('Alias deleted'), 'success', ['flash_remove' => true]);
        $this->json->renderSuccess();
    }

    #[Route(path: '/main/reading')]
    public function reading(Request $request): void
    {
        $this->acl->isAuthenticatedOrThrow();
        $cache_id = ["last_user_alias_id", $this->auth->getAuthId()];
        $last_user_alias_id = $this->db_cache->get($cache_id);
        $user_aliases = $this->reading_model->getUserAliases();
        $todays_readings = $this->reading_model->getDailyReadingsByAliasID($last_user_alias_id);
        $weekly_average = $this->reading_model->getWeeklyAverageByAliasID($last_user_alias_id);
        $daily_average = $this->reading_model->getDailyAverageByAliasID($last_user_alias_id);

        $context = $this->getContext([
            'title' => Lang::translate('Reading'),
            'today_readings' => $todays_readings,
            'weekly_average' => $weekly_average,
            'daily_average' => $daily_average,
            'user_aliases' => $user_aliases,
            'last_user_alias_id' => $last_user_alias_id,
        ]);

        echo $this->twig->render('main/reading.twig', $context);
    }

    #[Route(path: '/main/post_reading', verbs: ['POST'])]
    public function postReading(Request $request): void
    {
        $this->acl->isAuthenticatedOrThrowJSONException();
        $this->reading_model->addReadingByAuthID();
        $this->flash->setMessage(Lang::translate('Reading saved'), 'success', ['flash_remove' => true]);
        $this->json->renderSuccess(['redirect' => '/main/reading']);
    }

    #[Route(path: '/main/reading/delete/:id', verbs: ['POST'], cast: ['id' => 'int'])]
    public function deleteReading(Request $request): void
    {
        $this->app_acl->isOwnerOfReading($request->param('id'));
        $this->reading_model->deleteReading($request->param('id'));
        $this->flash->setMessage(Lang::translate('Reading deleted'), 'success', ['flash_remove' => true]);
        $this->json->renderSuccessWithMessage(Lang::translate('Reading deleted'));
    }
}
