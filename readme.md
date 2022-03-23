```
[x] /loggjafarthing/${data.body.assembly_id}: AssemblyPayload
export interface AssemblyPayload {
    assembly_id: number
    from: Maybe<string>
    to: Maybe<string>
}

[x] /nefndir/${data.body.committee_id}: CommitteePayload
export interface CommitteePayload {
    committee_id: number
    name: string
    abbr_long: Maybe<string>
    abbr_short: Maybe<string>
    first: Maybe<Assembly>,
    last: Maybe<Assembly>,
}

[x] /thingseta/${body.session_id}: CongressmanSittingPayload
export interface CongressmanSittingPayload {
    session_id: number
    from: Maybe<string>
    to: Maybe<string>
    type: Maybe<string>
    abbr: Maybe<string>
    assembly: Assembly,
    congressman: Congressman,
    congressman_constituency: Constituency,
    congressman_party: Maybe<Party>
}

[x] /kjordaemi/${data.body.constituency_id}: ConstituencyPayload
export interface ConstituencyPayload {
    constituency_id: number
    name: Maybe<string>
    abbr_short: Maybe<string>
    abbr_long: Maybe<string>
    description: Maybe<string>
}

[x] /verdbolga/${data.body.id}: InflationPayload
export interface InflationPayload {
    id: number
    value: number
    date: string
}

[x] /raduneyti/${data.body.ministry_id}: MinistryPayload
export interface MinistryPayload {
    ministry_id: number
    name: Maybe<string>
    abbr_short: Maybe<string>
    abbr_long: Maybe<string>
    first: Maybe<Assembly>
    last: Maybe<Assembly>
}

[x] /thingflokkar/${data.body.party_id}: PartyPayload
export interface PartyPayload {
    party_id: number
    name: string
    abbr_short: Maybe<string>
    abbr_long: Maybe<string>
    color: Maybe<string>
}

[x] /nefndarseta/${body.committee_sitting_id}: CommitteeSittingPayload
export interface CommitteeSittingPayload {
    committee_sitting_id: number
    order: Maybe<number>
    role: Maybe<string>
    from: string
    to: Maybe<string>
    assembly: Maybe<Assembly>,
    committee: Maybe<Committee>,
    congressman: Maybe<Congressman>,
    congressman_party: Maybe<Party>
    congressman_constituency: Maybe<Constituency>,
    first_committee_assembly: Maybe<Assembly>,
    last_committee_assembly: Maybe<Assembly>
}

[x] /thingmenn/${data.body.congressman_id}: CongressmanPayload
export interface CongressmanPayload {
    congressman_id: number
    name: string
    birth: string
    death: Maybe<string>
    abbreviation: Maybe<string>
}

[ ] /radherraseta/${data.body.minister_sitting_id}: MinisterSittingPayload
export interface MinisterSittingPayload {
    minister_sitting_id: number
    from: string
    to: Maybe<string>
    assembly: Maybe<Assembly>
    ministry: Maybe<Ministry>
    congressman: Maybe<Congressman>
    congressman_constituency: Maybe<Constituency>
    congressman_party: Maybe<Party>
    first_ministry_assembly: Maybe<Assembly>
    last_ministry_assembly: Maybe<Assembly>
}

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
