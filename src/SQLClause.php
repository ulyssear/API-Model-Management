<?php
declare(strict_types=1);


namespace App;


use PDO;

class SQLClause
{

    private Database|null $database;

    private string $table;
    private string|null $statement;
    private array|null $attributes;
    private array|null $where;
    private string|null $orderBy;
    private int|null $limit;
    private int|null $offset;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function where(array $parameters)
    {

        $newParameters = [];

        if ('array' !== $parameters[0]) {
            $newParameters[] = $this->parseWhereParameter($parameters);
            $this->where = $newParameters;

            return $this;
        }


        foreach ($parameters as $parameter) {
            $newParameters[] = $this->parseWhereParameter($parameter);
        }

        $this->where = $newParameters;

        return $this;
    }

    public function orderBy(int $orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function get()
    {

        $table = $this->table;
        $attributes = $this->attributes;
        $where = $this->where ?? [];
        $orderBy = $this->orderBy ?? 'ASC';
        $limit = $this->limit ?? null;
        $offset = $this->offset ?? null;

        $attributes = implode(',', $attributes);

        $where = array_map(fn($entry) => implode('', $entry), $where);
        $where = implode(',', $where);

        $query = "SELECT $attributes ";
        $query .= "FROM $table ";
        if ('' !== $where) $query .= "WHERE $where ";
        if (null !== $orderBy) $query .= "ORDER BY '$orderBy' ";
        if (null !== $limit) $query .= "LIMIT $limit ";
        if (null !== $offset) $query .= "OFFSET $offset ";

        $result = $this->database->getPDO()->query($query)->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function first()
    {
        return $this->limit(1)->offset(0)->get()[0];
    }

    public function setAttributes(array|null $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setStatement(string|null $statement)
    {
        $this->statement = $statement;
        return $this;
    }

    public function setDatabase(Database|null $database)
    {
        $this->database = $database;
        return $this;
    }


    private function parseWhereParameter(array $parameter)
    {

        // Format ['attribut', 'opérateur', 'valeur']
        if (3 === count($parameter)) {
            // TODO: Check si opérateur valide
            // TODO: Check si attribut valide
            // TODO: Check si valeur valide

            return $parameter;
        }

        // Format ['attribut', 'valeur'], on suppose que l'opérateur est "="
        if (2 === count($parameter)) {
            // TODO: Check si attribut valide
            // TODO: Check si valeur valide

            $parameter = [$parameter[0], '=', $parameter[1]];

            return $parameter;
        }

        // On retourne une erreur sur le format de $parameter :
        // il doit avoir 2 ou 3 items dans le tableau (cf en haut)

        return null;
    }


}