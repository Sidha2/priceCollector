<?php

declare(strict_types=1);

namespace Crypto\PriceCollector;
use Crypto\PriceCollector\DB\tickerDB;
use \Binance\API;

class Collector implements \JsonSerializable
{
   
    private $apiBinance;        # Binance api
    private $cycle = false;     # run on/off cycle
    private $sleep = 5;         # sleeptime of cycle
    private $pairsToRead;       # pairs from config.php
    private $tickerDB;          # work with ticker DB
    
    public function __construct(API $apiBinance, tickerDB $tickerDB, array $pairsToRead)
    {
        $this->apiBinance               = $apiBinance;            
        $this->apiBinance->caOverride   = true;
        $this->tickerDB                 = $tickerDB;
        $this->pairsToRead              = $pairsToRead;
    }

    public function jsonSerialize(): array
    {
        
    }


    /**
     *  chose right exchange ticker
     *  TODO change this to private
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
     *  TODO change this to private
     */
    public function fetchBinancePairs($pairsToRead): array
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


    /**
     *  Run ticker
     */
    public function run()
    {
        $cycle = true;

        # run this block atleast once
        while ($cycle === true )
        {

            foreach ($this->pairsToRead as $exchange => $tickerPairs)
            {                    

                $this->tickerDB->setDbName($exchange);

                # connection to DB was not successfull
                if ($this->tickerDB->connectionToDBError()) {
                    echo 'Failed to connect to MySQL - ' . $tickerDB->connectionToDBError();
                    break;
                }

                # read all prices from exchange
                $ticker = $this->fetchPairs($exchange, $tickerPairs);

                # if something goes wrong ticker is empty, for example, exchange is in maintenance
                if (empty($ticker))
                {
                    echo "Ticker z burzy '$exchange' sa nepodarilo nacitat.". PHP_EOL;
                    continue;
                }

                # main update pairs and prices to DB
                foreach ($ticker as $pair => $price)
                {
                    # try to update price
                    $updatePrice = $this->tickerDB->update($pair, $price);
                    if ($updatePrice !== true)
                    {
                        # if pair don't exist, create it, otherwise the new price is same as old price
                        $searchPair = $this->tickerDB->select($pair);
                        if(!$searchPair)
                        {
                            echo "Nepodarilo sa najst par '$pair'". PHP_EOL;

                            # if can't insert pair, need to create table
                            $insertPair = $this->tickerDB->insert($pair, $price);
                            if(!$insertPair)
                            {
                                echo "Nepodarilo sa vytvorit par '$pair'". PHP_EOL;

                                $createTable = $this->tickerDB->createTable($exchange);
                                if(!$createTable)
                                {
                                    echo $this->tickerDB->getConnectionError();
                                }
                                else
                                {
                                    echo "Vytvoril som tabulku '$exchange'". PHP_EOL;

                                    $insertPairTo = $this->tickerDB->insert($pair, $price);
                                    if(!$insertPair)
                                    {
                                        echo $this->tickerDB->getConnectionError(). PHP_EOL;
                                    }
                                    else
                                    {
                                        echo "Vytvoril som novy par '$pair' a pridadil mu cenu: $price". PHP_EOL;
                                    }
                                }
                            }
                            else
                            {
                                echo "Vytvoril som novy par '$pair' a pridadil mu cenu: $price". PHP_EOL;
                            }
                        }
                    }
                }
                
            } 

            # turn on/off cycle
            $cycle = $this->cycle;

            if ($cycle == true)
            {
                echo "Pairs updated successful! Starting new cycle... (wait: ".$this->sleep."s)" .PHP_EOL;
                sleep($this->sleep);
            }  
            else
            {
                echo "Pairs updated successful!";
            }        

            
        }

    }



    /**
     * Get the value of cycle
     */ 
    public function getCycle()
    {
        return $this->cycle;
    }

    /**
     * Set the value of cycle
     *
     * @return  self
     */ 
    public function setCycle($cycle)
    {
        $this->cycle = $cycle;

        return $this;
    }

    /**
     * Get the value of sleep
     */ 
    public function getSleep()
    {
        return $this->sleep;
    }

    /**
     * Set the value of sleep
     *
     * @return  self
     */ 
    public function setSleep($sleep)
    {
        $this->sleep = $sleep;

        return $this;
    }
}