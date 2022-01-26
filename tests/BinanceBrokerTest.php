<?php

declare(strict_types=1);

namespace Tests\Crypto\PriceCollector;
use Crypto\PriceCollector\Brokers\BinanceBroker;
use PHPUnit\Framework\TestCase;
use \Binance\API;

class BinanceBrokerTest extends TestCase
{  
    
    /**
     *  Setup
     */
    public function setUp() : void
    {
        
        $this->apiBinance = $this->createMock(API::class);

        

        $this->orderBuySuccessOutput = 
        [
            'symbol'        => 'BTCUSD',            
            'orderId'       => '7652393',
            'transactTime'  => '1508564815865',
            'price'         => '39000',
            'origQty'       => '1.00000000',
            'executedQty'   => '1.00000000',
            'status'        => 'FILLED',
            'side'          => 'BUY',
        ];

        $this->orderSellSuccessOutput = 
        [
            'symbol'        => 'BTCUSD',            
            'orderId'       => '7652393',
            'transactTime'  => '1508564815865',
            'price'         => '39000',
            'origQty'       => '1.00000000',
            'executedQty'   => '1.00000000',
            'status'        => 'FILLED',
            'side'          => 'SELL',
        ];

        
    }


    
    /**
     *  Test if Market buy failed
     */
    public function testMarketBuyFailed(): void 
    {
        $this->apiBinance->method('marketBuy')->willReturn(['error' => 'test error']);
        $this->assertFalse((new BinanceBroker($this->apiBinance))->market("BUY", "BTCUSD", "1"));
    }

    /**
     *  Test if Market buy is success
     */
    public function testMarketBuySuccess(): void 
    {
        $this->apiBinance->method('marketBuy')->willReturn($this->orderBuySuccessOutput);
        $this->assertTrue((new BinanceBroker($this->apiBinance))->market("BUY", "BTCUSD", "1"));
    }

    /**
     *  Test if Market sell faild
     */
    public function testMarketSellFailed(): void 
    {
        $this->apiBinance->method('marketSell')->willReturn(['error' => 'test error']);
        $this->assertFalse((new BinanceBroker($this->apiBinance))->market("SELL", "BTCUSD", "1"));
    }

    /**
     *  Test if Market sell is success
     */
    public function testMarketSellSuccess(): void 
    {
        $this->apiBinance->method('marketSell')->willReturn($this->orderSellSuccessOutput);
        $this->assertTrue((new BinanceBroker($this->apiBinance))->market("SELL", "BTCUSD", "1"));
    }


    

}