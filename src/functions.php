<?php

namespace App;

use ErrorException;

function exception_error_handler($severity, $message, $file, $line)
{
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

function read_resource(/*resource*/$resource): string
{
    $result = '';
    while ($data = fread($resource, 1024)) $result .= $data;
    fclose($resource);

    return $result;
}