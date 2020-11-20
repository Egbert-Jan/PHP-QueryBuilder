<?php

class InsertQuery extends QueryBaseClass {

    public function insert($keyVals) {
        global $pdo;
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

        $prepared = $pdo->prepare($sql);
        return $prepared->execute($keyVals);
    }
}
