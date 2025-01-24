<?php

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

if (!function_exists('env')) {
	function env($key, $default = null)
	{
		$key = mb_strtoupper($key);

		return $_ENV[$key] ?? $default;
	}
}