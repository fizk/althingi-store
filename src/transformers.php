<?php

namespace App;

use MongoDB\BSON\UTCDateTime;
use DateTime;

function serializeDatesRange($range): array {
    return [
        'from' => $range['from']
            ? new UTCDateTime((new DateTime($range['from']))->getTimestamp() * 1000)
            : null,
        'to' => $range['to']
            ? new UTCDateTime((new DateTime($range['to']))->getTimestamp() * 1000)
            : null,
    ];
}

function deserializeDatesRange($range): array {
    return [
        'from' => $range['from']
            ? $range['from']->toDateTime()->format('c')
            : null,
        'to' => $range['to']
            ? $range['to']->toDateTime()->format('c')
            : null,
    ];
}

function serializeBirth($date): array {
    return [
        'birth' => $date['birth']
            ? new UTCDateTime((new DateTime($date['birth']))->getTimestamp() * 1000)
            : null,
    ];
}

function deserializeBirth($date): array {
    return [
        'birth' => $date['birth']
            ? $date['birth']->toDateTime()->format('c')
            : null,
    ];
}

function serializeDate($date): array {
    return [
        'date' => $date['date']
            ? new UTCDateTime((new DateTime($date['date']))->getTimestamp() * 1000)
            : null,
    ];
}

function deserializeDate($date): array {
    return [
        'date' => $date['date']
            ? $date['date']->toDateTime()->format('c')
            : null,
    ];
}

function serializeAssembly($assembly): array
{
    return [
        ...$assembly,
        ...serializeDatesRange($assembly),
    ];
}
