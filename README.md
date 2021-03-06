# Examples

## index.php
```php
include "Autoloader.php";

$pdo = new PDO('mysql:host=localhost;dbname=database', "root", "root");
```

## Select
### 1.
```php
$result = (new SelectQuery($pdo))
    ->table("Users")
    ->where("id", "=", 11)
    ->or()
    ->where("id", "=", 12)
    ->orderBy("Name", ">") 
    ->limit(2, 1)
    ->getAll();
```

### 2.
```php
$result = (new SelectQuery($pdo))
    ->table("Users")
    ->setColumns(["Users.name", "Users.email", "Animals.name"])
    ->join("Animals", "Users.id", "=", "Animals.user_id")
    ->where("Animals.age", ">", "4")
    ->getAll();
```
Returns an array with retrieved data depending on the PDO settings

## Insert
```php
$result = (new InsertQuery($pdo))
    ->table("Users")
    ->insert([
        "name" => "Egbert",
        "age" => 20, 
        "email" => "mail@example.com"
    ]);
```
Returns amount of rows inserted by this query (0 or 1)


## Update
```php
$result = (new UpdateQuery($pdo))
    ->table("Users")
    ->update(["name" => "IkBenCool"])
    ->where("id", "=", 20)
    ->or()
    ->where("id", "=", 31)
    ->exec();
```
Returns amount of rows updated by this query

## Delete
```php
$result = (new DeleteQuery($pdo))
    ->table("Users")
    ->where("id", "=", 11)
    ->or()
    ->where("id", "=", 16)
    ->exec();
```
Returns amount of rows deleted by this query