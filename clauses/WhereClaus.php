<?php



class WhereClaus extends KeyValClaus {
    //AND - OR
    public $afterCondition = NULL;

    public function __construct($key, $operator, $value) {
        parent::__construct($key, $operator, $value);
    }
}