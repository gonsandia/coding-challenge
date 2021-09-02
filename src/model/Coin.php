<?php

declare(strict_types=1);

namespace Gonsandia\Model;

class Coin
{
    public const NICKEL_TYPE = 'NICKEL';
    public const DIME_TYPE = 'DIME';
    public const QUARTER_TYPE = 'QUARTER';
    public const DOLLAR_TYPE = 'DOLLAR';

    public const NICKEL_VALUE = 0.05;
    public const DIME_VALUE = 0.10;
    public const QUARTER_VALUE = 0.25;
    public const DOLLAR_VALUE = 1.00;

    public const VALID_COIN_VALUES = [
        self::NICKEL_VALUE,
        self::DIME_VALUE,
        self::QUARTER_VALUE,
        self::DOLLAR_VALUE
    ];

    public const VALID_COIN_TYPES = [
        self::NICKEL_TYPE,
        self::DIME_TYPE,
        self::QUARTER_TYPE,
        self::DOLLAR_TYPE
    ];

    public const COIN_VALUES = [
        self::NICKEL_TYPE => self::NICKEL_VALUE,
        self::DIME_TYPE => self::DIME_VALUE,
        self::QUARTER_TYPE => self::QUARTER_VALUE,
        self::DOLLAR_TYPE => self::DOLLAR_VALUE
    ];

    private float $value;

    private string $name;

    /**
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->checkIsValueIsAllowed($value);
        $this->value = $value;
        $this->setNameByValue($value);
    }

    /**
     * @param float $value
     */
    public function setNameByValue(float $value): void
    {
        foreach (self::COIN_VALUES as $type => $coinValue) {
            if ($value === $coinValue) {
                $this->name = $type;
                return;
            }
        }

        throw new CoinValueNotAllowedException();
    }

    private function checkIsValueIsAllowed(float $value): void
    {
        if (!in_array($value, self::VALID_COIN_VALUES, true)) {
            throw new CoinValueNotAllowedException();
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

    public function equal(Coin $coin): bool
    {
        return ($this->value === $coin->value) && ($this->name === $coin->name);
    }
}
