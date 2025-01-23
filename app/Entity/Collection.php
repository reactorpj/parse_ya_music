<?php

namespace App\Entity;

abstract class Collection
{
	/**
	 * @var Item[]
	 */
	protected array $collection;

	/**
	 * @param Item[] $collection
	 */
	public function __construct(array $collection = [])
	{
		$this->collection = $collection;
	}

	public function count(): int
	{
		return count($this->collection);
	}

	public function toArray(): array
	{
		return $this->collection;
	}
}