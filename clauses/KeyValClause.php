<?php

class KeyValClause {
    public $key;
    public $operator;
    public $value;
    public $placeholder;

    static $placeholderCounter = 0;
    
    public function __construct($key, $operator, $value) {
        $this->key = $key;
        $this->operator = $operator;
        $this->value = $value;
        
        //If value contains a . it can not be prepared with PDO so we use it directly as value
        // $this->placeholder = (strpos($value, ".") !== false)
        //     ? $value
        //     : ":".static::$placeholderCounter;

        //Fix for SQLServer!!!!!!!!!! Values have to be between ' '
        if(strpos($value, ".") !== false || strpos($value, "%") !== false) {  
            $this->placeholder = (gettype($value) == "string")
                ? "'" . $value . "'"
                : $value;
        } else {
            $this->placeholder = ":".static::$placeholderCounter;
        }

        static::$placeholderCounter++;
    }
}