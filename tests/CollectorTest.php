<?php

declare(strict_types=1);

namespace Tests\Crypto\PriceCollector;

use Crypto\PriceCollector\Collector;
use Crypto\PriceCollector\DB\TickerDB;
use PHPUnit\Framework\TestCase;
use \Binance\API;

class CollectorTest extends TestCase
{  
    
    /**
     *  Setup
     */
    public function setUp() : void
    {
        $this->pairsToRead = [
            "BTCUSDT",
            "ETHUSDT",
            "XMRUSDT"
        ];

        $this->pairsToReadFull = [

            "binance" => [
                "BTCUSDT",
                "ETHUSDT",
                "XMRUSDT"
            ],
        ];

        $this->apiBinance = $this->createMock(API::class);
        $this->tickerDB = $this->createMock(TickerDB::class);
        $this->collector = new Collector($this->apiBinance, $this->tickerDB, $this->pairsToReadFull);

        

        $this->binanceTicker = 
        [
            'BTCUSDT'    => '41992.73000000',            
            'ETHUSDT'    => '3216.77000000',
            'XMRUSDT'    => '218.80000000',
            'xxxxxx'    => '1111',
        ];

        $this->apiBinance->method('prices')->willReturn($this->binanceTicker);
    }


    /**
     *  Test if method return empty array if bad exchange name was added
     */
    public function testFetchPairsReturnEmptyAfterBadExchange(): void 
    {
        $pairs = $this->pairsToRead;
        $this->assertEquals(
            [], ((new Collector($this->apiBinance, $this->tickerDB, $this->pairsToReadFull))->fetchPairs('bitfinex', $pairs))
        );
    }


    /**
     *  Test if get pairs after right argument was set
     */
    public function testFetchRightExchangeData(): void
    {
        $expect = 
        [
            'BTCUSDT' => '41992.73000000',
            'ETHUSDT' => '3216.77000000',
            'XMRUSDT' => '218.80000000',
        ];   

        $this->assertEquals(
            $expect,
            ((new Collector($this->apiBinance, $this->tickerDB, $this->pairsToReadFull))->fetchPairs('binance', $this->pairsToRead))
        );
    }


    /**
     *  Test fetch pairs
     */
    public function testFetchBinancePairs(): void 
    {
        $expect = 
        [
            'BTCUSDT' => '41992.73000000',
            'ETHUSDT' => '3216.77000000',
            'XMRUSDT' => '218.80000000',
        ];

        $this->assertEquals(
            $expect,
            ((new Collector($this->apiBinance, $this->tickerDB, $this->pairsToReadFull))->fetchBinancePairs($this->pairsToRead))
        );
        
    }


    /**
     *  Test if ticker update price
     */
    public function testRunUpdatePrice(): void
    {
        $this->tickerDB->method('setDbName')->willReturn('binance');
        $this->tickerDB->method('connectionToDBError')->willReturn(null);
        // $this->collector->method('fetchPairs')->willReturn($this->binanceTicker);
        $this->tickerDB->method('update')->willReturn(true);

        (new Collector($this->apiBinance, $this->tickerDB, $this->pairsToReadFull))->run();
        $this->expectOutputString('Pairs updated successful!');
    }


    /**
     *  Test if ticker don't find pair
     */
    public function testRunDontFindPair(): void
    {
        $pairsToReadFull = [

            "binance" => [
                "BTCUSDT",
            ],
        ];

        $this->pairsToRead = [
            "BTCUSDT",
        ];

        $this->tickerDB->method('setDbName')->willReturn('binance');
        $this->tickerDB->method('connectionToDBError')->willReturn(null);
        $this->tickerDB->method('update')->willReturn(false);
        
        (new Collector($this->apiBinance, $this->tickerDB, $pairsToReadFull))->run();
        $this->expectOutputString(
            "Nepodarilo sa najst par 'BTCUSDT'\n".
            "Nepodarilo sa vytvorit par 'BTCUSDT'\n".
            "Pairs updated successful!"
         );
    }

    /**
     *  Test insert new pair
     */
    public function testRunInsertNewPair(): void
    {
        $pairsToReadFull = [

            "binance" => [
                "BTCUSDT",
            ],
        ];

        $this->pairsToRead = [
            "BTCUSDT",
        ];

        $this->tickerDB->method('setDbName')->willReturn('binance');
        $this->tickerDB->method('connectionToDBError')->willReturn(null);
        $this->tickerDB->method('select')->willReturn(false);
        $this->tickerDB->method('update')->willReturn(false);
        $this->tickerDB->method('insert')->willReturn(true);
        //$this->tickerDB->method('createTable')->willReturn(false);

        (new Collector($this->apiBinance, $this->tickerDB, $pairsToReadFull))->run();
        $this->expectOutputString(
            "Nepodarilo sa najst par 'BTCUSDT'\n".
            "Vytvoril som novy par 'BTCUSDT' a pridadil mu cenu: 41992.73000000\n".
            "Pairs updated successful!"
         );
    }

    /**
     *  Test Create table
     */
    public function testRunDontFindTableThenCreateNewTable(): void
    {
        $pairsToReadFull = [

            "binance" => [
                "BTCUSDT",
            ],
        ];

        $this->pairsToRead = [
            "BTCUSDT",
        ];

        $this->tickerDB->method('setDbName')->willReturn('binance');
        $this->tickerDB->method('connectionToDBError')->willReturn(null);
        $this->tickerDB->method('select')->willReturn(false);
        $this->tickerDB->method('update')->willReturn(false);
        $this->tickerDB->method('insert')->willReturn(false);
        $this->tickerDB->method('createTable')->willReturn(true);
        //$this->tickerDB->method('createTable')->willReturn(false);

        (new Collector($this->apiBinance, $this->tickerDB, $pairsToReadFull))->run();
        $this->expectOutputString(
            "Nepodarilo sa najst par 'BTCUSDT'\n".
            "Nepodarilo sa vytvorit par 'BTCUSDT'\n".
            "Vytvoril som tabulku 'binance'\n\n".
            "Pairs updated successful!"
         );
    }
}