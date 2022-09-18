<?php

namespace App\Routes;

use PHPUnit\Framework\TestCase;
use App\Handler;
use App\Routes\RoutesTrait;

class IssuesTest extends TestCase {

    use RoutesTrait;

    public function testThingmalRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
        ];
        $this->assertEquals(Handler\AssemblyIssues::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalWithCategoryRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'category' => 'a',
        ];
        $this->assertEquals(Handler\AssemblyIssues::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalWithIssueRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
        ];
        $this->assertEquals(Handler\AssemblyIssue::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalWithIssueCategoryRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/efnisflokkar');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
        ];
        $this->assertEquals(Handler\AssemblyIssueCategory::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalWithIssueSuperCategoryRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/efnishopar');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
        ];
        $this->assertEquals(Handler\AssemblyIssueSuperCategory::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalSpeechesRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/readur');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
        ];
        $this->assertEquals(Handler\AssemblyIssueSpeeches::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalSpeechRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/readur/19960312T190659');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
            'speech_id' => '19960312T190659'
        ];
        $this->assertEquals(Handler\AssemblyIssueSpeech::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalSpeecNonDateIDhRoute()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/readur/f0c5e59d51075aec341baa6a7cdaed90');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
            'speech_id' => 'f0c5e59d51075aec341baa6a7cdaed90'
        ];
        $this->assertEquals(Handler\AssemblyIssueSpeech::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalAllThingskjol()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/thingskjol');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
        ];
        $this->assertEquals(Handler\AssemblyIssueDocuments::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalSingleThingskjol()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/thingskjol/3');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
            'document_id' => 3,
        ];
        $this->assertEquals(Handler\AssemblyIssueDocument::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalSingleThingskjolVote()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/a/2/thingskjol/3/atkaedagreidsla');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
            'issue_id' => 2,
            'category' => 'a',
            'document_id' => 3,
        ];
        $this->assertEquals(Handler\AssemblyIssueDocumentsOutcome::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalStatusAggregation()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/stodur');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
        ];
        $this->assertEquals(Handler\AssemblyIssuesStatuses::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }

    public function testThingmalContentTypeAggregation()
    {
        // GIVEN
        $request = $this->createRequest('/loggjafarthing/1/thingmal/efnisflokkar');

        // WHEN
        $match = $this->getRoutesDefinitions()->match($request);

        // THEN
        $expectedAttributes = [
            'assembly_id' => 1,
        ];
        $this->assertEquals(Handler\AssemblyContentCategories::class, $match->getParam('handler'));
        $this->assertEquals($expectedAttributes, $match->getAttributes());
    }
}
