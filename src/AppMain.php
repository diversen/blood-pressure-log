<?php

declare(strict_types=1);

namespace App;

use Pebble\Router;
use App\AppUtils;
use App\Settings\SetupIntl;

/**
 * AppMain class. This is the main class for the app.
 * It is used in www/index.php
 */
class AppMain extends AppUtils
{
    public const VERSION = "v1.0.2";

    public function __construct()
    {
    }

    public function run()
    {
        // Set up include_path and some other stuff before we construct all services
        // Doing this here means we will catch all errors in a nice way
        $utils = $this->getUtils();
        $utils->addBaseToIncudePath();
        $utils->addBaseToIncudePath();
        $utils->addSrcToIncludePath();
        $utils->setErrorHandler();
        $utils->sendSSLHeaders();
        $utils->sessionStart();
        $utils->setDebug();

        // Construct all other services
        parent::__construct();

        // Now we can use the extends the AppUtils services in the app
        $this->csp->sendCSPHeaders();
        $this->csrf->setCSRFToken(verbs: ['GET'], exclude_paths: ['/account/captcha']);

        (new SetupIntl())->setupIntl();

        $router = new Router();

        $router->setFasterRouter(base_controller: 'Main');
        $router->addClass(\App\Main\Controller::class);
        $router->addClass(\App\QA\Controller::class);
        $router->addClass(\App\Account\ControllerExt::class);
        $router->addClass(\App\Google\Controller::class);
        $router->addClass(\App\Error\Controller::class);
        $router->addClass(\App\Settings\Controller::class);
        $router->addClass(\App\TwoFactor\Controller::class);
        $router->addClass(\App\Admin\Controller::class);
        $router->run();
    }
}
