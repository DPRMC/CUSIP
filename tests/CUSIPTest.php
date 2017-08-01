<?php
use PHPUnit\Framework\TestCase;
use DPRMC\CUSIP;

class CUSIPTest extends TestCase {

    public function testEmptyInputString() {
        $isCusip = CUSIP::isCUSIP( '' );
        $this->assertFalse( $isCusip );
    }

    public function testInvalidInputString() {
        $isCusip = CUSIP::isCUSIP( 'notValidCusip' );
        $this->assertFalse( $isCusip );
    }

    public function testValidInputString() {
        $isCusip = CUSIP::isCUSIP( '222386AA2' );
        $this->assertTrue( $isCusip );
    }

    public function testValidInputStringWithWhitespace() {
        $isCusip = CUSIP::isCUSIP( ' 222386AA2 ' );
        $this->assertTrue( $isCusip );
    }

    public function testInputStringWithNewLines() {
        $string = "3137A96Y7
3136A45X3
31397NCJ2
31397JYY4";

        $validCusips = CUSIP::getValidCusipsFromString( $string );
        $this->assertTrue( count( $validCusips ) == 4 );
    }

    public function testInputStringWithNewLinesAndBlankLines() {
        $string = "
        3137A96Y7
3136A45X3

31397NCJ2

31397JYY4

";

        $validCusips = CUSIP::getValidCusipsFromString( $string );
        $this->assertTrue( count( $validCusips ) == 4 );
    }

    public function testInputStringWithNewLinesAndBlankLinesAndCommas() {
        $string = "
        3137A96Y7,
,3136A45X3

31397NCJ2,

,31397JYY4,,,

";

        $validCusips = CUSIP::getValidCusipsFromString( $string );
        $this->assertTrue( count( $validCusips ) == 4 );
    }

    /**
     *
     */
    public function testInputStringWithNewLinesAndBlankLinesAndCommasAndDuplicates() {
        $string      = "
        3137A96Y7,
,31397NCJ2

31397NCJ2,

,3137A96Y7,,,

";
        $validCusips = CUSIP::getUniqueValidCusipsFromString( $string );
        $this->assertTrue( count( $validCusips ) == 2 );
    }

    public function testIsCusipWithStar() {
        $cusip   = '00800*AA0';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertTrue( $isCusip );
    }

    public function testIsCusipWithAmpersand() {
        $cusip   = '00800@AA8';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertTrue( $isCusip );
    }

    public function testIsCusipWithHash() {
        $cusip   = '00800#AA6';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertTrue( $isCusip );
    }

    public function testIsCusipWithInvalidCheckDigit() {
        $cusip   = '31397JYY5';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertFalse( $isCusip );
    }

    /**
     *
     */
    public function testGetValidCusipsFromStringWithNoCusipsInIt() {
        $cusipString = "This\nis\nnot\na list of\nCUSIP\nidentifiers";
        $validCusips = CUSIP::getValidCusipsFromString( $cusipString );
        $this->assertCount( 0, $validCusips );
    }
}