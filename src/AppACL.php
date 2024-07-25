<?php

declare(strict_types=1);

namespace App;

use Pebble\App\StdUtils;
use Diversen\Lang;
use Pebble\Exception\JSONException;
use Pebble\Exception\ForbiddenException;
use App\Main\ReadingModel;

/**
 * App spcific ACL
 */
class AppACL extends StdUtils
{

    public function isAdmin()
    {
        $this->acl_role->inSessionHasRole('admin');
    }

    public function isAdminJsonException()
    {
        if (!$this->isAdmin()) {
            throw new JSONException(Lang::translate('You are not allowed to do this. You are not admin'));
        }
    }

    public function isAdminForbiddenException()
    {
        if (!$this->isAdmin()) {
            throw new ForbiddenException(Lang::translate('You are not allowed to do this. You are not admin'));
        }
    }

    public function isOwnerOfReading(int $reading_id): array
    {

        $this->acl->isAuthenticatedOrThrowJSONException();

        $reading_model = new ReadingModel();
        $reading = $reading_model->getReadingByAuthID($reading_id);
        if (!$reading) {
            throw new JSONException(Lang::translate('You are not allowed to access this reading'));
        }

        return $reading;
    }
}
