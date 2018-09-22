<?php

namespace Engine;

use Engine\Models\Image;
use Engine\Models\Site;

class CrawlerHelper {
    public $url;

    public function __construct(string $url) {
        $this->url = $url;
    }

    public function createLink(string $src): string {
        $scheme = parse_url($this->url)['scheme']; //http
        $host = parse_url($this->url)['host']; // www.adad.com

        if (substr($src, 0, 2) === '//') {
            $src = $scheme . ':' . $src;
        } elseif (substr($src, 0, 1) === '/') {
            $src = $scheme . '://' . $host . $src;
        } elseif (substr($src, 0, 2) === './') {
            $path = dirname(parse_url($this->url)['path']);
            $src = $scheme . '://' . $host . $path . substr($src, 1);
        } elseif (substr($src, 0, 3) === '../') {
            $src = $scheme . '://' . $host . '/' . $src;
        } elseif (substr($src, 0, 5) !== 'https' && substr($src, 0, 4) !== 'http') {
            $src = $scheme . '://' . $host . '/' . $src;
        }

        if (substr($src, -1) === '/') {
            $src = rtrim($src, '/');
        }

        return $src;
    }

    public function saveSeoData($data) {
        /**
         * Getting from extract().
         *
         * @var $title
         * @var $description
         * @var $keywords
         */
        extract($data, EXTR_SKIP);

        if ($title === null) {
            return;
        }

        Site::updateSeoForUrl($this->url, $title, $description, $keywords);
    }

    public function saveRawLinks($links) {
        /** @var \DOMElement $link */
        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            if (strpos($href, '#') !== false) {
                continue;
            }

            if (substr($href, 0, 11) === 'javascript:') {
                continue;
            }

            $href = $this->createLink($href);

            if (Site::exists($href)) {
                continue;
            }

            Site::insertUrl($href);
        }
    }

    public function saveImages($images) {
        /** @var \DOMElement $image */
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            $alt = $image->getAttribute('alt');
            $title = $image->getAttribute('title');

            if (!$alt && !$title) {
                continue;
            }

            $src = $this->createLink($src);

            if (Image::exists($src)) {
                continue;
            }

            Image::insert($this->url, $src, $alt, $title);
        }
    }

}