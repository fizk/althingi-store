<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use function App\serializeDatesRange;
use function App\deserializeDatesRange;
use function App\serializeBirth;
use function App\deserializeBirth;

class CongressmanSitting implements SourceDatabaseAware
{
    const COLLECTION = 'congressman-sitting';
    private Database $database;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document ? [
            ...$document,
            'assembly' => $document['assembly'] ? [
                ...$document['assembly'],
                ...deserializeDatesRange($document['assembly']),
            ] : null,
            'congressman' => $document['congressman'] ? [
                ...$document['congressman'],
                ...deserializeBirth($document['congressman']),
            ] : null,
            'congressman_constituency' => $document['congressman_constituency'] ? [
                ...$document['congressman_constituency']
            ] : null,
            'congressman_party' => $document['congressman_party'] ? [
                ...$document['congressman_party'],
            ] : null,
            ...deserializeDatesRange($document),
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return [
                ...$document,
                'assembly' => $document['assembly'] ? [
                    ...$document['assembly'],
                    ...deserializeDatesRange($document['assembly']),
                ] : null,
                'congressman' => $document['congressman'] ? [
                    ...$document['congressman'],
                    ...deserializeBirth($document['congressman']),
                ] : null,
                'congressman_constituency' => $document['congressman_constituency'] ? [
                    ...$document['congressman_constituency']
                ] : null,
                'congressman_party' => $document['congressman_party'] ? [
                    ...$document['congressman_party'],
                ] : null,
                ...deserializeDatesRange($document),
            ];
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    public function fetchPartiesByAssembly(int $assemblyId)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$congressman_party.party_id',
                        'party' => ['$first' => '$congressman_party']
                    ]
                ],
                [
                    '$replaceRoot' => ['newRoot' => '$party']
                ]
        ]);

        return array_map(function (BSONDocument $item) {
            return $item->getArrayCopy();
        },iterator_to_array($documents));
    }

    public function fetchCongressmenSessions(int $assemblyId, bool $primary = true)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId,
                        'type' => $primary ? ['$ne' => 'varamaður'] : ['$eq' => 'varamaður']
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$congressman.congressman_id',
                        'sessions' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$set' => [
                        'sessions' => [
                            '$function' => [
                                'body' => 'function(s) {s.sort((a, b) => a.from - b.from);return s;}',
                                'args' => ['$sessions'],
                                'lang' => 'js'
                            ]
                        ]
                    ]
                ],
                [
                    '$addFields' => [
                        'congressman' => [
                            '$first' => '$sessions.congressman'
                        ],
                        'assembly' => [
                            '$first' => '$sessions.assembly'
                        ]
                    ]
                ],
                [
                    '$sort' => ['congressman.name' => 1]
                ]
            ]
        );

        return array_map(function (BSONDocument $item) {
            return [
                '_id' => $item['_id'],
                'congressman' => $item['congressman'] ? [
                    ...$item['congressman'],
                    ...deserializeBirth($item['congressman']),
                ] : null,
                'assembly' => $item['assembly'] ? [
                    ...$item['assembly'],
                    ...deserializeDatesRange($item['assembly']),
                ] : null,
                'sessions' => array_map(function (BSONDocument $session) {
                    return [
                        ...$session,
                        ...deserializeDatesRange($session),
                        'assembly' => $session['assembly'] ? [
                            ...$session['assembly'],
                            ...deserializeDatesRange($session['assembly']),
                        ] : null,
                        'congressman' => $session['congressman'] ? [
                            ...$session['congressman'],
                            ...deserializeBirth($session['congressman']),
                        ] : null,
                        'congressman_party' => $session['congressman_party'] ? [
                            ...$session['congressman_party'],
                        ] : null,
                        'congressman_constituency' => $session['congressman_constituency'] ? [
                            ...$session['congressman_constituency']
                        ] : null,
                    ];
                }, $item['sessions']->getArrayCopy()),
            ];
        }, iterator_to_array($documents));
    }

    public function fetchPartiesSessions(int $assemblyId, bool $primary = true)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId,
                        'type' => $primary ? ['$ne' => 'varamaður'] : ['$eq' => 'varamaður']
                    ]
                ],
                [
                    '$sort' => [
                        'congressman.name' => 1
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'congressman' => '$congressman.congressman_id',
                            'party' => '$congressman_party.party_id'
                        ],
                        'congressman' => ['$first' => '$congressman'],
                        'assembly' => ['$first' => '$assembly'],
                        'sessions' => [
                            '$push' => [
                                '_id' => '$_id',
                                'congressman_party' => '$congressman_party',
                                'congressman_constituency' => '$congressman_constituency',
                                'from' => '$from',
                                'to' => '$to',
                                'type' => '$type'
                            ]
                        ],
                        'congressman_party' => ['$first' => '$congressman_party']
                    ]
                ],
                [

                    '$group' => [
                        '_id' => '$_id.party',

                        'congressmen' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$addFields' => [
                        'assembly' => ['$first' => '$congressmen.assembly'],
                        'congressman_constituency' => ['$first' => '$congressmen.congressman_constituency'],
                        'party_id' => ['$first' => '$congressmen.congressman_party.party_id'],
                        'name' => ['$first' => '$congressmen.congressman_party.name'],
                        'abbr_short' => ['$first' => '$congressmen.congressman_party.abbr_short'],
                        'abbr_long' => ['$first' => '$congressmen.congressman_party.abbr_long'],
                        'color' => ['$first' => '$congressmen.congressman_party.color'],
                    ]
                ],
                [
                    '$set' => [
                        'congressmen' => [
                            '$function' => [
                                'body' => 'function(all) {'.
                                    'all.sort((a, b) => a.congressman.name.localeCompare(b.congressman.name, "is"));'.
                                    'return all;'.
                                '}',
                                'args' => ['$congressmen'],
                                'lang' => 'js'
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => ['name' => 1]
                ]
            ]
        );

        return array_map(function (BSONDocument $item) {
            return [
                '_id' => $item['_id'],
                'party_id' => $item['party_id'],
                'name' => $item['name'],
                'abbr_short' => $item['abbr_short'],
                'abbr_long' => $item['abbr_long'],
                'color' => $item['color'],
                'congressmen' => array_map(function(BSONDocument $session) {
                    return [
                        '_id' => (int)"{$session['_id']['congressman']}{$session['_id']['party']}",
                        'congressman' => $session['congressman'] ? [
                            ...$session['congressman'],
                            ...deserializeBirth($session['congressman']),
                        ] : null,
                        'assembly' => $session['assembly'] ? [
                            ...$session['assembly'],
                            ...deserializeDatesRange($session['assembly']),
                        ] : null,
                        'sessions' => array_map(function(BSONDocument $record) {
                            return [
                                '_id' => $record['_id'],
                                'congressman_party' => $record['congressman_party'] ? [
                                    ...$record['congressman_party']
                                ] : null,
                                'congressman_constituency' => $record['congressman_constituency'] ? [
                                    ...$record['congressman_constituency']
                                ] : null,
                                'type' => $record['type'],
                                ...deserializeDatesRange($record),
                            ];
                        }, $session['sessions']->getArrayCopy()),
                    ];
                }, $item['congressmen']->getArrayCopy()),
                'assembly' => $item['assembly'] ? [
                    ...$item['assembly'],
                    ...deserializeDatesRange($item['assembly']),
                ] : null,

            ];
        }, iterator_to_array($documents));

    }

    public function fetchConstituenciesSessions(int $assemblyId, bool $primary = true)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId,
                        'type' => $primary ? ['$ne' => 'varamaður'] : ['$eq' => 'varamaður']
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'congressman' => '$congressman.congressman_id',
                            'constituency' => '$congressman_constituency.constituency_id'
                        ],
                        'congressman' => ['$first' => '$congressman'],
                        'assembly' => ['$first' => '$assembly'],
                        'sessions' => [
                            '$push' => [
                                '_id' => '$_id',
                                'congressman_party' => '$congressman_party',
                                'congressman_constituency' => '$congressman_constituency',
                                'from' => '$from',
                                'to' => '$to',
                                'type' => '$type'
                            ]
                        ],
                        'congressman_constituency' => ['$first' => '$congressman_constituency']
                    ]
                ],
                [

                    '$group' => [
                        '_id' => '$_id.constituency',

                        'congressmen' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$addFields' => [
                        'assembly' => ['$first' => '$congressmen.assembly'],
                        'constituency_id' => ['$first' => '$congressmen.congressman_constituency.constituency_id'],
                        'name' => ['$first' => '$congressmen.congressman_constituency.name'],
                        'abbr_short' => ['$first' => '$congressmen.congressman_constituency.abbr_short'],
                        'abbr_long' => ['$first' => '$congressmen.congressman_constituency.abbr_long'],
                        'description' => ['$first' => '$congressmen.congressman_constituency.description'],
                    ]
                ],
                [
                    '$set' => [
                        'congressmen' => [
                            '$function' => [
                                'body' => 'function(all) {' .
                                    'all.sort((a, b) => a.congressman.name.localeCompare(b.congressman.name, "is"));' .
                                    'return all;' .
                                '}',
                                'args' => ['$congressmen'],
                                'lang' => 'js'
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => ['name' => 1]
                ]
        ]);

        return array_map(function (BSONDocument $item) {
            return [
                '_id' => $item['_id'],
                'constituency_id' => $item['constituency_id'],
                'name' => $item['name'],
                'abbr_short' => $item['abbr_short'],
                'abbr_long' => $item['abbr_long'],
                'description' => $item['description'],
                'congressmen' => array_map(function(BSONDocument $session) {
                    return [
                        '_id' => (int)"{$session['_id']['congressman']}{$session['_id']['constituency']}",
                        'congressman' => $session['congressman'] ? [
                            ...$session['congressman'],
                            ...deserializeBirth($session['congressman']),
                        ] : null,
                        'assembly' => $session['assembly'] ? [
                            ...$session['assembly'],
                            ...deserializeDatesRange($session['assembly']),
                        ] : null,
                        'sessions' => array_map(function(BSONDocument $record) {
                            return [
                                '_id' => $record['_id'],
                                'congressman_party' => $record['congressman_party'] ? [
                                    ...$record['congressman_party']
                                ] : null,
                                'congressman_constituency' => $record['congressman_constituency'] ? [
                                    ...$record['congressman_constituency']
                                ] : null,
                                'type' => $record['type'],
                                ...deserializeDatesRange($record),
                            ];
                        }, $session['sessions']->getArrayCopy()),
                    ];
                }, $item['congressmen']->getArrayCopy()),
                'assembly' => $item['assembly'] ? [
                    ...$item['assembly'],
                    ...deserializeDatesRange($item['assembly']),
                ] : null,

            ];
        }, iterator_to_array($documents));

    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = [
            ...$object,
            '_id' => $object['session_id'],
            'assembly' => $object['assembly'] ? [
                ...$object['assembly'],
                ...serializeDatesRange($object['assembly']),
            ] : null,
            'congressman' => $object['congressman'] ? [
                ...$object['congressman'],
                ...serializeBirth($object['congressman']),
            ] : null,
            'congressman_constituency' => $object['congressman_constituency'] ? [
                ...$object['congressman_constituency']
            ] : null,
            'congressman_party' => $object['congressman_party'] ? [
                ...$object['congressman_party'],
            ] : null,
            ...serializeDatesRange($object),
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['session_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }

    public function updateAssembly(?array $assembly): void
    {
        if (!$assembly) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['assembly' => [
                    ...$assembly,
                    ...serializeDatesRange($assembly),
                ]]],
                ['upsert' => false]
            );
    }

    public function updateParty(?array $party): void
    {
        if (!$party) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman_party.party_id' => $party['party_id']],
                ['$set' => ['congressman_party' => $party,]],
                ['upsert' => false]
            );
    }

    public function updateCongressman(?array $congressman): void
    {
        if (!$congressman) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman.congressman_id' => $congressman['congressman_id']],
                ['$set' => ['congressman' => [
                    ...$congressman,
                    ...serializeBirth($congressman)
                ],]],
                ['upsert' => false]
            );
    }

    public function updateConstituency(?array $constituency): void
    {
        if (!$constituency) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman_constituency.constituency_id' => $constituency['constituency_id']],
                ['$set' => ['congressman_constituency' => $constituency,]],
                ['upsert' => false]
            );
    }

    public function getSourceDatabase(): Database
    {
        return $this->database;
    }

    public function setSourceDatabase(Database $database): self
    {
        $this->database = $database;
        return $this;
    }
}
