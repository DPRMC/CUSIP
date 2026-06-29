<?php


namespace DPRMC;


class UnfixableCusipException extends \Exception {

    public string $unfixableCusip;

    public function __construct( string $message = "", int $code = 0, ?\Throwable $previous = NULL, string $unfixableCusip = null ) {
        parent::__construct( $message, $code, $previous );

        $this->unfixableCusip = $unfixableCusip;
    }
}