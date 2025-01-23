<?php

namespace App\Entity;

class Track implements Item
{
	public ?int $id = null;

	public ?string $outerId = null;
	public string $title;
	public string $duration;
}