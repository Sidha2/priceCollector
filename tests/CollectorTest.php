<?php

declare(strict_types=1);

namespace Tests\Crypto\PriceCollector;

use Crypto\PriceCollector\Collector;
use PHPUnit\Framework\TestCase;

class CollectorTest extends TestCase
{
    public function testClassDataCanBeEncoded(): void
    {
        $this->assertEquals(
            '{"ticker":"BTC"}',
            json_encode(new Collector(['ticker' => 'BTC']))
        );
    }
}