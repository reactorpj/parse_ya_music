<?php

namespace App\Entity;

class Artist implements Item
{
	public ?int $id = null;
	public string $outerId;
	public ?string $title;
	public int $favoriteCount = 0;
	public int $auditionCount = 0;
	public int $albumCount = 0;
}