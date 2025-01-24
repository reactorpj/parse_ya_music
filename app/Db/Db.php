<?php

namespace App\Db;

use PDO;
use PDOStatement;

class Db
{
	private PDO $pdo;

	private function __construct()
	{
		$host = env('DB_HOST');
		$dbName = env('DB_NAME');
		$password = env('DB_PASSWORD');
		$user = env('DB_USER');

		$this->pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8", $user, $password);
	}


	public static function getInstance(): Db
	{
		return new self();
	}

	public function prepare(string $query): PDOStatement
	{
		return $this->pdo->prepare($query);
	}

	public function exec(PDOStatement $statement): bool
	{
		return $statement->execute();
	}

	public function getLastId(?string $name = null): false|string
	{
		return $this->pdo->lastInsertId($name);
	}

	public function fetch(PDOStatement $statement): mixed
	{
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$statement->execute();

		return $statement->fetch();
	}
}
