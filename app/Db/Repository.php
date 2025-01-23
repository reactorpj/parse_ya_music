<?php

namespace App\Db;

use App\Entity\Artist;
use App\Entity\Track;
use App\Entity\TrackCollection;
use PDO;
use RuntimeException;

class Repository
{
	private Db $db;

	public function __construct()
	{
		$this->db = Db::getInstance();
	}

	public function addArtist(Artist $artist): Artist
	{
		$exists = $this->getArtist($artist->outerId);

		if ($exists !== null)
		{
			$this->updateArtist($artist, $exists->id);
			$artist->id = $exists->id;

			return $artist;

		}

		$statement = $this->db->prepare('INSERT INTO artists (outer_id, title, favorite_count, audition_count, album_count) VALUES(:outerId, :title, :favoriteCount, :auditionCount, :albumCount)');
		$statement->bindValue(':outerId', $artist->outerId);
		$statement->bindValue(':title', $artist->title);
		$statement->bindValue(':favoriteCount', $artist->favoriteCount, PDO::PARAM_INT);
		$statement->bindValue(':auditionCount', $artist->auditionCount, PDO::PARAM_INT);
		$statement->bindValue(':albumCount', $artist->albumCount, PDO::PARAM_INT);

		$result = $this->db->exec($statement);
		if (!$result)
		{
			throw new RuntimeException('Could not add artist');
		}

		$id = $this->db->getLastId('artists');
		if ($id === false)
		{
			throw new RuntimeException('Could not add artist');
		}

		$artist->id = (int)$id;

		return $artist;
	}

	public function addTracks(TrackCollection $tracks, Artist $artist): void
	{
		/** @var Track $track */
		foreach ($tracks->toArray() as $track)
		{
			try
			{
				$this->addTrack($track, $artist);
			}
			catch (RuntimeException $e)
			{
				var_dump($e->getMessage());
				continue;
			}
		}
	}

	public function addTrack(Track $track, Artist $artist): void
	{
		$exists = $this->getTrack($track->outerId);
		if ($exists !== null)
		{
			$this->updateTrack($track, $exists->id);

			return;
		}

		$statement = $this->db->prepare('INSERT INTO tracks (outer_id, title, duration, artist_id) VALUES (:outerId, :title, :duration, :artistId)');

		$statement->bindValue(':outerId', $track->outerId);
		$statement->bindValue(':title', $track->title);
		$statement->bindValue(':duration', $track->duration);
		$statement->bindValue(':artistId', $artist->id, PDO::PARAM_INT);

		$this->db->exec($statement);
		$id = $this->db->getLastId('tracks');
		if ($id === false)
		{
			throw new RuntimeException('Could not add track');
		}

		$track->id = (int)$id;
	}

	public function updateArtist(Artist $artist, int $id): void
	{
		$statement = $this->db->prepare('update artists set title=:title, favorite_count=:favorite_count, audition_count=:audition_count, album_count=:album_count WHERE id=:id');
		$statement->bindValue(':title', $artist->title);
		$statement->bindValue(':favorite_count', $artist->favoriteCount, PDO::PARAM_INT);
		$statement->bindValue(':audition_count', $artist->auditionCount, PDO::PARAM_INT);
		$statement->bindValue(':album_count', $artist->albumCount, PDO::PARAM_INT);
		$statement->bindValue(':id', $id, PDO::PARAM_INT);

		$statement->bindValue(':id', $id);

		$result = $this->db->exec($statement);

		if (!$result)
		{
			throw new RuntimeException('Could not update track');
		}
	}

	public function getArtist(string $outerId): ?Artist
	{
		$statement = $this->db->prepare('SELECT * FROM artists WHERE outer_id = :outerId limit 1');
		$statement->bindValue(':outerId', $outerId);
		$result = $this->db->fetch($statement);
		if ($result === false)
		{
			return null;
		}

		$artist = new Artist();
		$artist->id = (int)$result['id'];
		$artist->outerId = $result['outer_id'];
		$artist->title = $result['title'];
		$artist->favoriteCount = $result['favorite_count'];
		$artist->auditionCount = $result['audition_count'];
		$artist->albumCount = $result['album_count'];

		return $artist;
	}

	public function getTrack(string $outerId): ?Track
	{
		$statement = $this->db->prepare('SELECT * FROM tracks WHERE outer_id = :outerId limit 1');
		$statement->bindValue(':outerId', $outerId);
		$result = $this->db->fetch($statement);
		if ($result === false)
		{
			return null;
		}

		$track = new Track();
		$track->id = (int)$result['id'];
		$track->title = $result['title'];
		$track->duration = $result['duration'];

		return $track;
	}

	private function updateTrack(Track $track, int $id): void
	{
		$statement = $this->db->prepare('update tracks set title=:title, duration=:duration WHERE id=:id');

		$statement->bindValue(':title', $track->title);
		$statement->bindValue(':duration', $track->duration);
		$statement->bindValue(':id', $id);

		$result = $this->db->exec($statement);
		if (!$result)
		{
			throw new RuntimeException('Could not update track');
		}
	}
}