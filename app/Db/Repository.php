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

	public function addTrack(Track $track, int $artistId): Track
	{
		$statement = $this->db->prepare('INSERT INTO tracks (outer_id, title, duration, artist_id) VALUES (:outerId, :title, :duration, :artistId)');

		$statement->bindValue(':outerId', $track->outerId);
		$statement->bindValue(':title', $track->title);
		$statement->bindValue(':duration', $track->duration);
		$statement->bindValue(':artistId', $artistId, PDO::PARAM_INT);

		$this->db->exec($statement);
		$id = $this->db->getLastId('tracks');
		if ($id === false)
		{
			throw new RuntimeException('Could not add track');
		}

		$track->id = (int)$id;

		return $track;
	}

	public function updateArtist(Artist $artist, int $id): Artist
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

		$artist->id = $id;

		return $artist;
	}

	public function updateTrack(Track $track, int $id): Track
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

		return $this->getTrack($id);
	}

	public function getArtist(string $outerId): ?Artist
	{
		$statement = $this->db->prepare('SELECT id, outer_id, title, favorite_count, audition_count, album_count FROM artists WHERE outer_id = :outerId');
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

	public function getTrackByOuterId(string $outerId): ?Track
	{
		$statement = $this->db->prepare('SELECT id, title, duration, artist_id, outer_id FROM tracks WHERE outer_id = :outerId');
		$statement->bindValue(':outerId', $outerId);
		$result = $this->db->fetch($statement);
		if ($result === false)
		{
			return null;
		}

		return $this->fillTrack($result);
	}

	public function getTrack(int $id): ?Track
	{
		$statement = $this->db->prepare('SELECT id, title, duration, artist_id, outer_id FROM tracks WHERE id = :id');
		$statement->bindValue(':id', $id);
		$result = $this->db->fetch($statement);
		if (!is_array($result))
		{
			return null;
		}

		return $this->fillTrack($result);
	}

	private function fillTrack(array $result): Track
	{
		$track = new Track();

		$track->id = $result['id'];
		$track->title = $result['title'];
		$track->duration = $result['duration'];
		$track->artistId = $result['artist_id'];
		$track->outerId = $result['outer_id'];

		return $track;
	}
}