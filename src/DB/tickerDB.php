<?php

declare(strict_types=1);

namespace Crypto\PriceCollector\DB;
use Crypto\PriceCollector\DB\Db;

class tickerDB extends Db{
    
    private $db;
    private $dbName;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }


    /**
     *  Create Table
     */
    public function createTable($tableName): bool
    {
        $sql = "CREATE TABLE $tableName (
            id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            pair VARCHAR(50) NOT NULL,
            price VARCHAR(20) NOT NULL,
            updateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
        $query = $this->db->query($sql);
        
        if ($query->affectedRows() == 0) return true;     # tab created successful, -1 = false
        return false;
    }


    /**
     *  Update price in existing pair
     */
    public function update(string $pair, string $price)
    {
        $query = $this->db->query("UPDATE $this->dbName SET price = ? WHERE pair = ?", $price, $pair);

        if ($query->connection->affected_rows > 0) return true;
        return false;
    }


    /**
     *  Insert new price to ticker table
     */
    public function insert(string $pair, string $price)
    {
        $query = $this->db->query("INSERT INTO $this->dbName (pair, price) VALUES (?, ?)", $pair, $price);
        if ($query->connection->affected_rows > 0) return true;
        return false;
    }

    /**
     *  Select all culumns from ticker
     */
    public function select(string $pair)
    {
        $query = $this->db->query("SELECT * FROM $this->dbName WHERE pair = ?", $pair);
        if ($query->connection->error) return false;
        
        $query = $query->fetchArray();
        if(!empty($query)) return $query;
        return false;
    }

    /**
     *  Get query errors
     */
    public function getQueryErrorList()
    {        
        return $this->db->query->error_list;
    }

    /**
     *  Get connection errors
     */
    public function getConnectionErrorList()
    {        
        return $this->db->connection->error_list;
    }

    /**
     *  Get connection error as string only
     */
    public function getConnectionError()
    {        
        return $this->db->connection->error;
    }

    public function connectionToDBError()
    {
        return $this->db->connection->connect_error;
    }


    /**
     * Set the value of dbName
     *
     * @return  self
     */ 
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;

        return $this;
    }
}
