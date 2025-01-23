<?php

namespace App\Parser;

use App\Entity\Track;
use App\Entity\TrackCollection;

class TrackJsonParser
{
	public function parseTracks(array $tracks): TrackCollection
	{
		return $this->makeTracksCollection($tracks);
	}

	private function makeTracksCollection(array $tracks): TrackCollection
	{
		$data = [];

		foreach ($tracks as $track)
		{
			$entity = new Track();

			$entity->title = $track['title'];
			$entity->outerId = $track['id'];
			$entity->duration = $this->getDurationFromMs($track['durationMs']);

			$data[] = $entity;
		}

		return new TrackCollection($data);
	}

	public function getDurationFromMs(?int $durationMs): string
	{
		if ($durationMs === null)
		{
			return '';
		}

		$duration = (int)($durationMs / 1000);
		$minutes = (int)($duration / 60);
		$seconds = ($duration % 60);
		if ($seconds < 10)
		{
			$seconds = '0' . $seconds;
		}

		return "$minutes:$seconds";
	}
}