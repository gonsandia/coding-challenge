<?php

namespace Gonsandia\Tests\Model;

use Gonsandia\Model\Coin;
use Gonsandia\Model\NotAllowedItemQuantity;
use Gonsandia\Model\NotEnoughChangeException;
use Gonsandia\Model\Product;
use Gonsandia\Model\ProductNotAvailableException;
use Gonsandia\Model\VendingMachine;
use PHPUnit\Framework\TestCase;

class VendingMachineTest extends TestCase
{
    public function testItShoulCreateVendingMachine()
    {
        $vendingMachine = new VendingMachine();
        $this->assertInstanceOf(VendingMachine::class, $vendingMachine);
    }

    public function testItShoulInsertAndReturnCoins()
    {
        $vendingMachine = new VendingMachine();
        $coins = $this->coins();

        foreach ($coins as $coin) {
            $vendingMachine->insertMoney($coin);
        }

        $returnedCoins = $vendingMachine->returnCoins();

        foreach ($returnedCoins as $coin) {
            $this->assertContains($coin, $coins);
        }

        $this->assertEmpty($vendingMachine->returnCoins());
    }

    public function testItShoulInsertCoinsAndThrowException()
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->insertMoney(new Coin(1));

        $this->expectException(ProductNotAvailableException::class);

        $vendingMachine->getProduct(new Product('JUICE'));
    }

    public function testWhenAddInvalidNumberOfProductsItShouldThrowException()
    {
        $vendingMachine = new VendingMachine();

        $this->expectException(NotAllowedItemQuantity::class);

        $vendingMachine->setProducts(new Product('JUICE'), -1);
    }

    public function testWhenAddInvalidNumberOfCoinsItShouldThrowException()
    {
        $vendingMachine = new VendingMachine();

        $this->expectException(NotAllowedItemQuantity::class);

        $vendingMachine->setCoins(new Coin(1), -1);
    }

    public function testItShoulInsertCoinsAndPurchaseProductAndRemoveCoins()
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->insertMoney(new Coin(1));
        $vendingMachine->setProducts(new Product('JUICE'), 10);

        $returnedCoins = $vendingMachine->getProduct(new Product('JUICE'));

        $this->assertEmpty($returnedCoins);

        $this->assertEmpty($vendingMachine->returnCoins());
    }

    public function testItShoulInsertCoinsAndPurchaseProductReturnChange()
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->insertMoney(new Coin(1));
        $vendingMachine->insertMoney(new Coin(1));
        $vendingMachine->setProducts(new Product('JUICE'), 10);

        $returnedCoins = $vendingMachine->getProduct(new Product('JUICE'));

        $this->assertCount(1, $returnedCoins);
        $coin = array_pop($returnedCoins);
        $this->assertEquals(new Coin(1), $coin);

        $this->assertEmpty($vendingMachine->returnCoins());
    }

    public function testGivenStateItShoulInsertCoinsAndPurchaseProductReturnChange()
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->setProducts(new Product('JUICE'), 10);

        $vendingMachine->insertMoney(new Coin(0.25));
        $vendingMachine->insertMoney(new Coin(0.25));
        $vendingMachine->insertMoney(new Coin(0.25));
        $vendingMachine->insertMoney(new Coin(0.1));
        $vendingMachine->insertMoney(new Coin(0.1));
        $vendingMachine->insertMoney(new Coin(0.1));
        $vendingMachine->insertMoney(new Coin(0.05));

        $returnedCoins = $vendingMachine->getProduct(new Product('JUICE'));

        $this->assertCount(1, $returnedCoins);
        $coin = array_pop($returnedCoins);
        $this->assertEquals(new Coin(0.1), $coin);

        $this->assertEmpty($vendingMachine->returnCoins());
    }

    public function testItShoulInsertCoinsAndPurchaseProductThrowException()
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->setProducts(new Product('JUICE'), 10);

        $vendingMachine->insertMoney(new Coin(0.25));
        $vendingMachine->insertMoney(new Coin(0.25));
        $vendingMachine->insertMoney(new Coin(0.25));
        $vendingMachine->insertMoney(new Coin(0.1));
        $vendingMachine->insertMoney(new Coin(0.1));
        $vendingMachine->insertMoney(new Coin(0.1));

        $this->expectException(NotEnoughChangeException::class);

        $vendingMachine->getProduct(new Product('JUICE'));
    }

    public function coins()
    {
        $coins = [];
        $value1 = 0.05;
        $value2 = '0.10';
        $value3 = 0.25;
        $value4 = 1;

        $coins[] = new Coin($value1);
        $coins[] = new Coin($value2);
        $coins[] = new Coin($value3);
        $coins[] = new Coin($value4);

        return $coins;
    }
}
