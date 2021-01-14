<?php
declare(strict_types=1);


namespace App;


use PDO;

class Database
{

    private ?PDO $pdo;

    public function __construct(array $parameters)
    {

        $host = $parameters['host'] ?? null;
        $database = $parameters['database'] ?? null;
        $charset = $parameters['charset'] ?? null;
        $username = $parameters['username'] ?? null;
        $password = $parameters['password'] ?? null;

        $options = array(
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        $this->pdo = null;
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$database;charset=$charset", $username, $password, $options);
        } catch (\Exception $exception) {
            // Do nothing ?
        }
    }

    public function createTableOfModel(Model $model)
    {

        $name = $model->getName()->tableName();

        $databaseAttributes = $model->getAttributes()->databaseAttributes();

        $databaseAttributesAsString = (function ($attributes) {
            $result = [];

            foreach ($attributes as $name => $rules) {

                $attributeAsString = "`$name`";
                if (!!$rules['type']) $attributeAsString .= " $rules[type]";
                if (!!($rules['nullable'] ?? false)) $attributeAsString .= " $rules[nullable]";
                if (!!($rules['required'] ?? false)) $attributeAsString .= " $rules[required]";
                if (!!($rules['auto_increment'] ?? false)) $attributeAsString .= " $rules[auto_increment]";
                if (!!($rules['default'] ?? false)) $attributeAsString .= " $rules[default]";

                $result[] = $attributeAsString;

            }

            return implode(',', $result);

        })($databaseAttributes);

        $primaries = $model->getAttributes()->primaries();

        $primariesAsString = (function ($attributes) {

            $result = [];

            foreach ($attributes as $attribute) $result[] = "PRIMARY KEY($attribute)";

            return implode(',', $result);

        })($primaries);

        $foreigns = $model->getAttributes()->foreigns();

        $foreignsAsString = (function ($attributes) {

            $result = [];

            foreach ($attributes as $attribute => $entry) {
                $result[] = "CONSTRAINT `$entry[name]` FOREIGN KEY ($attribute) REFERENCES $entry[references]";
            }

            return implode(',', $result);

        })($foreigns);


        $sql = [];
        $sql[] = "CREATE TABLE IF NOT EXISTS `$name` ($databaseAttributesAsString";
        if ([] !== $primaries) $sql[] = ", $primariesAsString";
        if ([] !== $foreigns) $sql[] = ", $foreignsAsString";
        $sql[] = ");";

        $sql = implode('', $sql);

        if (!$this->pdo) {
            throw new \Exception('Error with the database : not connected !');
        }

        try {
            $this->pdo->query($sql);
        } catch (\PDOException $exception) {
            // echo($exception->getMessage() . "\r\n\r\n");
        }
    }

    public function query(string $table)
    {
        return (new SQLStatement($table))->setDatabase($this);
    }

    public function getPDO()
    {
        return $this->pdo;
    }

}