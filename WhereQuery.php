<?php

abstract class WhereQuery extends QueryBaseClass {

    protected $where = [];

    public function where($key, $operator, $value) {     
        array_push($this->where, new WhereClause($key, $operator, $value));
        return $this;
    }

    public function and() { 
        $this->addOperator("AND");
        return $this; 
    }
    public function or() { 
        $this->addOperator("OR");
        return $this; 
    }

    public function not() { 
        $this->addOperator("NOT");
        return $this;
    }

    private function addOperator($operator) {
        if(count($this->where) < 1) { return; }
        $this->where[count($this->where)-1]->afterCondition = $operator;
    }
    
    // protected abstract function createWhere(&$sql);
    function createWhere($sql) {
        $where = $this->where;
        if(!empty($where)) {
            $sql .= " WHERE ";
            for($i = 0; $i < count($where); $i++) {
                $sql .= $where[$i]->key . $where[$i]->operator . $where[$i]->placeholder;
                $afterCon = $where[$i]->afterCondition;
                if(!is_null($afterCon)) {
                    $sql .= " " . $afterCon . " ";
                }
            }
        }

        return $sql;
    }
}