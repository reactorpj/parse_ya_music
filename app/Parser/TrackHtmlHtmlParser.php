<?php

namespace App\Parser;

use App\Entity\Track;
use App\Entity\TrackCollection;

class TrackHtmlHtmlParser extends HtmlParser
{
	public function getTracks(int $artistId): TrackCollection
	{
		$data = [];

		$tracksNodeList = $this->xpath->query('//*[contains(@class, "page-artist__tracks")]');
		foreach ($tracksNodeList as $itemNode)
		{
			$sectionTitle = $itemNode->previousSibling?->firstElementChild?->nodeValue;
			if ($sectionTitle === "Треки")
			{
				$tracksListNode = $this->xpath->query('//*[@class="lightlist__cont"]/*[@data-item-id]', $itemNode);
				if ($tracksListNode === null || $tracksListNode->length === 0)
				{
					return new TrackCollection();
				}

				foreach ($tracksListNode as $trackNode) {
					$track = new Track();

					$track->outerId = trim($trackNode->attributes->getNamedItem("data-item-id")->nodeValue);
					$track->title = trim($this->xpath->query('descendant::*[contains(@class, "d-track__title")]', $trackNode)->item(0)->nodeValue);
					$track->duration = $this->parseDuration($this->xpath->query('descendant::*[contains(@class, "d-track__info")]/descendant::*[contains(@class, "typo-track")]', $trackNode)->item(0)->nodeValue);
					$track->artistId = $artistId;

					$data[] = $track;
				}

			}
		}

		return new TrackCollection($data);
	}

	private function parseDuration(?string $nodeValue): float|int
	{
		$result = 0;
		if (preg_match("/^\d+:\d{2}$/i", $nodeValue))
		{
			$chunks = explode(":", $nodeValue);
			if (count($chunks) !== 2)
			{
				return $result;
			}

			$result = (int)$chunks[0] * 60;
			$result += (int)$chunks[1];
		}

		return $result;
	}
}