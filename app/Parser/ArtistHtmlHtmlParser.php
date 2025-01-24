<?php

namespace App\Parser;

use App\Entity\Artist;

class ArtistHtmlHtmlParser extends HtmlParser
{
	public function getArtist(): Artist
	{
		$artist = new Artist();

		$artist->title = $this->getTitle();
		$artist->auditionCount = $this->getAuditionCount();
		$artist->favoriteCount = $this->getFavoriteCount();
		$artist->albumCount = $this->getAlbumCount();

		return $artist;
	}

	private function getTitle(): ?string
	{
		$titleNode = $this->xpath->query('//*[contains(@class, "page-artist__title")]');
		if ($titleNode->length === 0)
		{
			return null;
		}

		return trim($titleNode->item(0)->nodeValue);
	}

	private function getAuditionCount(): int
	{
		$auditionNode = $this->xpath->query('//*[contains(@class, "d-generic-page-head")]/descendant::*[contains(@class, "page-artist__summary")]');
		if ($auditionNode->length === 0)
		{
			return 0;
		}

		return $this->parseNumber($auditionNode->item(0)->nodeValue);
	}

	private function getFavoriteCount(): int
	{
		$favoriteNode = $this->xpath->query('//*[contains(@class, "d-like")]/descendant::*[contains(@class, "d-button__label")]');
		if ($favoriteNode->length === 0)
		{
			return 0;
		}

		return $this->parseNumber($favoriteNode->item(0)->nodeValue);
	}

	private function getAlbumCount(): int
	{
		$albumArtistNodeList= $this->xpath->query('//*[contains(@class, "page-artist__albums")]');
		if ($albumArtistNodeList->length === 0)
		{
			return 0;
		}

		foreach ($albumArtistNodeList as $item) {
			$sectionTitle = $item->previousSibling->firstElementChild->nodeValue;
			if ($sectionTitle !== "Альбомы")
			{
				continue;
			}

			return $this->xpath->query('./descendant::*[@class="lightlist__cont"]/descendant::*[@data-id]', $item)?->length;
		}

		return 0;
	}

	private function parseNumber(?string $value = ''): ?string
	{
		return preg_replace("/[^0-9]/", "", $value);
	}
}