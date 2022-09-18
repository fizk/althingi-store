<?php
namespace App\Handler;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ServerRequest;

class QueryParamTraitTest extends TestCase
{
    /**
     * @dataProvider dataProviderExtractType
     */
    public function testExtractType(ServerRequestInterface $request, array $expected)
    {
        $class = new class () {
            use QueryParamTrait;
        };

        $actual = $class->extractType($request);
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderExtractType()
    {
        return [
            [(new ServerRequest([], [], '/url'))->withQueryParams(['malaflokkur' => 'l']), ['l']],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['malaflokkur' => 'L']), ['l']],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['malaflokkur' => 'l,a']), ['l', 'a']],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['malaflokkur' => 'L,A']), ['l', 'a']],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['malaflokkur' => 'l, a']), ['l', 'a']],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['malaflokkur' => '']), []],
            [(new ServerRequest([], [], '/url'))->withQueryParams([]), []],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['somethingelse' => '1']), []],
            [(new ServerRequest([], [], '/url')), []],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['param' => 'one', 'malaflokkur' => 'l']), ['l']],
        ];
    }

    /**
     * @dataProvider dataProviderExtractCongressmanIsPrimary
     */
    public function testExtractCongressmanIsPrimary(ServerRequestInterface $request, bool $expected)
    {
        $class = new class () {
            use QueryParamTrait;
        };

        $actual = $class->extractCongressmanIsPrimary($request);
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderExtractCongressmanIsPrimary()
    {
        return [
            [(new ServerRequest([], [], '/url'))->withQueryParams(['tegund' => 'varamenn']), false],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['tegund' => 'VARAMENN']), false],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['tegund' => 'annad']), true],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['tegund' => '']), true],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['tegund']), true],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['param' => 'one']), true],
            [(new ServerRequest([], [], '/url')), true],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['param' => 'one', 'tegund' => 'varamenn']), false],
        ];
    }

    /**
     * @dataProvider dataProviderExtractPointer
     */
    public function testExtractPointer(ServerRequestInterface $request, ?string $expected)
    {
        $class = new class () {
            use QueryParamTrait;
        };

        $actual = $class->extractPointer($request);
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderExtractPointer()
    {
        return [
            [(new ServerRequest([], [], '/url'))->withQueryParams(['bendill' => '1']), '1'],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['bendill' => 'long-string']), 'long-string'],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['bendill' => '']), null],
            [(new ServerRequest([], [], '/url'))->withQueryParams(['bendill']), null],
        ];
    }
}
