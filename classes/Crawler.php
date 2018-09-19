<?php

include 'DOMDocumentParser.php';

class Crawler {
    //Корень для индексации сайтов
    public $url = null;

    public function __construct(string $url) {
        $this->url = $url;
    }



}