<?php

declare(strict_types=1);

namespace Gonsandia\Model;

class Product
{
    public const WATER_TYPE = 'WATER';
    public const JUICE_TYPE = 'JUICE';
    public const SODA_TYPE = 'SODA';

    public const WATER_PRICE = 0.65;
    public const JUICE_PRICE = 1.00;
    public const SODA_PRICE = 1.50;

    public const VALID_PRODUCT_TYPES = [
        self::WATER_TYPE,
        self::JUICE_TYPE,
        self::SODA_TYPE
    ];

    public const PRODUCT_PRICES = [
        self::WATER_TYPE => self::WATER_PRICE,
        self::JUICE_TYPE => self::JUICE_PRICE,
        self::SODA_TYPE => self::SODA_PRICE
    ];

    private float $value;

    private string $name;

    public function __construct(string $name)
    {
        $name = strtoupper($name);

        $this->checkValueIsAllowed($name);
        $this->name = $name;
        $this->setValueByName($name);
    }

    public function setValueByName(string $name): void
    {
        $this->value = self::PRODUCT_PRICES[$name];
    }

    private function checkValueIsAllowed(string $value): void
    {
        if (!in_array($value, self::VALID_PRODUCT_TYPES)) {
            throw new ProductValueNotAllowedException();
        }
    }

    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function equal(Product $product): bool
    {
        return ($this->value === $product->value) && ($this->name === $product->name);
    }

    public function getChangeValue(float $payment): float
    {
       $total = ($payment - $this->value);

       if ($total < 0) {
           throw new NotEnoughMoneyException();
       }
       return $total;
    }
}
