<?php

/**
 *  DB login
 */
$dbhost = 'localhost';
$dbuser = 'db_user';
$dbpass = 'db_password';
$dbname = 'db_name';
$dbport = 3306;

/**
 *  Ticker BINANCE Create table Query
 */
$createBinanceTicker = "CREATE TABLE binance (
    id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pair VARCHAR(50) NOT NULL,
    price VARCHAR(20) NOT NULL,
    updateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

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
