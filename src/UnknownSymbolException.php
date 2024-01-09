<?php


namespace DPRMC;


class UnknownSymbolException extends \Exception {

    public string $unknownSymbol;

    public function __construct( string $message = "", int $code = 0, ?\Throwable $previous = NULL, string $unknownSymbol = null ) {
        parent::__construct( $message, $code, $previous );

        $this->unknownSymbol = $unknownSymbol;
    }
}