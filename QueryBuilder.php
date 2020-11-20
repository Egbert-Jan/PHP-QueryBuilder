<?php 

class QueryBuilder {
    private $table;
    private $pdo;
    
    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function select() {
        $selectQuery = new SelectQuery($this->pdo);
        $selectQuery->table = $this->table;
        return $selectQuery;
    }

    public function insert($keyVals) {
        $insertQuery = new InsertQuery($this->pdo);
        $insertQuery->table = $this->table;
        return $insertQuery->insert($keyVals);
    }
}
