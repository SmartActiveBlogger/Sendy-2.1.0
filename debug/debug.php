<?php
/**
 * Created by PhpStorm.
 * User: Alan Urquhart
 * Company: ASU IT & Design
 * Web: www.asuweb.co.uk
 * Date: 12/07/2016
 * Time: 11:58
 */
function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}