<?php

class InsertQuery extends QueryBaseClass {

    public function insert($keyVals) {
        $sql = "INSERT INTO " . $this->table . " (";
        $sql .= implode(", ", array_keys($keyVals)) . ")";
        
        $sql .= " VALUES ";
        
        $sql .= "(";
        $keys = array_keys($keyVals);
        foreach($keys as &$key) {
            $key = ":".$key;
        }
        unset($val);
        
        $sql .= implode(", ", $keys);
        $sql .= ")";

        $this->sql = $sql;
        $this->keyVals = $keyVals;

        $prepared = $this->pdo->prepare($sql);
        // print_r($prepared);
        return $prepared->execute($keyVals);
    }
}
