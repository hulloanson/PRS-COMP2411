<?php


class Util
{

    static function jsonToArray($json)
    {
        return json_decode($json);
    }

    static function guidv4()
    {
        $data = openssl_random_pseudo_bytes( 16 );
        $data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 ); // set version to 0100
        $data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 ); // set bits 6-7 to 10

        return vsprintf( '%s%s%s%s%s%s%s%s', str_split( bin2hex( $data ), 4 ) );
    }

    static function submitTypeToString($submitType)
    {
        $submitTypee = intval($submitType);
        switch ($submitTypee) {
            case 1:
                return "Abstract";
            case 2:
                return "Main Paper";
            case 3:
                return "Revised Paper";
            default:
                return "Type ID Error";
        }
    }

    static function boolToYesNo($bool)
    {
        $booll = boolval($bool);
        return ($booll == true ? 'Yes' : 'No');
    }

    function base64ToFile($base64String)
    {

    }
}