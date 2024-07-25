<?php
	
return [
	'url' => 'mysql:host=127.0.0.1;dbname=bp',
	'username' => 'root',
	'password' => 'password',
	'options' => [
		PDO::ATTR_STRINGIFY_FETCHES => false, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode=""',
	],
];
