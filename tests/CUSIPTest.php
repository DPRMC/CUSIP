<?php
use PHPUnit\Framework\TestCase;
use DPRMC\CUSIP;

class CUSIPTest extends TestCase {

    public function testEmptyInputString() {
        $isCusip = CUSIP::isCUSIP('');
        $this->assertFalse($isCusip);
    }

    public function testInvalidInputString() {
        $isCusip = CUSIP::isCUSIP('notValidCusip');
        $this->assertFalse($isCusip);
    }

    public function testValidInputString() {
        $isCusip = CUSIP::isCUSIP('222386AA2');
        $this->assertTrue($isCusip);
    }

    public function testValidInputStringWithWhitespace() {
        $isCusip = CUSIP::isCUSIP(' 222386AA2 ');
        $this->assertTrue($isCusip);
    }

    public function testInputStringWithNewLines() {
        $cusips = "3137A96Y7
3136A45X3
31397NCJ2
31397JYY4";

        $validCusips = CUSIP::getValidCusipsFromString($cusips);
        $this->assertTrue(count($validCusips) == 4);
    }

    public function testInputStringWithNewLinesAndBlankLines() {
        $cusips = "
        3137A96Y7
3136A45X3

31397NCJ2

31397JYY4

";

        $validCusips = CUSIP::getValidCusipsFromString($cusips);
        $this->assertTrue(count($validCusips) == 4);
    }
}