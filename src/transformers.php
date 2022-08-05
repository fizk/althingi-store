<?php

namespace App;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

function serializeDatesRange(array $range): array
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

function serializeBirth(array $date): array
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

function serializeDate(array $date): array
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

function serializeAssembly(?array $assembly): ?array
{
    if (!$assembly) return null;

    return [
        ...$assembly,
        ...serializeDatesRange($assembly),
    ];
}

function deserializeAssembly(?BSONDocument $assembly): ?array
{
    if (!$assembly) return null;
    return [
        ...$assembly,
        ...deserializeDatesRange($assembly)
    ];
}

function serializeCongressman(?array $congressman): ?array
{
    if (!$congressman) return null;
    return [
        ...$congressman,
        ...serializeBirth($congressman),
    ];
}

function deserializeCongressman(?BSONDocument $congressman): ?array
{
    if (!$congressman) return null;
    return [
        ...$congressman,
        ...deserializeBirth($congressman),
    ];
}

function serializeCommittee(?array $committee): ?array
{
    if (!$committee) return null;
    return [
        ...$committee,
    ];
}

function deserializeCommittee(?BSONDocument $committee): ?array
{
    if (!$committee) return null;
    return [...$committee];
}

function serializeParty(?array $party): ?array
{
    if (!$party) return null;
    return [
        ...$party,
    ];
}

function deserializeParty(?BSONDocument $party): ?array
{
    if (!$party) return null;
    return [...$party];
}

function serializeConstituency(?array $constituency): ?array
{
    if (!$constituency) return null;
    return [
        ...$constituency,
    ];
}

function deserializeConstituency(?BSONDocument $constituency): ?array
{
    if (!$constituency) return null;
    return [...$constituency];
}

function serializeMinistry(?array $ministry): ?array
{
    if (!$ministry) return null;
    return [
        ...$ministry,
    ];
}

function deserializeMinistry(?BSONDocument $ministry): ?array
{
    if (!$ministry) return null;
    return [
        ...$ministry,
    ];
}

function serializeInflation(?array $inflation): ?array
{
    if (!$inflation) return null;
    return [
        ...$inflation,
        ...serializeDate($inflation),
    ];
}

function deserializeInflation(?BSONDocument $inflation): ?array
{
    if (!$inflation) return null;
    return [
        ...$inflation,
        ...deserializeDate($inflation),
    ];
}

function serializePlenary(?array $plenary): ?array
{
    if (!$plenary) return null;
    return array_merge([
        ...$plenary,
        ...serializeDatesRange($plenary),
    ], isset($plenary['assembly']) ? ['assembly' => serializeAssembly($plenary['assembly'])] : []);
}

function deserializePlenary(?BSONDocument $plenary): ?array
{
    if (!$plenary) return null;

    return array_merge([
        ...$plenary,
        'assembly' => deserializeAssembly($plenary['assembly']),
        ...deserializeDatesRange($plenary),
    ], isset($plenary['_id']) ? ['_id' => [...$plenary['_id']]] : []);
}

function serializePlenaryAgenda(?array $plenary): ?array
{
    if (!$plenary) return null;
    return [
        ...$plenary,
        'assembly' => serializeAssembly($plenary['assembly']),
        'issue' => serializeIssue($plenary['issue']),
        'plenary' => serializePlenary($plenary['plenary']),

        'posed' => serializeCongressman($plenary['posed']),
        'posed_party' => serializeParty($plenary['posed_party']),
        'posed_constituency' => serializeConstituency($plenary['posed_constituency']),

        'answerer' => serializeCongressman($plenary['answerer']),
        'answerer_party' => serializeParty($plenary['answerer_party']),
        'answerer_constituency' => serializeConstituency($plenary['answerer_constituency']),

        'counter_answerer' => serializeCongressman($plenary['counter_answerer']),
        'counter_answerer_party' => serializeParty($plenary['counter_answerer_party']),
        'counter_answerer_constituency' => serializeConstituency($plenary['counter_answerer_constituency']),

        'instigator' => serializeCongressman($plenary['instigator']),
        'instigator_party' => serializeParty($plenary['instigator_party']),
        'instigator_constituency' => serializeConstituency($plenary['instigator_constituency']),
    ];
}

function deserializePlenaryAgenda(?BSONDocument $plenary): ?array
{
    if (!$plenary) return null;
    return [
        ...$plenary,
        '_id' => (array) $plenary['_id'],
        'assembly' => deserializeAssembly($plenary['assembly']),
        'issue' => deserializeIssue($plenary['issue']),
        'plenary' => deserializePlenary($plenary['plenary']),

        'posed' => deserializeCongressman($plenary['posed']),
        'posed_party' => deserializeParty($plenary['posed_party']),
        'posed_constituency' => deserializeConstituency($plenary['posed_constituency']),

        'answerer' => deserializeCongressman($plenary['answerer']),
        'answerer_party' => deserializeParty($plenary['answerer_party']),
        'answerer_constituency' => deserializeConstituency($plenary['answerer_constituency']),

        'counter_answerer' => deserializeCongressman($plenary['counter_answerer']),
        'counter_answerer_party' => deserializeParty($plenary['counter_answerer_party']),
        'counter_answerer_constituency' => deserializeConstituency($plenary['counter_answerer_constituency']),

        'instigator' => deserializeCongressman($plenary['instigator']),
        'instigator_party' => deserializeParty($plenary['instigator_party']),
        'instigator_constituency' => deserializeConstituency($plenary['instigator_constituency']),
    ];
}

function serializeIssue(?array $issue): ?array {
    if (!$issue) return null;

    return array_merge([
        ...$issue,
    ], isset($issue['assembly']) ? ['assembly' => serializeAssembly($issue['assembly'])] : []);
}

function deserializeIssue(?BSONDocument $issue): ?array {
    if (!$issue) return null;

    return array_merge([
        ...$issue,
    ], isset($issue['assembly']) ? ['assembly' => deserializeAssembly($issue['assembly'])] : []);
}
