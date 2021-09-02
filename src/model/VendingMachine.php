<?php

declare(strict_types=1);

namespace Gonsandia\Model;

use ItemNotAllowedException;

class VendingMachine
{
    private array $state = [];

    private array $coins = [];

    private array $memento = [];

    public function insertMoney(Coin $coin): void
    {
        $this->coins[] = $coin;
    }

    public function returnCoins(): array
    {
        $returnCoins = $this->coins;
        $this->cleanCoins();
        return $returnCoins;
    }

    public function getProduct(Product $product): array
    {
        $this->purchase($product);
        return $this->returnCoins();
    }

    public function setProducts(Product $product, int $count = 1): void
    {
        $this->setItemValues($product->name(), $count);
    }

    public function setCoins(Coin $coin, int $count = 1): void
    {
        $this->setItemValues($coin->name(), $count);
    }

    private function setItemValues(string $name, int $count): void
    {
        if ($count < 0) {
            throw new NotAllowedItemQuantity();
        }

        $this->checkIfItemIsAllowed($name);

        $this->setStateValue($name, $count);
    }

    private function addItem(string $name): void
    {
        $this->checkIfItemIsAllowed($name);

        $key = strtolower($name);
        if (array_key_exists($key, $this->state)) {
            $this->state[$key]++;
        } else {
            $this->setStateValue($key, 1);
        }
    }

    private function removeItem(string $name): void
    {
        $key = strtolower($name);
        if (array_key_exists($key, $this->state) && $this->getStateValue($key) > 0) {
            $this->state[$key]--;
        } else {
            throw new ProductNotAvailableException();
        }
    }

    private function cleanCoins(): void
    {
        $this->coins = [];
    }

    private function purchase(Product $product): void
    {
        $this->addMemento();

        // try to return the bigger coins available
        $coinValues = array_reverse(Coin::VALID_COIN_VALUES);

        foreach ($coinValues as $coinValue) {
            try {
                $this->removeItem($product->name());
                $this->setChange($product, $coinValue);
                return;
            } catch (NotEnoughChangeException $e) {
                $this->restoreLastMemento();
            }
        }

        throw new NotEnoughChangeException();
    }

    private function setChange(Product $product, float $bannedCoinValue): void
    {
        $value = $product->getChangeValue($this->calculateCoinsValue());

        $this->addUserCoinsToMachineState();

        $this->addCoinToChange(Coin::DOLLAR_TYPE, $value, $bannedCoinValue);
        $this->addCoinToChange(Coin::QUARTER_TYPE, $value, $bannedCoinValue);
        $this->addCoinToChange(Coin::DIME_TYPE, $value, $bannedCoinValue);
        $this->addCoinToChange(Coin::NICKEL_TYPE, $value, $bannedCoinValue);

        if ($value !== 0.00) {
            throw new NotEnoughChangeException();
        }
    }

    private function addMemento(): void
    {
        $this->memento[] = new VendingMachineMemento(
            $this->state,
            $this->coins,
        );
    }

    private function restoreLastMemento(): void
    {
        /** @var VendingMachineMemento $lastMemento */
        $lastMemento = end($this->memento);

        $this->state = $lastMemento->state();
        $this->coins = $lastMemento->coins();
    }

    private function calculateCoinsValue(): float
    {
        $total = 0.00;

        /** @var Coin $coin */
        foreach ($this->coins as $coin) {
            $total += $coin->value();
        }

        return $total;
    }

    private function addUserCoinsToMachineState(): void
    {
        /* @var Coin $coin */
        foreach ($this->coins as $key => $coin) {
            $this->addItem($coin->name());
            unset($this->coins[$key]);
        }
    }

    private function addCoinToChange(string $coinType, float &$value, float $bannedCoinValue): void
    {
        $coinValue = Coin::COIN_VALUES[$coinType];

        if ($coinValue > $bannedCoinValue) {
            return;
        }

        while ($value >= $coinValue && array_key_exists(strtolower($coinType), $this->state) && $this->getStateValue($coinType) > 0) {
            $this->removeItem($coinType);
            $this->coins[] = new Coin($coinValue);
            $value = round($value - $coinValue, 2);
        }
    }

    private function checkIfItemIsAllowed(string $name): void
    {
        if (!(in_array($name, Coin::VALID_COIN_TYPES) || in_array($name, Product::VALID_PRODUCT_TYPES))) {
            throw new ItemNotAllowedException();
        }
    }

    public function getStateValue(string $name): int
    {
        $key = strtolower($name);
        return $this->state[$key];
    }

    private function setStateValue(string $name, int $count): void
    {
        $key = strtolower($name);
        $this->state[$key] = $count;
    }
}
