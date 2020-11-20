<?php

//Add support for other joins
//Add support for where and joins in same query
//Add MIN/MAX
//Add LIKE
class SelectQuery extends QueryBaseClass {

    private $selection = ["*"];
    private $where = [];
    private $joins = [];
    private $orderBys = [];
    private $limit = [];

    public function setColumns($selection) {
        $this->selection = $selection;
        return $this;
    }

    public function where($key, $operator, $value) {     
        array_push($this->where, new WhereClaus($key, $operator, $value));
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

    public function count($column = "id") { $this->selection = ["COUNT(" . $column . ")"]; return $this; }
    public function average($column = "id") { $this->selection = ["AVG(" . $column . ")"]; return $this; }
    public function sum($column = "id") { $this->selection = ["SUM(" . $column . ")"]; return $this; }

    public function join($table, $key, $operator, $value) {
        $joinClaus = new JoinWhereClaus($table, $key, $operator, $value);
        array_push($this->joins, $joinClaus);
        return $this;
    }

    //ORDER BY Country ASC, CustomerName DESC;
    public function orderBy($column, $operator = "ASC") {
        if($operator == "<" || $operator == "ASC") {
            $this->orderBys[$column] = "ASC";
        } else if ($operator == ">" || $operator == "DESC") {
            $this->orderBys[$column] = "DESC";
        }

        return $this;
    }

    public function limit($limit, $offset = null) {
        $this->limit = [$limit, $offset];
        return $this;
    }

    private function buildQuery() {

        $sql = "SELECT " . implode(", ", $this->selection) . " FROM " . $this->table;

        $joins = $this->joins;
        if(!empty($joins)) {
            for($i = 0; $i < count($joins); $i++) {
                $sql .= " INNER JOIN ";
                $sql .=  $joins[$i]->table . " ON " .$joins[$i]->key . $joins[$i]->operator . $joins[$i]->placeholder;
            }
        }
        
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
        
        $orderBys = $this->orderBys;
        if(!empty($orderBys)) {
            $sql .= " ORDER BY ";

            foreach($orderBys as $key => $value) {
                $sql .= $key . " " . $value;
            }
        }

        $limit = $this->limit;
        if(!empty($limit)) {
            if(!is_null($limit[1])) {
                $sql .= " LIMIT " . $limit[1].", ".$limit[0];
            } else {
                $sql .= " LIMIT " . $limit[0];
            }
        }
        
        echo $sql . "<br>";

        //Een van de twee moet altijd null zijn
        $whereOrJoin = array_merge($where, $joins);
        $prepared = $this->pdo->prepare($sql);
        foreach ($whereOrJoin as $claus) {
            $prepared->bindValue($claus->placeholder, $claus->value);
        }

        $prepared->execute();
        return $prepared;
    }

    public function get() {
        $preparedQuery = $this->buildQuery();
        return $preparedQuery->fetch();
    }

    public function getAll() {
        $preparedQuery = $this->buildQuery();
        return $preparedQuery->fetchAll();
    }
}