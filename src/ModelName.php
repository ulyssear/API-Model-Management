<?php
declare(strict_types=1);


namespace App;


class ModelName
{

    private string $name;
    private string $originalName;
    private string $tableName;


    public function __construct(string $name, ?string $tableName = null) {
        $this->parseName($name);
        $this->parseTableName($tableName);
    }

    public function name() {
        return $this->name;
    }

    public function originalName() {
        return $this->originalName;
    }

    public function tableName() {
        return $this->tableName;
    }


    private function parseName (string $name) {
        $words = explode('-', $name);
        $name = [];

        $cursor = -1;
        foreach( $words as $word ) {

            $cursor += 1;

            $word = strtolower($word);
            $words[$cursor] = $word;

            if ($cursor > 0) $word = ucfirst($word);

            $name[] = $word;
        }

        $name = implode('', $name);

        $this->name = $name;
        $this->originalName = implode('-', $words);
    }

    private function parseTableName (?string $tableName) {

        if (!!$tableName) {
            $this->tableName = $tableName;
            return;
        }

        $words = explode('-', $this->name);
        $tableName = implode('_', $words) . 's';

        $this->tableName = $tableName;

    }

}