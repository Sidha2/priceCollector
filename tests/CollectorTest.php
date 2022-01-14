<?php

declare(strict_types=1);

namespace Tests\Crypto\PriceCollector;

use Crypto\PriceCollector\Collector;
use PHPUnit\Framework\TestCase;
use Binance\API;

class CollectorTest extends TestCase
{  

    public function testFetchBinancePairs(): void 
    {

        $pairsToRead_Binance = [
            "BTCUSDT",
            "ETHUSDT",
            "XMRUSDT"
        ];

        $input = 
        [
            'BTCUSDT'    => '41992.73000000',            
            'ETHUSDT'    => '3216.77000000',
            'XMRUSDT'    => '218.80000000',
            'xxxxxx'    => '1111',
        ];        

        $expect = 
        [
            'BTCUSDT' => '41992.73000000',
            'ETHUSDT' => '3216.77000000',
            'XMRUSDT' => '218.80000000',
        ];

        $mock = $this->createMock(API::class);
        $mock->method('prices')->willReturn($input);

        $this->assertEquals(
            $expect,
            (new Collector())->fetchBinancePairs($mock, $pairsToRead_Binance)
        );
        
    }

}