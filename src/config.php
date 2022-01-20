<?php

/**
 *  DB login
 */
$dbhost = 'localhost';
$dbuser = 'db_user';
$dbpass = 'db_password';
$dbname = 'db_name';
$dbport = 3306;


# list of symbols to read from ticker
$pairsToRead = [

    "binance" => [
        "BTCUSDT",
        "ETHUSDT",
        "XMRUSDT"
    ],
    /*
    "bitfinex" => [
        "BTCUSD",
        "BTCUST",
    ],
    */
];
