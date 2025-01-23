<?php

require_once "vendor/autoload.php";

$links = [
	'https://music.yandex.ru/artist/36800/albums',
//	'https://music.yandex.ru/artist/4034285/tracks',
//	'https://music.yandex.ru/artist/1813/tracks',
//	'https://music.yandex.ru/artist/41114',
//	'https://music.yandex.ru/artist/41098',
];

foreach ($links as $link) {
	try {
		(new \App\YandexMusicParser($link))->execute();
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
}


exit();
//die();