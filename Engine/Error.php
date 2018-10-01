<?php

namespace Engine;

class Error {

    /**
     * Exception handler
     *
     * @param \Exception $exception The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception) {
        $log = __DIR__ . '/logs/' . date('d-m-Y') . '.txt';

        ini_set('error_log', $log);

        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= "Message: '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " .
            $exception->getLine() . "\n";

        error_log($message);
    }
}