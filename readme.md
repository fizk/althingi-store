```
[ ] /forsetaseta/${data.body.president_id}: PresidentSittingPayload
export interface PresidentSittingPayload {
    president_id: number
    from: string
    to: Maybe<string>
    title: string
    abbr: Maybe<string>
    assembly: Maybe<Assembly>
    congressman: Maybe<Congressman>
    congressman_party: Maybe<Party>
    congressman_constituency: Maybe<Constituency>
}

export interface Assembly {
    assembly_id: number
    from: Maybe<string>
    to: Maybe<string>
}
export interface Congressman {
    congressman_id: number
    name: string
    birth: string
    death: Maybe<string>
    abbreviation: Maybe<string>
}
export interface Party {
    party_id: number
    name: string
    abbr_short: Maybe<string>
    abbr_long: Maybe<string>
    color: Maybe<string>
}
export interface Constituency {
    constituency_id: number
    name: Maybe<string>
    abbr_short: Maybe<string>
    abbr_long: Maybe<string>
    description: Maybe<string>
}
```






// committee-sitting.add 	=> `/nefndarseta/${body.committee_sitting_id}`      | Handler\CommitteeSitting::class   : committee-sitting
// assembly.add 			=> `/loggjafarthing/${data.body.assembly_id}`       | Handler\Assembly::class           : assembly
// committee.add			=> `/nefndir/${data.body.committee_id}`             | Handler\Committee::class          : committee
// congressman.add 		    => `/thingmenn/${data.body.congressman_id}`         | Handler\Congressman::class        : congressman
// constituency.add 		=> `/kjordaemi/${data.body.constituency_id}`        | Handler\Constituency::class       : constituency
// minister-sitting.add 	=> `/radherraseta/${data.body.minister_sitting_id}` |

// ministry.add             => `/raduneyti/${data.body.ministry_id}`            | Handler\Ministry::class           : ministry
// party.add                => `/thingflokkar/${data.body.party_id}`            | Handler\Party::class              : party
// inflation.add            => `/verdbolga/${data.body.id}`                     | Handler\Inflation::class          : inflation
// session.add              => `/thingseta/${body.session_id}`                  | Handler\CongressmanSitting::class : congressman-sitting
// president.add            => `/forsetaseta/${data.body.president_id}`         |





// php ./public/index.php console:assembly && \
// php ./public/index.php console:committee-sitting && \
// php ./public/index.php console:committee && \
// php ./public/index.php console:congressman && \
// php ./public/index.php console:constituency && \
// php ./public/index.php console:minister-sitting && \
// php ./public/index.php console:ministry && \
// php ./public/index.php console:party && \
// php ./public/index.php console:inflation && \
// php ./public/index.php console:session && \
// php ./public/index.php console:president-sitting



## Get Congressman Sessions
```
db.getCollection('congressman-sitting').aggregate([
    {
        $match: {
            'assembly.assembly_id': 140,
            'type': {$ne: 'varamaður'}
        }
    },
    {
        $group: {
            _id: '$congressman.congressman_id',
            sessions: { $push: "$$ROOT"}
        }
    },
    {
            $addFields: {
                "congressman": {
                    $first: "$sessions.congressman"
                 },
                "assembly": {
                    $first: "$sessions.assembly"
                 }
            }
    }
])
```

## Get all parties in Assembly
```
db.getCollection('congressman-sitting').aggregate([
    {
        $match: {
                'assembly.assembly_id': 140
        }
    },
    {
            $group: {
                    _id: '$congressman_party.party_id',
                    party: { $first: "$congressman_party"}
            }
    },
    {
        $replaceRoot: { newRoot: "$party" }
    }
])
```

## Get all constituencies including congressmen sessions
```
db.getCollection('congressman-sitting').aggregate([
    {
        $match: {
                'assembly.assembly_id': 140
        }
    },
    {
            $group: {
                    _id: {
                        congressman: '$congressman.congressman_id',
                        constituency: '$congressman_constituency.constituency_id'
                     },
                     congressman: { $first: "$congressman"},
                     assembly: { $first: "$assembly"},
                     sessions: { $push: {
                         _id: '$_id',
                         congressman_party: "$congressman_party",
                         congressman_constituency: '$congressman_constituency',
                         from: "$from",
                         to: "$to",
                         type: "$type"
                        }
                     },
                     congressman_constituency: { $first: "$congressman_constituency"}
            }
    }
   ,
    {

             $group: {
                _id: '$_id.constituency',

                congressmen: { $push: "$$ROOT"}
            }
    }
    ,
    {
            $addFields: {
                 assembly: {$first: '$congressmen.assembly'},
                 constituency_id: {$first: '$congressmen.congressman_constituency.constituency_id'},
                 name: {$first: '$congressmen.congressman_constituency.name'},
                 abbr_short: {$first: '$congressmen.congressman_constituency.abbr_short'},
                 abbr_long: {$first: '$congressmen.congressman_constituency.abbr_long'},
                 description: {$first: '$congressmen.congressman_constituency.description'},
            }
    },
    {
            $sort: {name: 1}
    }
])






```

## Get all parties including congressman sessions
```
db.getCollection('congressman-sitting').aggregate([
    {
        $match: {
                'assembly.assembly_id': 140,
            'type': {$ne: 'varamaður'}
        }
    },
    {
            $group: {
                    _id: {
                        congressman: '$congressman.congressman_id',
                        constituency: '$congressman_constituency.constituency_id'
                     },
                     congressman: { $first: "$congressman"},
                     assembly: { $first: "$assembly"},
                     sessions: { $push: {
                         _id: '$_id',
                         congressman_party: "$congressman_party",
                         congressman_constituency: '$congressman_constituency',
                         from: "$from",
                         to: "$to",
                         type: "$type"
                        }
                     },
                     congressman_constituency: { $first: "$congressman_constituency"}
            }
    }
   ,
    {

             $group: {
                _id: '$_id.constituency',

                congressmen: { $push: "$$ROOT"}
            }
    }
    ,
    {
            $addFields: {
                 assembly: {$first: '$congressmen.assembly'},
                 constituency_id: {$first: '$congressmen.congressman_constituency.constituency_id'},
                 name: {$first: '$congressmen.congressman_constituency.name'},
                 abbr_short: {$first: '$congressmen.congressman_constituency.abbr_short'},
                 abbr_long: {$first: '$congressmen.congressman_constituency.abbr_long'},
                 description: {$first: '$congressmen.congressman_constituency.description'},
            }
    },
    {
            $set: {
                'congressmen': {
                        $function: {
                            body: function(all) {
                                all.sort((a, b) => a.congressman.name.localeCompare(b.congressman.name))
                                return all;
                            },
                            args: ['$congressmen'],
                            lang: "js"
                        }
                 }
            }
    },
    {
            $sort: {name: 1}
    }
])
```

## Get all parties in a government
```
db.getCollection('minister-sitting').aggregate([

    {
            $match: {
                    'assembly.assembly_id': 146
            }
    },
    {
            $group: {
                    _id: '$congressman_party.party_id',
                    party: { $push: "$$ROOT"}
            }
    },
    {
            $addFields: {
                 party: {$first: '$party.congressman_party'}
            }
    },
    {
        $replaceRoot: { newRoot: "$party" }
    },
    {
            $sort: {name: 1}
    }
])
```
## Get minister sessions
```
db.getCollection('minister-sitting').aggregate([

    {
            $match: {
                    'assembly.assembly_id': 140
            }
    }
    ,
    {
            $group: {
                _id: '$ministry.ministry_id',
                ministry_id: {$first: '$ministry.ministry_id'},
                abbr_short: {$first: '$ministry.abbr_short'},
                abbr_long: {$first: '$ministry.abbr_long'},
                first: {$first: '$ministry.first'},
                last: {$first: '$ministry.last'},
                name: {$first: '$ministry.name'},
                congressmen: { $push: "$$ROOT"}
            }
    }
    ,
    {
            $set: {
                'congressmen': {
                        $function: {
                            body: function(all) {
                                all.sort((a, b) => a.from - b.from)
                                return all;
                            },
                            args: ['$congressmen'],
                            lang: "js"
                        }
                 }
            }
    },
])
```
