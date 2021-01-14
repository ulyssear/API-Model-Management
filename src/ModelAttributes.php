<?php
declare(strict_types=1);


namespace App;


class ModelAttributes
{

    private array $attributes;
    private array $databaseAttributes = [];
    private array $foreigns = [];
    private array $primaries = [];


    public function __construct(array $attributes = [])
    {
        $this->parseAttributes($attributes);
        $this->parseDatabaseAttributes($attributes);
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function databaseAttributes()
    {
        return $this->databaseAttributes;
    }

    public function primaries()
    {
        return $this->primaries;
    }

    public function foreigns()
    {
        return $this->foreigns;
    }


    private function parseAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    private function parseDatabaseAttributes(array $attributes)
    {
        $validator = Validator::singleton();
        $rules = $validator->rules();

        foreach ($attributes as $name => $_rules) {

           $_rules = explode('|', $_rules);

           foreach ($_rules as $rule) {
               $result = explode(':', $rule);
               $_name = $result[0];
               $value = $result[1];

               dd($attributes);

               $rule = $rules->call('select', $_name);
               $isValid = $rule->map['validate']($value);

               dd($result);

               // TODO : Parse errors from validator

           }
       }

        // dd($validator->rules()->entries());
    }


    private function _parseDatabaseAttributes(array $attributes)
    {
        $this->databaseAttributes = [];

        $databaseRules = [];

        foreach ($attributes as $name => $rules) {

            $databaseRules[$name] = [];

            $rules = explode('|', $rules);

            foreach ($rules as $rule) {

                $rule = explode(':', $rule);

                $isNameValueFormat = 1 < count($rule);

                $ruleName = $rule[0];
                $ruleValue = null;

                if (true === $isNameValueFormat) $ruleValue = $rule[1];

                $this->addRuleToDatabaseAttribute($name, $ruleName, $ruleValue);

            }

        }

    }

    private function addRuleToDatabaseAttribute(string $attribute, string $name, ?string $value)
    {

        $attributeRules = $this->databaseAttributes[$attribute] ?? [];

        switch ($name) {

            case 'id':
                $attributeRules['type'] = 'BIGINT';
                $attributeRules['unsigned'] = 'UNSIGNED';
                $attributeRules['auto_increment'] = 'AUTO_INCREMENT';

                $this->primaries[] = $attribute;
                break;

            case 'email':
            case 'password':
            case 'string':
                $attributeRules['type'] = 'VARCHAR';
                break;

            case 'unsigned':
                $attributeRules['unsgined'] = 'UNSIGNED';
                break;

            case 'float':
                $attributeRules['type'] = 'FLOAT';
                break;

            case 'decimal':
                $attributeRules['type'] = 'DECIMAL';
                break;

            case 'small number':
                $attributeRules['type'] = 'SMALLINT';
                break;

            case 'tiny number':
                $attributeRules['type'] = 'TINYINT';
                break;

            case 'big number':
                $attributeRules['type'] = 'BIGINT';
                break;

            case 'number':
                $attributeRules['type'] = 'INT';
                break;

            case 'boolean':
                $attributeRules['type'] = 'BOOLEAN';
                break;

            case 'required':
                $attributeRules['required'] = 'NOT NULL';
                break;

            case 'enum':
                $values = explode(',', $value);
                $values = array_map(function ($entry) {
                    return "'$entry'";
                }, $values);
                $values = implode(',', $values);

                $attributeRules['type'] = "ENUM($values)";
                break;

            case 'min':
                // Do nothing ?
                break;

            case 'max':
                if (!!$value) $attributeRules['type'] = "$attributeRules[type]($value)";
                break;

            case 'nullable':
                $attributeRules['default'] = 'NULL';
                break;

            case 'default':
                $attributeRules['default'] = "DEFAULT '$value'";
                break;

            case 'foreign to':
                $attributeRules['unsigned'] = 'UNSIGNED';
                $attributeRules['type'] = 'BIGINT';

                $valueExploded = explode('.', $value);

                $this->foreigns[$attribute] = [
                    "name" => "fk_$attribute",
                    'references' => "$valueExploded[0]($valueExploded[1])"
                ];

                break;

            default:
                break;

        }

        $this->databaseAttributes[$attribute] = $attributeRules;

        return $this;

    }

    static public function is(string $type, $value)
    {

        switch ($type) {

            case 'id':
                // TODO : Better validator ?
                // Est-ce qu'on prend en compte auto_increment ?
                // Est-ce qu'on verifie si l'id est bien un big number ?
                return is_numeric($value);

            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL);

            case 'password':
                // TODO : Better validator
                // Verifier si le password respecte bien le format de chiffrement
                return is_string($value);

            case 'string':
                // TODO : Better validator ?
                // Est-ce qu'on verifie aussi les caractères ?
                return is_string($value);

            case 'unsigned':
                // Est-ce qu'on inclus aussi 0
                return is_numeric($value) && -1 < ((int)$value);

            case 'float':
                // TODO : Better validator ?
                // Est-ce qu'on verifie si la valeur est bien dans l'intervalle du float ?
                return is_float($value);

            case 'decimal':
                // TODO : Better validator ?
                // Est-ce que le type decimal est vraiment identique à float ?
                return is_float($value);

            case 'small number':
                // TODO : Better validator
                // Inclure l'intervalle des small numbers
                return is_numeric($value);

            case 'tiny number':
                // TODO : Better validator
                // Inclure l'intervalle des tiny numbers
                return is_numeric($value);

            case 'big number':
                // TODO : Better validator
                // Inclure l'intervalle des big numbers
                return is_numeric($value);

            case 'number':
                return is_numeric($value);

            case 'boolean':
                return is_bool($value);

            case 'required':
                return false === is_null($value);

            case 'enum':
                // TODO
                return false;

            case 'min':
                // TODO
                return false;

            case 'max':
                // TODO
                return false;

            case 'nullable':
                // TODO
                return false;

            case 'default':
                // TODO
                return false;

            case 'foreign to':
                // TODO
                return false;


        }

    }

}