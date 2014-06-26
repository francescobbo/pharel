# Pharel

Pharel is A Relational Algebra library for PHP. It is not simply inspired by [Arel](https://github.com/rails/arel), it is actually a Class-by-Class port of Arel to PHP made by a Rails lover constrained to work with PHP.

## Description

Pharel is just an Abstract Syntax Tree with a node per each SQL statement.

With all probabilities, it's not very confortable for everyday querying, as methods are quite verbose, but it lets you write your own ORM forgetting the complexity of query generation and database compatibility. You can still use it raw in your application, for input escaping or if you need to support many different DBMS.

## Why should you bother?

* Generates the 4 CRUD queries
* Prevents SQL injection through server escaping (if used correctly)
* Can handle arbitrarly large and complex queries (joins in joins in unions in joins in nested queries)
* Support all your favorite databases custom syntaxes:
    * MySQL
    * PostgreSQL
    * SQLite
    * Oracle, MSSQL and others to come

## Requirements

Due to use of traits in place of modules, Pharel requires PHP >= 5.4 to work, which also ships with the magnificent array shorthand syntax.

I could not resist the pulse to make a gem -- ehm, a package. So you can use [Composer](https://github.com/composer/composer) to install and use the library. Put

    {
        "require-dev": {
            "aomega08/pharel": "0.*"
        }
    }

in your composer.json file and run `composer install` or `php composer.phar install`, depending on your installation method.

If you don't have composer, or do not want to use it, you can just import the lib directory. Be sure to have a PSR-0 compliant autoloader (class `Pharel\Nodes\Test_Me` is in file `./Pharel/Nodes/Test/Me.php`), and enjoy.

## Examples

```php
$users = new Pharel\Table("users");
$query = $users->project(Pharel::sql("*"));

echo $query->to_sql();
// => SELECT * FROM `users`
```

```php
$users = new Pharel\Table("users");
$query = $users->project($users["id"]);

echo $query->to_sql();
// => SELECT `users`.`id` FROM `users`
```

```php
$users = new Pharel\Table("users");
$query = $users->project($users["age"]->average());

echo $query->to_sql();
// => SELECT AVG(`users`.`age`) AS avg_id FROM `users`
```

```php
$users = new Pharel\Table("users");
$query = $users->project(Pharel::sql("*"))->where($users["name"]->eq("amy"));

echo $query->to_sql();
// => SELECT * FROM `users` WHERE `users`.`name` = 'amy';
```

```php
$posts = new Pharel\Table("posts");
$users = new Pharel\Table("users");

$query = $posts
    ->project(Pharel::sql("*"))
    ->join("users")->on($posts["user_id"]->eq($users["id"])
    ->take(1);

echo $query->to_sql();
// => SELECT * FROM `posts` JOIN `users` ON `posts`.`user_id` = `users`.`id` LIMIT 1
```

```php
$users = new Pharel\Table("users");

$users->where($users["age"]->eq(10))              // WHERE `users`.`age` = 10
$users->where($users["age"]->not_eq(10))          // WHERE `users`.`age` != 10
$users->where($users["age"]->gt(10))              // WHERE `users`.`age` > 10
$users->where($users["age"]->gteq(10))            // WHERE `users`.`age` >= 10
$users->where($users["age"]->lt(10))              // WHERE `users`.`age` < 10
$users->where($users["age"]->lteq(10))            // WHERE `users`.`age` <= 10
$users->where($users["age"]->in([ 3, 10, 26 ]))   // WHERE `users`.`age` IN (3, 10, 26)
```

## Status

It is not unit tested and completely unstable. You should probably not use this, but please, contribute.

## License

Pharel is released under the terms of the [MIT License](https://github.com/aomega08/pharel/blob/master/LICENSE).