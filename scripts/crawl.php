<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Engine\CrawlerHelper;
use Engine\DOMDocumentParser;
use Engine\Models\Site;

function followLinks(string $url) {
    $helper = new CrawlerHelper($url);
    $parser = new DOMDocumentParser($url);

    $links = $parser->getLinks();
    $helper->saveRawLinks($links);

    $images = $parser->getImages();
    $helper->saveImages($images);

    $seo = $parser->getSeoData();
    $helper->saveSeoData($seo);

    unset($helper, $parser);
}


$url = 'https://dogsecrets.ru';

echo 'Crawling...' . PHP_EOL;
$t = microtime(true);

while (true) {
    if ($result = Site::firstWhithoutSeo()) {
        $url = $result->url;
    }
    followLinks($url);
}

echo 'It TAKES ' . (microtime(true) - $t) . PHP_EOL;