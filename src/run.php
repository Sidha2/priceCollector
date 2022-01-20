<?php

declare(strict_types=1);


// require 'vendor/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

/**
 *  Include config file
 */ 
include("config.php");

use Crypto\PriceCollector\Collector;
use Crypto\PriceCollector\DB\TickerDB;
use Crypto\PriceCollector\DB\Db;
use \Binance\API;


$db         = new Db($dbhost, $dbuser, $dbpass, $dbname, 3306);     # connection do DB
$apiBinance = new API('', '');                                      # API keys no needed
$tickerDB   = new TickerDB($db);                                    # work with DB
$collector  = new Collector($apiBinance, $tickerDB, $pairsToRead);  # pairsToRead set in config.php
$collector->setCycle(true)->setSleep(5);                            # set up collector to loop with sleep time

# RUN price collector
$collector->run();
