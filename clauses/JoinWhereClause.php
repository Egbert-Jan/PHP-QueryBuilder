<?php

class JoinWhereClause extends WhereClause {
    public $table;

    public function __construct($table, $key, $operator, $value) {
        parent::__construct($key, $operator, $value);
        $this->table = $table;
    }
}