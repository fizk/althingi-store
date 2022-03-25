<?php

namespace App;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

function serializeDatesRange($range): array
{
    return [
        'from' => $range['from']
            ? new UTCDateTime((new DateTime($range['from']))->getTimestamp() * 1000)
            : null,
        'to' => $range['to']
            ? new UTCDateTime((new DateTime($range['to']))->getTimestamp() * 1000)
            : null,
    ];
}

function deserializeDatesRange(BSONDocument $range): array
{
    return [
        'from' => $range['from']
            ? $range['from']->toDateTime()->format('c')
            : null,
        'to' => $range['to']
            ? $range['to']->toDateTime()->format('c')
            : null,
    ];
}

function serializeBirth($date): array
{
    return [
        'birth' => $date['birth']
            ? new UTCDateTime((new DateTime($date['birth']))->getTimestamp() * 1000)
            : null,
    ];
}

function deserializeBirth(BSONDocument $date): array
{
    return [
        'birth' => $date['birth']
            ? $date['birth']->toDateTime()->format('c')
            : null,
    ];
}

function serializeDate($date): array
{
    return [
        'date' => $date['date']
            ? new UTCDateTime((new DateTime($date['date']))->getTimestamp() * 1000)
            : null,
    ];
}

function deserializeDate(BSONDocument $date): array
{
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

function deserializeAssembly(BSONDocument $assembly): array
{
    return [
        ...$assembly,
        ...deserializeDatesRange($assembly)
    ];
}

function serializeCongressman($congressman): array
{
    return [
        ...$congressman,
        ...serializeBirth($congressman),
    ];
}

function deserializeCongressman(BSONDocument $congressman): array
{
    return [
        ...$congressman,
        ...deserializeBirth($congressman),
    ];
}

function serializeCommittee($committee): array
{
    return [
        ...$committee,
    ];
}

function deserializeCommittee(BSONDocument $committee): array
{
    return [...$committee];
}

function serializeParty($party): array
{
    return [
        ...$party,
    ];
}

function deserializeParty(BSONDocument $party): array
{
    return [...$party];
}

function serializeConstituency($constituency): array
{
    return [
        ...$constituency,
    ];
}

function deserializeConstituency(BSONDocument $constituency): array
{
    return [...$constituency];
}

function serializeMinistry($ministry): array
{
    return [
        ...$ministry,
    ];
}

function deserializeMinistry(BSONDocument $ministry): array
{
    return [
        ...$ministry,
    ];
}

function serializeInflation($inflation): array
{
    return [
        ...$inflation,
        ...serializeDate($inflation),
    ];
}

function deserializeInflation(BSONDocument $inflation): array
{
    return [
        ...$inflation,
        ...deserializeDate($inflation),
    ];
}
