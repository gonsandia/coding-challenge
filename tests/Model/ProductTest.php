<?php

namespace Gonsandia\Tests\Model;

use Gonsandia\Model\Product;
use Gonsandia\Model\ProductValueNotAllowedException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGivenNotValidValueItShoulThrowException()
    {
        $this->expectException(ProductValueNotAllowedException::class);

        new Product('Beer');
    }

    public function testGivenValidValueItShoulCreateProduct()
    {
        $value1 = 'Water';
        $value2 = 'soda';
        $value3 = 'JUICE';

        $product1 = new Product($value1);
        $product2 = new Product($value2);
        $product3 =  new Product($value3);

        $this->assertEquals('WATER', $product1->name());
        $this->assertEquals('SODA', $product2->name());
        $this->assertEquals('JUICE', $product3->name());
        $this->assertEquals(0.65, $product1->value());
        $this->assertEquals(1.5, $product2->value());
        $this->assertEquals(1, $product3->value());
    }
}
