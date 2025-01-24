<?php

namespace App\Parser;

use App\Entity\Track;
use App\Entity\TrackCollection;

class TrackJsonParser
{
	public function parseTracks(array $tracks, int $artistId): TrackCollection
	{
		return $this->makeTracksCollection($tracks, $artistId);
	}

	private function makeTracksCollection(array $tracks, int $artistId): TrackCollection
	{
		$data = [];

		foreach ($tracks as $track)
		{
			$entity = new Track();

			$entity->title = $track['title'];
			$entity->outerId = $track['id'];
			$entity->duration = $this->getDurationFromMs($track['durationMs']);
			$entity->artistId = $artistId;

			$data[] = $entity;
		}

		return new TrackCollection($data);
	}

	public function getDurationFromMs(?int $durationMs): int
	{
		if ($durationMs === null)
		{
			return 0;
		}

		return (int)($durationMs / 1000);
	}
}