<?php

namespace Engine\Exceptions;

use Exception;
use Throwable;

class TitleNotFoundException extends Exception {
    public function __construct(
        string $url,
        string $message = 'Title not found.',
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = trim($message);
        $message .= ' URL: ' . $url;

        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        parent::__toString();
    }
}