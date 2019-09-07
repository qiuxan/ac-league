<?php
namespace App;

class Utils
{
    public static function debug( $value )
    {
        echo '<pre>';
        print_r( $value );
        echo '</pre>';
    }

    public static function trace( $value )
    {
        $handle = fopen( '/tmp/debug.log', 'a' );
        ob_start();
        print_r( $value );
        $contents = ob_get_clean();

        fwrite( $handle, $contents . "\n" );
        fclose( $handle );
    }

    public static function randString( $length = 10 )
    {
        //$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $string = '';
        for( $p = 0; $p < $length; $p++ )
        {
            $string .= $characters[mt_rand( 0, strlen( $characters ) - 1 )];
        }

        return $string;
    }
    /**
     * @param mixed $price
     * @return int
     */
    public static function dollarsToCents( $price )
    {
        $price = round( $price, 2 );
        $parts = explode( ".", $price );
        if( isset( $parts[1] ) && strlen( $parts[1] ) == 1 )
        {
            $parts[1] .= "0";
        }
        else if( !isset( $parts[1] ) )
        {
            $parts[] = '.00';
        }
        $centsString = str_replace( ".", "", implode( ".", $parts ) );
        $centsInt = (int)( $price * 100 );
        //Bring int up to string
        while( (string)$centsString > (string)$centsInt )
        {
            $centsInt++;
        }
        //Bring int down to string
        while( (string)$centsInt > (string)$centsString )
        {
            $centsInt--;
        }
        return $centsInt;
    }

    public static function CSVRowFromDataArray( array $row )
    {
        $csvData = array();
        foreach( $row as $data )
        {
            $csvData[] = '"' . $data . '"';
        }
        return implode( ",", $csvData ) . "\n";
    }

    public static function toMySQLDate( $dateString = '' )
    {
        if( !strlen( $dateString ) )
        {
            return null;
        }
        $dateArray = explode( "/", $dateString );
        $reversed = array_reverse( $dateArray );

        return implode( '-', $reversed );
    }

    public static function toMySQLDateTime($dateTimeString = '')
    {
        if(!strlen($dateTimeString))
        {
            return '';
        }

        if(date('Y-m-d H:i:s', strtotime($dateTimeString)) == $dateTimeString){
            return $dateTimeString;
        }

        $dateTimeArray = explode(" ", $dateTimeString);
        $dateArray = explode("/", $dateTimeArray[0]);
        $reversed = array_reverse($dateArray);

        if(isset($dateTimeArray[2]))
        {
            return implode('-', $reversed) . ' ' . date('H:i:s', strtotime($dateTimeArray[1] . ' ' . $dateTimeArray[2]));
        }
        else
        {
            return implode('-', $reversed) . ' ' . date('H:i:s', strtotime($dateTimeArray[1]));
        }
    }
}