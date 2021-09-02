<?php

declare(strict_types=1);

namespace Gonsandia\Model;

class VendingMachineMemento
{
    private array $state;

    private array $coins;

    /**
     * @param array $state
     * @param array $coins
     */
    public function __construct(array $state, array $coins)
    {
        $this->state = $state;
        $this->coins = $coins;
    }

    /**
     * @return array
     */
    public function state(): array
    {
        return $this->state;
    }

    /**
     * @return array
     */
    public function coins(): array
    {
        return $this->coins;
    }
}
