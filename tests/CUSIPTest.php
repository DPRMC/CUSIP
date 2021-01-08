<?php

use PHPUnit\Framework\TestCase;
use DPRMC\CUSIP;

class CUSIPTest extends TestCase {

    /**
     * @test
     */
    public function testEmptyInputString() {
        $isCusip = CUSIP::isCUSIP( '' );
        $this->assertFalse( $isCusip );
    }

    /**
     * @test
     */
    public function testInvalidInputString() {
        $isCusip = CUSIP::isCUSIP( 'notValidCusip' );
        $this->assertFalse( $isCusip );
    }



    /**
     * @test
     */
    public function testInvalidInputStringWithBadChars() {
        $isCusip = CUSIP::isCUSIP( '12345678-' );
        $this->assertFalse( $isCusip );
    }

    /**
     * @test
     */
    public function testValidInputString() {
        $isCusip = CUSIP::isCUSIP( '222386AA2' );
        $this->assertTrue( $isCusip );
    }

    /**
     * @test
     */
    public function testValidInputStringWithWhitespace() {
        $isCusip = CUSIP::isCUSIP( ' 222386AA2 ' );
        $this->assertTrue( $isCusip );
    }

    /**
     * @test
     */
    public function testInputStringWithNewLines() {
        $string = "3137A96Y7
3136A45X3
31397NCJ2
31397JYY4";

        $validCusips = CUSIP::getValidCusipsFromString( $string );
        $this->assertTrue( count( $validCusips ) == 4 );
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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
     * @test
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

    /**
     * @test
     */
    public function testIsCusipWithStar() {
        $cusip   = '00800*AA0';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertTrue( $isCusip );
    }

    /**
     * @test
     */
    public function testIsCusipWithAmpersand() {
        $cusip   = '00800@AA8';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertTrue( $isCusip );
    }

    /**
     * @test
     */
    public function testIsCusipWithHash() {
        $cusip   = '00800#AA6';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertTrue( $isCusip );
    }

    /**
     * @test
     */
    public function testIsCusipWithInvalidCheckDigit() {
        $cusip   = '31397JYY5';
        $isCusip = CUSIP::isCUSIP( $cusip );
        $this->assertFalse( $isCusip );
    }

    /**
     * @test
     */
    public function testGetValidCusipsFromStringWithPregSplitFailure() {
        $fileResourceInsteadOfString = fopen( "./LICENSE", "r" );
        $validCusips                 = CUSIP::getValidCusipsFromString( $fileResourceInsteadOfString );
        $this->assertEquals( 0, count( $validCusips ) );
    }


    /**
     * @test
     */
    public function cusipOfInvalidLengthShouldReturnFalse() {
        $cusip         = '12345678987654321'; // Definitely longer than a CUSIP should be.
        $shouldBeFalse = CUSIP::getChecksumDigit( $cusip );
        $this->assertFalse( $shouldBeFalse );
    }
}