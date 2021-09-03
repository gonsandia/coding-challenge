<?php

namespace Gonsandia\Ui;

use Gonsandia\Model\Coin;
use Gonsandia\Model\CoinValueNotAllowedException;
use Gonsandia\Model\NotAllowedItemQuantityException;
use Gonsandia\Model\NotEnoughChangeException;
use Gonsandia\Model\NotEnoughMoneyException;
use Gonsandia\Model\Product;
use Gonsandia\Model\ProductNotAvailableException;
use Gonsandia\Model\ProductValueNotAllowedException;
use Gonsandia\Model\VendingMachine;
use ItemNotAllowedException;

class Cli
{
    public const SERVICE_COMMAND = 'SERVICE';
    public const RETURN_COMMAND = 'RETURN-COIN';
    public const INSERT_COMMAND = [1, 0.25, 0.1, 0.05];
    public const SHUTDOWN_COMMAND = 'EXIT';
    public const GET_SODA_COMMAND = 'GET-SODA';
    public const GET_WATER_COMMAND = 'GET-WATER';
    public const GET_JUICE_COMMAND = 'GET-JUICE';

    private VendingMachine $vendingMachine;

    public function __construct()
    {
        $this->vendingMachine = new VendingMachine();

        $this->vendingMachine->setProducts(new Product('SODA'), 20);
        $this->vendingMachine->setProducts(new Product('WATER'), 20);
        $this->vendingMachine->setProducts(new Product('JUICE'), 20);

        $this->vendingMachine->setCoins(new Coin(1.00), 100);
        $this->vendingMachine->setCoins(new Coin(0.10), 100);
        $this->vendingMachine->setCoins(new Coin(0.25), 100);
        $this->vendingMachine->setCoins(new Coin(0.05), 100);
    }

    public function run()
    {
        $this->showHelp();

        while (true) {
            $input = readline("Command: ");

            $input = strtoupper(trim($input));

            if ($input === self::SHUTDOWN_COMMAND) {
                break;
            }

            $this->execute($input);
        }
        $this->bye();
    }

    public function execute(string $input): void
    {
        switch (true) {
            case $input === self::RETURN_COMMAND:
                $this->returnCoins();
                break;
            case $input === self::SERVICE_COMMAND:
                $this->doService();
                break;
            case in_array((float)$input, self::INSERT_COMMAND):
                $this->insertCoin((float)$input);
                break;
            case $input === self::GET_JUICE_COMMAND:
                $this->purchaseProduct('JUICE');
                break;
            case $input === self::GET_WATER_COMMAND:
                $this->purchaseProduct('WATER');
                break;
            case $input === self::GET_SODA_COMMAND:
                $this->purchaseProduct('SODA');
                break;
            default:
                echo 'INVALID COMMAND' . PHP_EOL;
        }
    }

    private function returnCoins()
    {
        $coins = $this->vendingMachine->returnCoins();
        $this->showCoins($coins);
    }

    private function insertCoin(float $coin)
    {
        $this->vendingMachine->insertMoney(new Coin($coin));
    }

    private function addNewProducts()
    {
        $name = readline("Name: ");
        $stock = (int)readline("Stock: ");

        try {
            $this->vendingMachine->setProducts(new Product($name), $stock);
            echo "$stock $name on stock" . PHP_EOL;
        } catch (ProductValueNotAllowedException | ItemNotAllowedException) {
            echo 'INVALID PRODUCT' . PHP_EOL;
        } catch (NotAllowedItemQuantityException) {
            echo 'INVALID AMOUNT' . PHP_EOL;
        }

        $input = readline("Add more (Y/N): ");

        $input = strtoupper(trim($input));

        if ($input === 'Y') {
            $this->addNewProducts();
        }
    }

    private
    function addCoins()
    {
        $value = readline("Value: ");
        $stock = (int)readline("Stock: ");

        try {
            $this->vendingMachine->setCoins(new Coin($value), $stock);
            echo "$stock coin of $value for change" . PHP_EOL;
        } catch (CoinValueNotAllowedException | ItemNotAllowedException) {
            echo 'INVALID PRODUCT' . PHP_EOL;
        } catch (NotAllowedItemQuantityException) {
            echo 'INVALID AMOUNT' . PHP_EOL;
        }

        $input = readline("Add more (Y/N): ");

        $input = strtoupper(trim($input));

        if ($input === 'Y') {
            $this->addCoins();
        }
    }

    private
    function doService(): void
    {
        $addNewProducts = readline('Update product stock (Y/N) ? ');

        $addNewProducts = strtoupper(trim($addNewProducts));

        if ($addNewProducts === 'Y') {
            $this->addNewProducts();
        }
        $updateChange = readline('Update coins stock (Y/N) ? ');

        $updateChange = strtoupper(trim($updateChange));

        if ($updateChange === 'Y') {
            $this->addCoins();
        }
    }

    private
    function showHelp(): void
    {
        echo str_repeat('*', 100) . PHP_EOL;
        echo 'Available Commands: ' . PHP_EOL;
        echo str_pad('Purchase WATER (0.65$):', 75) . self::GET_WATER_COMMAND . PHP_EOL;
        echo str_pad('Purchase JUICE (1$):', 75) . self::GET_JUICE_COMMAND . PHP_EOL;
        echo str_pad('Purchase SODA (1.5$):', 75) . self::GET_SODA_COMMAND . PHP_EOL;
        echo str_pad('Insert coins:', 75) . "(" . implode(', ', self::INSERT_COMMAND) . ")" . PHP_EOL;
        echo str_pad('Return coins:', 75) . self::RETURN_COMMAND . PHP_EOL;
        echo str_pad('Service:', 75) . self::SERVICE_COMMAND . PHP_EOL;
        echo str_pad('Shutdown machine:', 75) . self::SHUTDOWN_COMMAND . PHP_EOL;
        echo str_repeat('*', 100) . PHP_EOL;
    }

    private
    function bye(): void
    {
        echo str_repeat('*', 100) . PHP_EOL;
        echo "Bye Bye" . PHP_EOL;
        echo str_repeat('*', 100) . PHP_EOL;
    }

    private
    function purchaseProduct(string $string)
    {
        try {
            $coins = $this->vendingMachine->getProduct(new Product($string));
            echo $string . PHP_EOL;
            $this->showCoins($coins);
        } catch (ProductNotAvailableException) {
            echo 'PRODUCT NOT AVAILABLE' . PHP_EOL;
        } catch (NotEnoughChangeException) {
            echo 'THE MACHINE DONT HAVE COINS FOR RETURN CHANGE' . PHP_EOL;
        } catch (NotEnoughMoneyException) {
            echo 'NOT ENOUGH MONEY TO PURCHASE ' . $string . PHP_EOL;
        }
    }

    private
    function showCoins(array $coins): void
    {
        foreach ($coins as $coin) {
            echo $coin->value() . PHP_EOL;
        }
    }
}
