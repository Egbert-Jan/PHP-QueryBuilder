<?php

class DeleteQuery extends WhereQuery {
    
    public function exec() {
        $sql = "DELETE FROM " . $this->table;

        $sql = $this->createWhere($sql);

        $prepared = $this->pdo->prepare($sql);
        foreach ($this->where as $clause) {
            $prepared->bindValue($clause->placeholder, $clause->value);
        }
        // print_r($prepared);
        $prepared->execute();
        return $prepared->rowCount();
    }
}