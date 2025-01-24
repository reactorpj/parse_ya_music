<?php

namespace App;

use App\Db\Repository;
use App\Entity\Track;
use App\Http\YaMusicClient;
use App\Parser\ArtistHtmlHtmlParser;
use App\Parser\TrackHtmlHtmlParser;
use App\Parser\TrackJsonParser;
use Exception;

require "Config/Init.php";

class YandexMusicParser
{
	private YaMusicClient $client;
	private Repository $repo;
	private string $artistId;
	private ArtistHtmlHtmlParser $artistParser;
	private TrackHtmlHtmlParser $tracksParser;
	private TrackJsonParser $jsonTrackParser;

	/**
	 * @throws Exception
	 */
	public function __construct(string $link)
	{
		$this->artistId = $this->getArtistId($link);
		$this->client = new YaMusicClient($this->artistId);
		$this->repo = new Repository();
		$this->artistParser = new ArtistHtmlHtmlParser();
		$this->tracksParser = new TrackHtmlHtmlParser();
		$this->jsonTrackParser = new TrackJsonParser();
	}

	public function execute(): void
	{
		$tracksContent = $this->client->getTracksContent();
		$artistContent = $this->client->getArtistContent();

		$this->artistParser->parse($artistContent);
		$artist = $this->artistParser->getArtist();
		$artist->outerId = $this->artistId;

		$artist = $this->saveArtist($artist);

		$this->tracksParser->parse($tracksContent);
		$tracks = $this->tracksParser->getTracks($artist->id);

		$this->saveTracks($tracks, $artist->id);

		if ($tracks->count() === 100)
		{
			$this->reloadTracks($artist);
		}
	}

	/**
	 * @throws Exception
	 */
	private function getArtistId(string $link): string
	{
		$path = parse_url($link, PHP_URL_PATH);
		if ($path === false)
		{
			throw new Exception('You should pass link from Yandex Music like /artist/1111111');
		}

		$path_chunks = explode('/', trim($path, '/'));

		if (
			is_array($path_chunks)
			&& count($path_chunks) >= 2
		)
		{
			return $path_chunks[1];
		}

		throw new Exception('You should pass link from Yandex Music like /artist/1111111');
	}

	/**
	 * @param Entity\Artist $artist
	 * @return void
	 */
	public function reloadTracks(Entity\Artist $artist): void
	{
		$page = 1;

		do {
			$result = $this->client->getTracks($page);
			if (
				!is_array($result)
				|| !isset($result['tracks'])
			)
			{
				return;
			}

			$collection = $this->jsonTrackParser->parseTracks($result['tracks'], $artist->id);
			$this->saveTracks($collection, $artist->id);

			$page++;
		} while ($collection->count() < 100 && $collection->count() > 0);
	}

	private function saveArtist(Entity\Artist $artist): Entity\Artist
	{
		if ($existsArtist = $this->repo->getArtist($artist->outerId))
		{
			return $this->repo->updateArtist($artist, $existsArtist->id);
		}

		return $this->repo->addArtist($artist);
	}

	private function saveTracks(Entity\TrackCollection $tracks, int $artistId): void
	{
		/** @var Track $track */
		foreach ($tracks->toArray() as $track)
		{
			if ($existsTrack = $this->repo->getTrackByOuterId($track->outerId))
			{
				$this->repo->updateTrack($track, $existsTrack->id);

				continue;
			}

			$this->repo->addTrack($track, $artistId);
		}
	}
}