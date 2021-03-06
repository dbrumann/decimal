<?php

namespace Tg\Tests\Decimal;

use PHPUnit\Framework\TestCase;
use function Tg\Decimal\dec;
use function Tg\Decimal\dec0;
use function Tg\Decimal\dec1;
use function Tg\Decimal\dec2;
use function Tg\Decimal\dec3;
use function Tg\Decimal\dec5;
use function Tg\Decimal\dec6;
use Tg\Decimal\Decimal;
use Tg\Decimal\Decimal2;
use function Tg\Decimal\floatish as fl;

class DecimalTest extends TestCase
{
    private static function assertDecimalSame(Decimal $a, Decimal $b)
    {
        static::assertSame((string)$a, (string)$b);
    }

    private static function assertDecimalNotSame(Decimal $a, Decimal $b)
    {
        static::assertNotSame((string)$a, (string)$b);
    }

    public function dataProviderAddNotSame()
    {
        yield [
            fl("0.21"),
            fl("0.10")->add(fl("0.10"))
        ];
    }

    /** @dataProvider dataProviderAddNotSame */
    public function testAddNotSame(Decimal $a, Decimal $b)
    {
        static::assertDecimalNotSame($a, $b);
    }

    /**
     * @dataProvider dataProviderAddSame
     * @dataProvider dataProviderMulScaleBySelf
     * @dataProvider dataProviderMul
     * @dataProvider dataProviderSubtract
     * @dataProvider dataProviderDiv
     */
    public function testSame(Decimal $a, Decimal $b)
    {
        static::assertDecimalSame($a, $b);
    }

    public function dataProviderAddSame()
    {
        yield [
            fl("0.20"),
            fl("0")->add(fl("0.20"))
        ];

        yield [
            fl("0.20"),
            fl("0.10")->add(fl("0.10"))
        ];

        yield [
            fl("0.200"),
            fl("0.10")->add(fl("0.10"))
        ];

        yield [
            fl("1"),
            fl("0.5")->add(fl("0.5"))
        ];

        yield [
            fl("2"),
            fl("1")->add(fl("1"))
        ];

        yield [
            fl("0.000000000002"),
            fl("0.000000000001")->add(fl("0.000000000001"))
        ];
    }

    public function dataProviderSubtract()
    {
        yield [
            fl("-0.1"),
            fl("0")->sub(fl("0.1"))
        ];

        yield [
            fl("-2.9"),
            fl("0.1")->sub(fl("3"))
        ];

        yield [
            fl("-2.1"),
            fl("-3.1")->sub(fl("-1"))
        ];
    }

    public function dataProviderMul()
    {
        yield [
            dec("1.21"),
            dec("1.1")->mul(dec("1.1"))
        ];

        yield [
            dec("1.70052"),
            dec("1.532")->mul(dec("1.11"))
        ];
    }

    public function dataProviderMulScaleBySelf()
    {

        yield [
            dec("1.2"),
            dec("1.1")->mulScaleBySelf(dec("1.1"))
        ];

        yield [
            fl("0"),
            fl("1")->mulScaleBySelf(fl("0"))
        ];

        yield [
            fl("1"),
            fl("1")->mulScaleBySelf(fl("1"))
        ];

        yield [
            fl("0.30"),
            fl("0.10")->mulScaleBySelf(fl("3"))
        ];

        yield [
            fl("4"),
            fl("2")->mulScaleBySelf(fl("2"))
        ];

        yield [
            fl("0.000000000004"),
            fl("0.000000000002")->mulScaleBySelf(fl("2"))
        ];
    }

    public function dataProviderDiv()
    {
        yield [
            fl("0.3333333333333333"),
            fl("1")->divScaleBySelf(fl("3"))
        ];
    }

    public function testPrecision()
    {
        $prec = "0.3333333333333333";

        static::assertSame(
            $prec,
            var_export(1 / 3, true)
        );

        static::assertSame(
            $prec,
            fl("1")->divScaleBySelf(fl("3"))->__toString()
        );
    }

    public function testPrecision2()
    {
        static::assertSame(
            '33.333333333333336',
            var_export(100 / 3, true)
        );

        static::assertSame(
            '33.3333333333333333',
            fl("100")->divScaleBySelf(fl("3"))->__toString()
        );


        static::assertSame(
            '33.3333333333333333',
            fl("100")->divScaleBySelf(fl("3"), true)->__toString()
        );
    }

    public function testRound()
    {
        static::assertDecimalSame(
            dec2('100.54')->round(1),
            dec1('100.5')
        );

        static::assertDecimalSame(
            dec2('100.55')->round(1),
            dec1('100.6')
        );

        static::assertDecimalSame(
            dec0('3'),
            dec0('5')->divScaleBySelf(dec0('2'), true)
        );

        static::assertDecimalSame(
            dec0('2'),
            dec0('5')->divScaleBySelf(dec0('2'), false)
        );
    }

    public function testReduceScale()
    {
        static::assertNotSame(
            (string)dec1("1.0"),
            (string)dec6('1.000000')->reduceScale()
        );

        static::assertSame(
            (string)dec0("1"),
            (string)dec6('1.000000')->reduceScale()
        );

        static::assertSame(
            (string)dec0("1"),
            (string)dec6('1')->reduceScale()
        );

        static::assertSame(
            (string)dec3("1.111"),
            (string)dec6('1.111000')->reduceScale()
        );

        static::assertSame(
            (string)dec5("1.11101"),
            (string)dec6('1.111010')->reduceScale()
        );
    }

    public function testPow()
    {
        static::assertSame(
            "100",
            (string)dec0("10")->pow(dec0("2"))
        );

        static::assertSame(
            "100000",
            (string)dec0("10")->pow(dec0("5"))
        );
    }

    public function testAbs()
    {
        static::assertSame(
            "10",
            (string)dec("10")->abs()
        );

        static::assertSame(
            "10",
            (string)dec("-10")->abs()
        );
    }

    public function testToDecimal()
    {
        $res = dec("0.1256")->toDecimal(2);

        static::assertInstanceOf(Decimal2::class, $res);
        static::assertSame((string)$res, '0.13');
    }
}