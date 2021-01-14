<?php
declare(strict_types=1);


namespace App;


class SQLStatement
{

    private Database|null $database;

    private string $table;
    private string $statement;
    private array $attributes;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function select (string|array $attributes) {
        $statement = 'select';

        if ('string' === gettype($attributes)) $attributes = [$attributes];

        $this->statement = 'select';
        $this->attributes = $attributes;

        return (new SQLClause($this->table))
            ->setDatabase($this->database)
            ->setAttributes($attributes)
            ->setStatement($statement);
    }

    public function setDatabase(Database|null $database) {
        $this->database = $database;
        return $this;
    }

}