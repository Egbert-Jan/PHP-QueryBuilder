<?php

class WhereClause extends KeyValClause {
    //AND - OR
    public $afterCondition = NULL;

    public function __construct($key, $operator, $value) {
        parent::__construct($key, $operator, $value);
    }
}