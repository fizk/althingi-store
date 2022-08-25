<?php

namespace App\Routes;

use PHPUnit\Framework\TestCase;
use App\Handler;
use App\Routes\RoutesTrait;

class PlenaryTest extends TestCase {

    use RoutesTrait;

    public function testThingfundirRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingfundir');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
        ];
        $this->assertEquals(Handler\AssemblyPlenaries::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingfundirFundurRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingfundir/2');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'plenary_id' => 2,
        ];
        $this->assertEquals(Handler\AssemblyPlenary::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingfundirFirstFundurRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingfundir/-');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'plenary_id' => '-',
        ];
        $this->assertEquals(Handler\AssemblyPlenary::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }
}
