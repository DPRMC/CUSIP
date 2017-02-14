<?php
use PHPUnit\Framework\TestCase;
use DPRMC\CUSIP;

class CUSIPTest extends TestCase {

    public function testEmptyInputString() {
        $isCusip = CUSIP::isCUSIP('');
        $this->assertFalse($isCusip);
    }
}