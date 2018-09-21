<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Engine\CrawlerHelper;
use Engine\DOMDocumentParser;
use Engine\Models\Image;
use Engine\Models\Site;

$alreadyCrawled = [];
$crawling = [];
$alreadyFoundImages = [];
$helper = null;

function getSeoData (string $url) {
    global $alreadyFoundImages, $helper;

    $parser = new DOMDocumentParser($url);

    $title = $parser->getTitle();

    if ($title === '' or $title === null) {
        return;
    }

    $description = '';
    $keywords = '';

    $metaArray = $parser->getMetaTags();

    /** @var DOMElement $meta */
    foreach ($metaArray as $meta) {
        if ($meta->getAttribute('name') === 'description') {
            $description = $meta->getAttribute('content');
            $description = str_replace("\n", '', $description);
        }

        if ($meta->getAttribute('name') === 'keywords') {
            $keywords = $meta->getAttribute('content');
            $keywords = str_replace("\n", '', $keywords);
        }
    }

    if (Site::exists($url)) {
        echo "WARNING: $url already exists" . PHP_EOL;
    } elseif (Site::insert($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url" . PHP_EOL;
    } else {
        echo "ERROR WHILE INSERTING: $url" . PHP_EOL;
    }

    $imageArray = $parser->getImages();

    /** @var DOMElement $image */
    foreach ($imageArray as $image) {
        $src = $image->getAttribute('src');
        $alt = $image->getAttribute('alt');
        $title = $image->getAttribute('title');

        if (!$alt && !$title) {
            continue;
        }

        if ($helper === null) {
            $helper = new CrawlerHelper($url);
        }

        $src = $helper->createLink($src);

        if (!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;

            if (!Image::exists($src)) {
                Image::insert($url, $src, $alt, $title);
            }
        }
    }

}

function followLinks(string $url) {
    global $alreadyCrawled, $crawling, $helper;

    $parser = new DOMDocumentParser($url);

    $linkList = $parser->getLinks();

    /** @var DOMElement $link */
    foreach ($linkList as $link) {
        $href = $link->getAttribute('href');

        if (strpos($href, '#') !== false) {
            continue;
        }

        if (substr($href, 0, 11) === 'javascript:') {
            continue;
        }

        if ($helper === null) {
            $helper = new CrawlerHelper($url);
        }

        $href = $helper->createLink($href);

        if(!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href;
            $crawling[] = $href;

            //Insert href
        }

        getSeoData($href);
    }
    //array_reverse and array_pop
    array_shift($crawling);

    foreach ($crawling as $site) {
        followLinks($site);
        echo 'NEXT SITE' . PHP_EOL;
    }
}

$startUrl = 'http://www.bbc.com';
$t = microtime(true);
followLinks($startUrl);
die;
echo 'It TAKES ' . (microtime(true) - $t);