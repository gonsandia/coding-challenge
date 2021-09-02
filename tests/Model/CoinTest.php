<?php

namespace Gonsandia\Tests\Model;

use Gonsandia\Model\Coin;
use Gonsandia\Model\CoinValueNotAllowedException;
use PHPUnit\Framework\TestCase;

class CoinTest extends TestCase
{
    public function testGivenNotValidValueItShoulThrowException()
    {
        $this->expectException(CoinValueNotAllowedException::class);

        new Coin(0.5);
    }

    public function testItShoulCreateValidCoins()
    {
        $value1 = 0.05;
        $value2 = '0.10';
        $value3 = 0.25;
        $value4 = 1;

        $coin1 = new Coin($value1);
        $coin2 = new Coin($value2);
        $coin3 =  new Coin($value3);
        $coin4 =  new Coin($value4);

        $this->assertEquals($value1, $coin1->value());
        $this->assertEquals($value2, $coin2->value());
        $this->assertEquals($value3, $coin3->value());
        $this->assertEquals($value4, $coin4->value());
        $this->assertEquals(Coin::NICKEL_TYPE, $coin1->name());
        $this->assertEquals(Coin::DIME_TYPE, $coin2->name());
        $this->assertEquals(Coin::QUARTER_TYPE, $coin3->name());
        $this->assertEquals(Coin::DOLLAR_TYPE, $coin4->name());
    }
}
