<?php

declare(strict_types=1);

namespace Crypto\PriceCollector;

class Collector implements \JsonSerializable
{
   
    private $apiBinance;
    
    public function __construct(\Binance\API $apiBinance)
    {
        $this->apiBinance = $apiBinance;            
        $this->apiBinance->caOverride = true;
    }

    public function jsonSerialize(): array
    {
        
    }


    /**
     *  chose right exchange ticker
     */
    public function fetchPairs($exchange, $pairs): array
    {
        switch ($exchange) {
            case 'binance':
                return $this->fetchBinancePairs($pairs);
                break;
            
            default:
                return [];
                break;
        }
    }


    /**
     *  Fetch prices from Binance exchange
     */
    public function fetchBinancePairs(array $pairsToRead): array
    {        
        $ticker = $this->apiBinance->prices();
        $priceList = [];

        # associate prices to symbols from config
        foreach ($ticker as $symbol=>$price)
        {
            # build array of symbols and prices
            foreach ($pairsToRead as $searchedPair)
            {
                if ($searchedPair == $symbol) $priceList[$symbol] = $price;
            }
        }

        return $priceList;
    }


}