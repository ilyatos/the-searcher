<?php


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
        @$this->doc->loadHTML(file_get_contents($url, false, $context));
    }

    public function getLinks() {
        return $this->doc->getElementsByTagName('a');
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

    public function getImages() {
        return $this->doc->getElementsByTagName('img');
    }

}