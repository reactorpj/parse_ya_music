<?php

namespace App\Parser;

use DOMXPath;
use Throwable;
use Masterminds\HTML5;

abstract class HtmlParser
{
	protected DOMXPath $xpath;

	public function parse(string $content): void
	{
		try {
			$html5 = new HTML5();
			$dom = $html5->loadHTML($content);
			$this->xpath = new DOMXPath($dom);
		}
		catch (Throwable $e) {
			var_dump($e->getMessage());
		}
	}

}