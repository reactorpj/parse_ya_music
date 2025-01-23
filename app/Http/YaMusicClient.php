<?php

namespace App\Http;

use Exception;

class YaMusicClient
{
	private const ARTIST_PATH = '/artist/';
	private const TRACKS_PATH = '/handlers/artist.jsx';
	private const YA_MUSIC_PATH = 'https://music.yandex.ru';
	private const TRACKS_POSTFIX = '/tracks';
	private const ALBUMS_POSTFIX = '/albums';
	private const DEFAULT_PAGE_SIZE = 100;
	private Client $httpClient;

	public function __construct(private readonly string $artistId)
	{
		$this->httpClient = new Client();
	}

	public function getTracksContent(): ?string
	{
		$content = $this->httpClient->getContent($this->getTracksContentPath());
		if ($content === false) {
			return null;
		}

		return $content;
	}

	public function getTracks(int $page): ?array
	{
		$content = $this->httpClient->getContent($this->getTracksPath($page));
		if ($content === false)
		{
			return null;
		}

		try
		{
			return json_decode($content, true);
		}
		catch (Exception $e)
		{
			var_dump($e->getMessage());
		}

		return null;
	}

	public function getArtistContent(): ?string
	{
		$content = $this->httpClient->getContent($this->getAlbumsPath());
		if ($content === false) {
			return null;
		}

		return $content;
	}

	private function getTracksContentPath(): string
	{
		return self::YA_MUSIC_PATH . self::ARTIST_PATH . $this->artistId . self::TRACKS_POSTFIX;
	}

	private function getAlbumsPath(): string
	{
		return self::YA_MUSIC_PATH . self::ARTIST_PATH . $this->artistId . self::ALBUMS_POSTFIX;
	}

	private function getTracksPath(int $page): string
	{
		$data = [
			'artist' => $this->artistId,
			'what' => 'tracks',
			'trackPage' => $page,
			'trackPageSize' => self::DEFAULT_PAGE_SIZE,
			'lang' => 'ru',
			'external-domain' => 'music.yandex.ru',
		];

		$queryParams = http_build_query($data);

		return self::YA_MUSIC_PATH . self::TRACKS_PATH . '?' . $queryParams;
	}
}