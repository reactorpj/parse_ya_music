<?php

namespace App\Db;

use PDO;
use PDOStatement;

class Db
{
	private PDO $pdo;

	private function __construct()
	{
		$this->pdo = new PDO('mysql:host=yamusmysql;dbname=yandex_music;charset=utf8', 'root', 'root');
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
