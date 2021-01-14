<?php
declare(strict_types=1);

$validators = [];

$validators['id'] = [
    'database' => [
        'type' => 'BIGINT',
        'unsigned' => true,
        'auto_increment' => true
    ],
    'validate' => function ($value) {
        // TODO : Better validator ?
        // Est-ce qu'on prend en compte auto_increment ?
        // Est-ce qu'on verifie si l'id est bien un big number ?
        return is_numeric($value);
    }
];

$validators['email'] = [
    'database' => [
        'type' => 'VARCHAR'
    ],
    'validate' => function ($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
];

$validators['password'] = [
    'database' => [
        'type' => 'VARCHAR'
    ],
    'validate' => function ($value) {
        // TODO : Better validator
        // Verifier si le password respecte bien le format de chiffrement
        return is_string($value);
    }
];

$validators['string'] = [
    'database' => [
        'type' => 'VARCHAR'
    ],
    'validate' => function ($value) {
        // TODO : Better validator ?
        // Est-ce qu'on verifie aussi les caractères ?
        return is_string($value);
    }
];

$validators['unsigned'] = [
    'database' => [
        'unsigned' => true
    ],
    'validate' => function ($value) {
        // Est-ce qu'on inclus aussi 0 ?
        return is_numeric($value) && -1 < ((int)$value);
    }
];

$validators['float'] = [
    'database' => [
        'type' => 'FLOAT'
    ],
    'validate' => function ($value) {
        // TODO : Better validator ?
        // Est-ce qu'on verifie si la valeur est bien dans l'intervalle du float ?
        return is_float($value);
    }
];

$validators['decimal'] = [
    'database' => [
        'type' => 'DECIMAL'
    ],
    'validate' => function ($value) {
        // TODO : Better validator ?
        // Est-ce que le type decimal est vraiment identique à float ?
        return is_float($value);
    }
];

$validators['small number'] = [
    'database' => [
        'type' => 'SMALLINT'
    ],
    'validate' => function ($value) {
        // TODO : Better validator
        // Inclure l'intervalle des small numbers
        return is_numeric($value);
    }
];

$validators['big number'] = [
    'database' => [
        'type' => 'BIGINT'
    ],
    'validate' => function ($value) {
        // TODO : Better validator
        // Inclure l'intervalle des big numbers
        return is_numeric($value);
    }
];

$validators['medium number'] = [
    'database' => [
        'type' => 'MEDIUMINT'
    ],
    'validate' => function ($value) {
        // TODO : Better validator
        // Inclure l'intervalle des medium numbers
        return is_numeric($value);
    }
];

$validators['tiny medium'] = [
    'database' => [
        'type' => 'TINYINT'
    ],
    'validate' => function ($value) {
        // TODO : Better validator
        // Inclure l'intervalle des tiny numbers
        return is_numeric($value);
    }
];

$validators['number'] = [
    'database' => [
        'type' => 'INT'
    ],
    'validate' => function ($value) {
        return is_numeric($value);
    }
];

$validators['boolean'] = [
    'database' => [
        'type' => 'BOOLEAN'
    ],
    'validate' => function ($value) {
        return is_bool($value);
    }
];

$validators['required'] = [
    'database' => [
        'not-null' => true
    ],
    'validate' => function ($value) {
        return false === is_null($value);
    }
];

$validators['enum'] = [
    'database' => function ($value) {
        $values = explode(',', $value);
        $values = array_map(function ($entry) {
            return "'$entry'";
        }, $values);
        $values = implode(',', $values);

        return ["type" => "ENUM($values)"];
    },
    'validate' => function ($value) {
        // TODO
        return false;
    }
];

$validators['min'] = [
    'database' => [],
    'validate' => function ($value) {
        // TODO
        return false;
    }
];

$validators['max'] = [
    'database' => function ($value) {
        $attributeRules = [];
        if (!!$value) {
            $attributeRules['type'] = "$attributeRules[type]($value)";
        }
        return $attributeRules;
    },
    'validate' => function ($value) {
        // TODO
        return false;
    }
];

$validators['nullable'] = [
    'database' => [
        'default' => 'NULL'
    ],
    'validate' => function ($value) {
        // TODO
        return false;
    }
];

$validators['default'] = [
    'database' => fn($value) => [
        'default' => $value
    ],
    'validate' => function ($value) {
        // TODO
        return false;
    }
];

$validators['foreign to'] = [
    'database' => fn($value) => [
        'unsigned' => true,
        'type' => 'BIGINT',
        'foreign' => $value
    ],
    'validate' => function ($value) {
        // TODO
        return false;
    }
];

/*
$validators[''] = [
    'database' => [],
    'validate' => function ($value) {
        return false;
    }
];
 */

return $validators;