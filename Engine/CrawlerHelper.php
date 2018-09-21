<?php

namespace Engine;

class CrawlerHelper {
    //Корень для начала индексации сайтов
    public $url;

    public function __construct(string $url) {
        $this->url = $url;
    }

    public function createLink(string $src) {
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

        return $src;
    }

}