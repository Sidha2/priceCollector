<?php

declare(strict_types=1);

namespace Crypto\PriceCollector;

class Collector implements \JsonSerializable
{
    public function __construct(private array $data)
    {
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}