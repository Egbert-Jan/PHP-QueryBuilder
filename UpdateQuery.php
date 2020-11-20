<?php


class UpdateQuery extends WhereQuery {

    //UPDATE Customers
    //SET ContactName = 'Alfred Schmidt', City= 'Frankfurt'
    //WHERE CustomerID = 1;

    private $keyValues = [];

    public function set($keyVals) {
        $this->keyValues = $keyVals;
        return $this;
    }

    public function exec() {
        $sql = "UPDATE " . $this->table . " SET ";

        $keys = array_keys($this->keyValues);
        foreach($keys as &$key) {
            $sql .= $key . " = :".$key . ",";
        }
        unset($val);
        $sql = rtrim($sql, ','); //Remove last comma

        $sql = $this->createWhere($sql);

        $prepared = $this->pdo->prepare($sql);

        //NIET GOED ALS 2 DE ZELFDE PARAMETERS ZIJN
        foreach ($keys as $key) {
            $prepared->bindValue(":".$key, $this->keyValues[$key]);
        }

        foreach ($this->where as $clause) {
            $prepared->bindValue($clause->placeholder, $clause->value);
        }
        // print_r($prepared);
        return $prepared->execute();
    }

}