<?php


namespace DPRMC;

/**
 * Class CUSIP
 *
 * @package DPRMC
 */
class CUSIP {


    /**
     * A CUSIP may only contain characters that pass this regex.
     */
    const REGEX_PATTERN = "/[\dA-Z@\*\#]{9}/";

    const SEDOL_WEIGHT = [ 1, 3, 1, 7, 3, 9, 1 ];

    /**
     * @param $string
     *
     * @return array
     */
    public static function getUniqueValidCusipsFromString( $string ) {
        $cusips = self::getValidCusipsFromString( $string );
        $cusips = self::removeInvalidCusips( $cusips );
        $cusips = array_unique( $cusips );
        $cusips = array_values( $cusips );

        return $cusips;
    }

    /**
     * Often times a Controller will accept an input string, that is a big list of CUSIPS
     * that were copy/pasted in. Instead of munging that data in the controller, let's put
     * it in our handy CUSIP package.
     * In the interest of testing, I suppress the error from preg_split and explicitly test for failure.
     * This way I can be sure that my removeInvalidCusips function will receive an array.
     * FYI, you can make preg_split fail by passing it a file resource (fopen()) instead of a string.
     *
     * @param string $string A string that might contain some cusips. In most of our use cases, it will be one CUSIP
     *                       per line.
     *
     * @return array
     */
    public static function getValidCusipsFromString( string $string ): array {
        $cusips = @preg_split( "/[\s,]+/", $string, -1, PREG_SPLIT_NO_EMPTY );
        if ( $cusips === FALSE ) {
            return [];
        }

        return self::removeInvalidCusips( $cusips );
    }

    /**
     * @param array $cusips
     *
     * @return array The input array, but with invalid CUSIPs removed.
     */
    public static function removeInvalidCusips( $cusips ) {
        $validCusips = [];
        foreach ( $cusips as $cusip ):
            if ( CUSIP::isCUSIP( $cusip ) ):
                $validCusips[] = $cusip;
            endif;
        endforeach;

        return $validCusips;
    }

    /**
     * Determines if the given CUSIP is valid according to its checksum digit.
     * Below is the wikipedia link to the pseudocode that this function is based on.
     * https://en.wikipedia.org/wiki/CUSIP#Check_digit_pseudocode
     *
     * @param string|null $cusip The string that you want to determine is a valid CUSIP or not.
     *
     * @return bool
     */
    public static function isCUSIP( $cusip = NULL ) {

        if ( empty( $cusip ) ):
            return FALSE;
        endif;

        // Trim any whitespace from the input string.
        $cusip = trim( $cusip );

        // A CUSIP is always 9 characters long.
        if ( strlen( $cusip ) != 9 ) {
            return FALSE;
        }

        if ( TRUE === self::containsInvalidCharacters( $cusip ) ) {
            return FALSE;
        }

        $checksumDigit = CUSIP::getChecksumDigit( $cusip );
        // If the last character of the cusip is equal to the checksum digit, then it validates.
        if ( substr( $cusip,
                     -1 ) == $checksumDigit
        ):
            return TRUE;
        endif;

        return FALSE;
    }


    public static function containsInvalidCharacters( $cusip ) {
        $cusip = strtoupper( $cusip );
        if ( 1 === preg_match( self::REGEX_PATTERN, $cusip ) ) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param $cusip
     *
     * @return bool|int
     */
    public static function getChecksumDigit( $cusip ) {
        // Trim any whitespace from the input string.
        $cusip = trim( $cusip );

        // A CUSIP is always 9 characters long.
        if ( strlen( $cusip ) < 8 || strlen( $cusip ) > 9 ) {
            return FALSE;
        }
        // The $sum is the running tally of values that gets some math performed on it at the
        // end that converts it into the checksum digit.
        $sum = 0;
        // Split the CUSIP into an array. One character per array element, for easy looping.
        $chars = str_split( $cusip,
                            1 );
        // Loop through the first 8 characters. (The 9th character is the checksum.
        for ( $i = 0; $i < 8; $i++ ) {
            $c = $chars[ $i ]; // Pull the next character from the array.
            // The value ($v) is the numeric value for this character.
            // The value depends on what type of character it is. The if/else statements below handle the cases.
            $v = NULL;
            // If the character is a digit, then we just take the integer value of it.
            if ( ctype_digit( $c ) ):
                $v = (int)$c;
            // The next 3 elseif's are special cases.
            // The characters * @ # are all valid in a CUSIP and are given the values 36,37, and 38 respectively when validating.
            elseif ( $c == '*' ):
                $v = 36;
            elseif ( $c == '@' ):
                $v = 37;
            elseif ( $c == '#' ):
                $v = 38;
            // If the character is a letter, we force it to lowercase, so we can be sure we get a consistent value
            // from the ord() function. Example: a and A have different values according to the ord() function.
            elseif ( ctype_alpha( $c ) ):
                $c        = strtolower( $c );
                $ord      = ord( $c );
                $position = $ord - 96;
                $v        = $position + 9; // S&P encodes A == 10, and so on.
            endif;
            // Of the 8 characters we are checking, if the character being checked right now is
            // in an odd position, then we are supposed to double it's value. Example: 6 becomes 12
            if ( ( $i % 2 ) != 0 ):
                $v *= 2;
            endif;
            // If the value ($v) is 2 digits long, then add them together. So 12 would be 1 + 2 to give you 3.
            // Then add that to the sum.
            $vDiv10 = floor( $v / 10 );
            $vMod10 = ( $v % 10 );
            $sum    += $vDiv10 + $vMod10;
        }
        // $sum = $sum + (int)( $v / 10 ) + $v % 10
        // I split it out, so people don't have to second guess the order of operations.
        $sumMod10         = $sum % 10;
        $tenMinusSumMod10 = 10 - $sumMod10;

        return $tenMinusSumMod10 % 10; // Return the checksum digit
    }


    /**
     * @param $sedol
     *
     * @return string
     */
    public static function isSEDOL( $sedol ) {
        $numRange   = range( 0, 9 );
        $alphaRange = range( 'A', 'Z' );
        $range      = array_merge( $numRange, $alphaRange );
        $charValues = array_flip( $range );

        $sedol = strtoupper( $sedol );
        $chars = str_split( $sedol );

        $sum = 0;
        foreach ( $chars as $i => $char ):
            // Vowels will never be in a SEDOL
            if ( stripos( 'AEIOU', $char ) ):
                return FALSE;
            endif;
            $sum += $charValues[ $char ] * self::SEDOL_WEIGHT[ $i ];
        endforeach;

        return 0 == $sum % 10;
    }


    /**
     * @param $isin
     *
     * @return bool
     * @see       https://github.com/pear/Validate_Finance/blob/master/Validate/Finance/ISIN.php
     * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
     */
    public static function isISIN( $isin ) {
        if ( ! preg_match( '/^[A-Z]{2}[A-Z0-9]{9}[0-9]$/i', $isin ) ):
            return FALSE;
        endif;

        // Convert letters to numbers.
        $base10 = '';
        for ( $i = 0; $i <= 11; $i++ ):
            $base10 .= base_convert( $isin[ $i ], 36, 10 );
        endfor;

        // Calculate double-add-double checksum.
        $checksum = 0;
        $len      = strlen( $base10 ) - 1;
        $parity   = $len % 2;
        // Iterate over every digit, starting with the rightmost (=check digit).
        for ( $i = $len; $i >= 0; $i-- ):
            // Multiply every other digit by two.
            $weighted = $base10[ $i ] << ( ( $i - $parity ) & 1 );
            // Sum up the weighted digit's digit sum.
            $checksum += $weighted % 10 + (int)( $weighted / 10 );
        endfor;

        return ! (bool)( $checksum % 10 );
    }


    /**
     * @param string $originalCUSIP
     * @return string
     * @throws UnfixableCusipException
     */
    public static function fixCusip( string $originalCUSIP ): string {

        $cleanOriginalCUSIP = $originalCUSIP;
        $cleanOriginalCUSIP = trim($cleanOriginalCUSIP);
        $cleanOriginalCUSIP = strtoupper($cleanOriginalCUSIP);


        if ( self::isCUSIP( $cleanOriginalCUSIP ) ):
            return $cleanOriginalCUSIP;
        endif;

        $fixedCUSIP = self::_replaceIO( $cleanOriginalCUSIP );

        if ( self::isCUSIP( $fixedCUSIP ) ):
            return $fixedCUSIP;
        endif;

        throw new UnfixableCusipException( "I am unable to fix this CUSIP to make it valid.", 0, NULL, $originalCUSIP );
    }


    /**
     * "To avoid confusion, the letters I and O are not used since they might be mistaken for the digits 1 and 0."
     * @url https://en.wikipedia.org/wiki/CUSIP#:~:text=A%20CUSIP%20(%2F%CB%88kj,clearing%20and%20settlement%20of%20trades.
     * @param string $CUSIP
     * @return string
     */
    protected static function _replaceIO( string $CUSIP ): string {
        $CUSIP = str_replace( 'O', 0, $CUSIP );
        $CUSIP = str_replace( 'I', 1, $CUSIP );
        return $CUSIP;
    }


}