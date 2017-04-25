<?php


namespace DPRMC;

/**
 * Class CUSIP
 * @package DPRMC
 */
class CUSIP {

    /**
     * Determines if the given CUSIP is valid according to its checksum digit.
     * Below is the wikipedia link to the pseudocode that this function is based on.
     * https://en.wikipedia.org/wiki/CUSIP#Check_digit_pseudocode
     * @param string $cusip The string that you want to determine is a valid CUSIP or not.
     * @return bool
     */
    public static function isCUSIP($cusip) {

        // Trim any whitespace from the input string.
        $cusip = trim($cusip);

        // A CUSIP is always 9 characters long.
        if (strlen($cusip) != 9) {
            return false;
        }
        // The $sum is the running tally of values that gets some math performed on it at the
        // end that converts it into the checksum digit.
        $sum = 0;
        // Split the CUSIP into an array. One character per array element, for easy looping.
        $chars = str_split($cusip,
                           1);
        // Loop through the first 8 characters. (The 9th character is the checksum.
        for ($i = 0; $i < 8; $i++) {
            $c = $chars[$i]; // Pull the next character from the array.
            // The value ($v) is the numeric value for this character.
            // The value depends on what type of character it is. The if/else statements below handle the cases.
            $v = null;
            // If the character is a digit, then we just take the integer value of it.
            if (ctype_digit($c)):
                $v = (int)$c;
            // The next 3 elseif's are special cases.
            // The characters * @ # are all valid in a CUSIP and are given the values 36,37, and 38 respectively when validating.
            elseif ($c == '*'):
                $v = 36;
            elseif ($c == '@'):
                $v = 37;
            elseif ($c == '#'):
                $v = 38;
            // If the character is a letter, we force it to lowercase, so we can be sure we get a consistent value
            // from the ord() function. Example: a and A have different values according to the ord() function.
            elseif (ctype_alpha($c)):
                $c = strtolower($c);
                $ord = ord($c);
                $position = $ord - 96;
                $v = $position + 9; // S&P encodes A == 10, and so on.
            endif;
            // Of the 8 characters we are checking, if the character being checked right now is
            // in an odd position, then we are supposed to double it's value. Example: 6 becomes 12
            if (($i % 2) != 0):
                $v *= 2;
            endif;
            // If the value ($v) is 2 digits long, then add them together. So 12 would be 1 + 2 to give you 3.
            // Then add that to the sum.
            $vDiv10 = floor($v / 10);
            $vMod10 = ($v % 10);
            $sum += $vDiv10 + $vMod10;
        }
        // $sum = $sum + (int)( $v / 10 ) + $v % 10
        // I split it out, so people don't have to second guess the order of operations.
        $sumMod10 = $sum % 10;
        $tenMinusSumMod10 = 10 - $sumMod10;
        $checksumDigit = $tenMinusSumMod10 % 10;
        // If the last character of the cusip is equal to the checksum digit, then it validates.
        if (substr($cusip,
                   -1) == $checksumDigit
        ):
            return true;
        endif;
        return false;
    }


    /**
     * @param array $cusips
     * @return array The input array, but with invalid CUSIPs removed.
     */
    public static function removeInvalidCusips($cusips) {
        $validCusips = [];
        foreach ($cusips as $cusip):
            if (CUSIP::isCUSIP($cusip)):
                $validCusips[] = $cusip;
            endif;
        endforeach;
        return $validCusips;
    }

    /**
     * Often times a Controller will accept an input string, that is a big list of CUSIPS
     * that were copy/pasted in. Instead of munging that data in the controller, let's put
     * it in our handy CUSIP package.
     * @param string $string A string that might contain some cusips. In most of our use cases, it will be one CUSIP per line.
     * @return array
     */
    public static function getValidCusipsFromString($string) {
        $cusips = preg_split("/[\s,]+/", $string, PREG_SPLIT_NO_EMPTY);
        if ($cusips === false) {
            return [];
        }
        return self::removeInvalidCusips($cusips);
    }
}