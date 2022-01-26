<?php

declare(strict_types=1);

namespace Crypto\PriceCollector\Brokers;
use Crypto\PriceCollector\DB\Db;
use \Binance\API;


class BinanceBroker {

    /**
     *  OrderStatus should return array
     *  [
     *      'symbol'           # BTCUSD
     *       'orderId'         # 7652393
     *       'transactTime'    # 1508564815865
     *       'price'           # 0.00000000
     *       'origQty'         # 1.00000000
     *       'executedQty'     # 1.00000000
     *       'status'          # FILLED
     *       'side'            # BUY / SELL
     *   ]
     */
    private $orderStatus = [];

    /**
     *  OrderError should return array
     *  [
     *      'msg'               # error msg from exchange
     *  ]
     */
    private $orderError  = [];
    
    private $api;               # Binance API 

    /**
     *  Constructor
     *  $api - Binance API 
     */
    public function __construct(API $api) 
    {
        $this->api = $api;
    }


    /**
     *  Market Buy
     * 
     * @param $symbol string the currency symbol
     * @param $quantity string the quantity required
     * @return bool order was successful or not
     */
    public function marketBuy(string $symbol, string $quantity): bool 
    {
        return $this->market('BUY', $symbol, $quantity);
    }


    /**
     *  Market Sell
     * 
     * @param $symbol string the currency symbol
     * @param $quantity string the quantity required
     * @return bool order was successful or not
     */
    public function marketSell(string $symbol, string $quantity) 
    {
        return $this->market('SELL', $symbol, $quantity);
    }


    /**
     *  Market Buy / Sell
     * 
     * @param $side string "BUY" or "SELL
     * @param $symbol string the currency symbol
     * @param $quantity string the quantity required
     * @return bool order was successful or not
     * 
     *  TODO: make this method private
     * 
     */
    public function market(string $side, string $symbol, string $quantity): bool 
    {
        # execute order on exchange
        switch ($side) {
            case 'BUY':
                $order = $this->api->marketBuy($symbol, $quantity);
                break;

            case 'SELL':
                $order = $this->api->marketSell($symbol, $quantity);
                break;

            default:
                $this->setOrderError('Wrong SIDE parameter. Please use "BUY" or "SELL"');
                return false;
                break;
        }
        
        # order was processed
        if (isset($order['status']))
        {
            $this->setOrderStatus(
                [
                    'symbol'        => $order['symbol'],        # BTCUSD
                    'orderId'       => $order['orderId'],       # 7652393
                    'transactTime'  => $order['transactTime'],  # 1508564815865
                    'price'         => $order['price'],         # 0.00000000
                    'origQty'       => $order['origQty'],       # 1.00000000
                    'executedQty'   => $order['executedQty'],   # 1.00000000
                    'status'        => $order['status'],        # FILLED
                    'side'          => $order['side'],          # BUY
                ]
            );

            # order was filled
            if ($order['status'] === 'FILLED') return true; 

            # order failed
            return false;
        }
        
        # else some error occurred
        $this->setOrderError(['msg' => '$order']);

        return false;
    }


    /**
     * Get the value of orderStatus
     */ 
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * Set the value of orderStatus
     *
     * @return  self
     */ 
    private function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get the value of orderError
     */ 
    public function getOrderError()
    {
        return $this->orderError;
    }

    /**
     * Set the value of orderError
     *
     * @return  self
     */ 
    private function setOrderError($orderError)
    {
        $this->orderError = $orderError;

        return $this;
    }
}
