<?php

namespace App\Http;

class Client
{
	public function getContent(string $link): string|false
	{
		$opts = array(
			'http'=>array(
				'method' => "GET",
				'header' => "Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n",
				'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0',
			)
		);

		$context = stream_context_create($opts);

		return file_get_contents($link, false, $context);
	}
}