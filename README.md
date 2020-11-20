# Example

## index.php
```php
include "Autoloader.php";

$pdo = new PDO('mysql:host=localhost;dbname=database', "root", "root");
```

## Select
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

```php
$result = (new SelectQuery($pdo))
    ->table("Users")
    ->join("Products", "Products.id", "=", 1)
    ->join("Category", "Users.id", "=", "Category.user_id")
    ->setColumns(["Users.name", "Products.name"])
    ->get();
```

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