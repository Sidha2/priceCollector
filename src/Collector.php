<?php

declare(strict_types=1);

namespace Crypto\PriceCollector;

class Collector implements \JsonSerializable
{
    public $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}