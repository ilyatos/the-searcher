<?php

include 'db_connetcion.php';
include 'classes/DOMDocumentParser.php';


$alreadyCrawled = [];
$crawling = [];
$alreadyFoundImages = [];

function linkExists(string $url) : bool {
    global $connection;

    $query = $connection->prepare("SELECT * FROM sites WHERE url = :u");

    $query->bindParam(':u', $url);
    $query->execute();

    return $query->rowCount() != 0;
}

function insertLink(string $url, string $title, string $description, string $keywords) : bool {
    global $connection;

    $query = $connection->prepare("INSERT INTO sites(url, title, description, keywords) 
                                             VALUES (:u,:t,:d,:k)");

    $query->bindParam(':u', $url);
    $query->bindParam(':t', $title);
    $query->bindParam(':d', $description);
    $query->bindParam(':k', $keywords);

    return $query->execute();
}

function imageExists(string $src) : bool {
    global $connection;

    $query = $connection->prepare("SELECT * FROM images WHERE imageUrl = :s");

    $query->bindParam(':s', $src);
    $query->execute();

    return $query->rowCount() != 0;
}

function insertImage(string $url, string $src, string $alt, string $title) : bool {
    global $connection;

    $query = $connection->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title) 
                                             VALUES (:su,:si,:a,:t)");

    $query->bindParam(':su', $url);
    $query->bindParam(':si', $src);
    $query->bindParam(':a', $alt);
    $query->bindParam(':t', $title);

    return $query->execute();
}

function createLink (string $src, string $url) {
    $scheme = parse_url($url)['scheme']; //http
    $host = parse_url($url)['host']; // www.adad.com

    if (substr($src, 0,2) == '//') {
        $src = $scheme . ':' . $src;
    } elseif (substr($src, 0,1) == '/') {
        $src = $scheme . '://' . $host . $src;
    } elseif (substr($src, 0,2) == './') {
        $path = dirname(parse_url($url)['path']);
        $src = $scheme . '://' . $host . $path . substr($src, 1);
    } elseif (substr($src, 0,3) == '../') {
        $src = $scheme . '://' . $host . '/' . $src;
    } elseif (substr($src, 0,5) != 'https' && substr($src, 0,4) != 'http') {
        $src = $scheme . '://' . $host . '/' . $src;
    }

    return $src;
}

function getSeoData (string $url) {
    global $alreadyFoundImages;

    $parser = new DOMDocumentParser($url);

    $title = $parser->getTitle();

    if ($title == '' or is_null($title)) {
        return;
    }

    $description = '';
    $keywords = '';

    $metaArray = $parser->getMetaTags();

    /** @var DOMElement $meta */
    foreach ($metaArray as $meta) {
        if ($meta->getAttribute('name') == 'description') {
            $description = $meta->getAttribute('content');
            $description = str_replace("\n", "", $description);
        }

        if ($meta->getAttribute('name') == 'keywords') {
            $keywords = $meta->getAttribute('content');
            $keywords = str_replace("\n", "", $keywords);
        }
    }

    if (linkExists($url)) {
        echo "WARNING: $url already exists <br>";
    } elseif (insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url <br>";
    } else {
        echo "ERROR WHILE INSERTING: $url <br>";
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

        $src = createLink($src, $url);

        if (!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;

            if (!imageExists($src)) {
                insertImage($url, $src, $alt, $title);
            }
        }
    }

}

function followLinks(string $url) {
    global $alreadyCrawled, $crawling;

    $parser = new DOMDocumentParser($url);

    $linkList = $parser->getLinks();

    $i = 0;
    /** @var DOMElement $link */
    foreach ($linkList as $link) {
        $href = $link->getAttribute('href');

        if (strpos($href, '#') !== false) {
            continue;
        } elseif (substr($href, 0,11) === 'javascript:') {
            continue;
        }

        $href = createLink($href, $url);

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

$startUrl = "http://www.apple.com";

$t = microtime(true);
followLinks($startUrl);
echo 'It TAKES ' . (microtime(true) - $t);