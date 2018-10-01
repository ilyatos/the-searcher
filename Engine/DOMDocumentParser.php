<?php

namespace Engine;

use DOMDocument;
use Engine\Exceptions\TitleNotFoundException;

class DOMDocumentParser {
    private $doc;
    private $url;

    public function __construct(string $html, string $url) {
        $this->url = $url;
        $this->doc = new DOMDocument();
        @$this->doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    }

    public function getLinks() {
        return $this->doc->getElementsByTagName('a');
    }

    public function getImages() {
        return $this->doc->getElementsByTagName('img');
    }

    public function getSeoData() {
        $title = $this->getTitle();


        $description = '';
        $keywords = '';

        $metaArray = $this->getMetaTags();

        /** @var \DOMElement $meta */
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

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords
        ];
    }

    public function getTitle() {
        $elements = $this->doc->getElementsByTagName('title');

        if (sizeof($elements) == 0 || $elements->item(0) == null) {
            throw new TitleNotFoundException($this->url);
        }

        return str_replace("\n", "", $elements->item(0)->nodeValue);
    }

    public function getMetaTags() {
        return $this->doc->getElementsByTagName('meta');
    }
}