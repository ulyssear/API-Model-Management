<?php
declare(strict_types=1);

namespace App;


class Validator
{

    private Collection $rules;

    public static function singleton(): self
    {
        $validator = new Validator();

        $rules = require __DIR__ . '/../config/validator.php';

        foreach ($rules as $ruleName => $rule) {
            $validator->rules()->add($ruleName)
                ->call('select', $ruleName)
                ->add('database-attributes', $rule['database'])
                ->add('validate', $rule['validate']);
        }

        return $validator;
    }

    public function __construct()
    {

        $this->rules = (new Collection())
            ->setFunction('select', function (string $name) {
                $value = $this->rules->map[$name] ?? null;
                if (null === $value) {
                    dump($this->rules);
                    throw new \Exception("Entry named '$name' not defined in rules !");
                }
                return $value;
            })
            ->setFunction('add', function (string $name) {
                $this->rules->map[$name] = (new Collection)
                    ->setFunction('add', function (string $_name, $value) use ($name) {
                        $this->rules->map[$name]->map[$_name] = $value;
                    })
                    ->setFunction('validate', function (?string $value) use ($name) {
                        //dd($this->rules);
                        $foo = $this->rules->call('select', $name)->call('validate', $value);
                        return $foo;
                    });
            });

    }

    public function rules(): Collection
    {
        return $this->rules;
    }

}