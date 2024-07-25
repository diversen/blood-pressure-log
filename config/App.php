<?php

return [
	'server_scheme' => 'http',
	'server_name' => 'localhost:8000', // 'hostname -I' . ':8080', // 
	'site_name' => 'The Blood Pressure Log',
	'timezone' => 'UTC',
	'login_redirect' => '/main/reading',
	'logout_redirect' => '/account/signin',
	'env' => 'dev',
	'base_path' => dirname(__FILE__) . '/..',
	'server_url' => 'http://localhost:8000',
	'pager_limit' => 100,
	'home_url_authenticated' => '/main/reading',
	'home_url' => '/',
	'contact_email' => 'info@10kilobyte.com',
	'company_name' => '10Kilobyte',
];
