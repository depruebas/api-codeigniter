<?php

if (! function_exists('debug'))
{

    function debug( $var, $die = false)
    {
        $debug = debug_backtrace();
        $data_log = [
            'file' => $debug[0]['file'],
            'line' => $debug[0]['line'],
            'data' => $var
        ];
        header('Content-Type: application/json');
        echo json_encode($data_log, JSON_PRETTY_PRINT);
        echo PHP_EOL;

        if ( $die) die;

    }

}