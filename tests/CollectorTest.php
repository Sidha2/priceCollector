<?php

declare(strict_types=1);

namespace Tests\Crypto\PriceCollector;

use Crypto\PriceCollector\Collector;
use PHPUnit\Framework\TestCase;


class CollectorTest extends TestCase
{  
    
    public function setUp() : void
    {
        $this->apiBinance = $this->createMock(\Binance\API::class);
        $this->Collector = new Collector($this->apiBinance);

        $this->pairsToRead = [
            "BTCUSDT",
            "ETHUSDT",
            "XMRUSDT"
        ];

        $this->binanceTicker = 
        [
            'BTCUSDT'    => '41992.73000000',            
            'ETHUSDT'    => '3216.77000000',
            'XMRUSDT'    => '218.80000000',
            'xxxxxx'    => '1111',
        ];

        $this->apiBinance->method('prices')->willReturn($this->binanceTicker);
    }


    public function testFetchPairsReturnEmptyAfterBadExchange(): void 
    {
        $pairs = $this->pairsToRead;
        $this->assertEquals(
            [], ((new Collector($this->apiBinance))->fetchPairs('bitfinex', $pairs))
        );
    }



    public function testFetchRightExchangeData(): void
    {
        $expect = 
        [
            'BTCUSDT' => '41992.73000000',
            'ETHUSDT' => '3216.77000000',
            'XMRUSDT' => '218.80000000',
        ];   
        $pairs = $this->pairsToRead;

        $this->assertEquals(
            $expect,
            ((new Collector($this->apiBinance))->fetchPairs('binance', $pairs))
        );
    }


    public function testFetchBinancePairs(): void 
    {
        $expect = 
        [
            'BTCUSDT' => '41992.73000000',
            'ETHUSDT' => '3216.77000000',
            'XMRUSDT' => '218.80000000',
        ];

        // $mock = $this->createMock(API::class);
        // $mock->method('prices')->willReturn($this->binanceTicker);
        $pairs = $this->pairsToRead;

        $this->assertEquals(
            $expect,
            ((new Collector($this->apiBinance))->fetchBinancePairs($pairs))
        );
        
    }

}