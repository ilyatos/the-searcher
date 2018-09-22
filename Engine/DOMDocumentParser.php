<?php

namespace Engine;

use DOMDocument;

class DOMDocumentParser {
    private $doc;

    public function __construct(string $url) {
        $options = [
            'http' => [
                'method' => "GET",
                'header' => "User-Agent: searcherBot/0.1\n"
            ],
        ];

        $context = stream_context_create($options);

        $this->doc = new DOMDocument();
        $html = file_get_contents($url, false, $context);
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
            return null;
        }

        return str_replace("\n", "", $elements->item(0)->nodeValue);
    }

    public function getMetaTags() {
        return $this->doc->getElementsByTagName('meta');
    }
}