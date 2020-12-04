<?php


class UpdateQuery extends WhereQuery {
    
    private $keyValues = [];

    public function update($keyVals) {
        foreach($keyVals as $key => $val) {
            array_push($this->keyValues, new KeyValClause($key, "=", $val));
        }

        return $this;
    }

    public function exec() {
        $sql = "UPDATE " . $this->table . " SET ";

        $keyVals = $this->keyValues;
        foreach ($keyVals as $clause) {
            $sql .= $clause->key . "=" . $clause->placeholder . ",";
        }
        $sql = rtrim($sql, ','); //Remove last comma

        $sql = $this->createWhere($sql);
        $prepared = $this->pdo->prepare($sql);

        foreach ($this->keyValues as $clause) {
            if(substr($clause->placeholder, 0, 1) == ":") {
                $prepared->bindValue($clause->placeholder, $clause->value);
            }
        }

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