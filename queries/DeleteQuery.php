<?php

class DeleteQuery extends WhereQuery {
    
    public function exec() {
        $sql = "DELETE FROM " . $this->table;

        $sql = $this->createWhere($sql);

        $prepared = $this->pdo->prepare($sql);
        foreach ($this->where as $clause) {
            //Needed for joins. Parameters can't take a . (example: "Animals.user_id") | handled in KeyValClause
            if(substr($clause->placeholder, 0, 1) == ":") {
                $prepared->bindValue($clause->placeholder, $clause->value);
            }
        }
        $prepared->execute();
        $this->printErrorsWhenInDebug($prepared);
        return $prepared->rowCount();
    }
}