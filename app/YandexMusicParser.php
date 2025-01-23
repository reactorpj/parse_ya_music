<?php

namespace App;

use App\Db\Repository;
use App\Http\YaMusicClient;
use App\Parser\ArtistHtmlHtmlParser;
use App\Parser\TrackHtmlHtmlParser;
use App\Parser\TrackJsonParser;
use Exception;


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

		$this->repo->addArtist($artist);

		$this->tracksParser->parse($tracksContent);
		$tracks = $this->tracksParser->getTracks();

		$this->repo->addTracks($tracks, $artist);
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

			$collection = $this->jsonTrackParser->parseTracks($result['tracks']);
			$this->repo->addTracks($collection, $artist);

			$page++;
		} while ($collection->count() < 100);
	}
}