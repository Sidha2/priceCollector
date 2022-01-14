<?php

declare(strict_types=1);

namespace Crypto\PriceCollector;

class Collector implements \JsonSerializable
{
   
    
    public function __construct()
    {

    }

    public function jsonSerialize(): array
    {
        
    }


    /**
     *  Fetch prices from Binance exchange
     */
    public function fetchBinancePairs($api, array $pairsToRead_Binance): array
    {        
        $ticker = $api->prices();
        $priceList = [];

        # associate prices to symbols from config
        foreach ($ticker as $symbol=>$price)
        {
            # build array of symbols and prices
            foreach ($pairsToRead_Binance as $searchedPair)
            {
                if ($searchedPair == $symbol) $priceList[$symbol] = $price;
            }
        }

        return $priceList;
    }


}