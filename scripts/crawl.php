<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Engine\CrawlerHelper;
use Engine\DOMDocumentParser;
use Engine\Models\Site;
use GuzzleHttp\Client;

$headers = [
    "User-Agent: searcherBot/1.0\n"
];

function followLinks(string $url) {
    global $headers;

    $client = new Client([
        'defaults' => [
            'headers' => $headers,
        ]
    ]);

    try {
        $html = $client->request('GET', $url)->getBody()->getContents();
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        Site::updateWhere(['broken' => 1], 'url', $url);
        echo $e->getMessage();
        return;
    }

    $parser = new DOMDocumentParser($html, $url);
    $helper = new CrawlerHelper($url);

    $links = $parser->getLinks();
    $helper->saveRawLinks($links);

    $images = $parser->getImages();
    $helper->saveImages($images);

    $seo = $parser->getSeoData();
    $helper->saveSeoData($seo);

    unset($client, $parser, $helper);
}


$url = 'https://www.dogsecrets.ru';

echo 'Crawling...' . PHP_EOL;
$t = microtime(true);

while (true) {
    if ($result = Site::firstWhithoutSeo()) {
        $url = $result->url;
    }
    followLinks($url);
}

echo 'It TAKES ' . (microtime(true) - $t) . PHP_EOL;