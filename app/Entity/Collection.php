<?php

namespace App\Entity;

use Countable;
use Iterator;

abstract class Collection implements Iterator, Countable
{
	/**
	 * @var Item[]
	 */
	protected array $collection;
	protected int $pointer = 0;

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

	public function current(): Item
	{
		return $this->collection[$this->pointer];
	}

	public function next(): void
	{
		$this->pointer++;
	}

	public function key(): int
	{
		return $this->pointer;
	}

	public function valid(): bool
	{
		return $this->pointer < count($this->collection);
	}

	public function rewind(): void
	{
		$this->pointer = 0;
	}
}