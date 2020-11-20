<?php



class DeleteQuery extends WhereQuery {
    
    public function execute() {
        //DELETE FROM Customers WHERE CustomerName='Alfreds Futterkiste';
        $sql = "DELETE FROM " . $this->table;

        $sql = $this->createWhere($sql);

        $prepared = $this->pdo->prepare($sql);
        foreach ($this->where as $claus) {
            $prepared->bindValue($claus->placeholder, $claus->value);
        }

        return $prepared->execute();
    }
}