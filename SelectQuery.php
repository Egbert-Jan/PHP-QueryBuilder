<?php

//Add support for other joins
//Add support for where and joins in same query
//Add MIN/MAX
//Add LIKE
class SelectQuery extends WhereQuery {

    private $selection = ["*"];
    private $joins = [];
    private $orderBys = [];
    private $limit = [];

    public function setColumns($selection) {
        $this->selection = $selection;
        return $this;
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
        $sql = $this->createWhere($sql);
        
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
        
        // echo $sql . "<br>";

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