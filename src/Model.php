<?php
declare(strict_types=1);


namespace App;


class Model
{

    private ModelName $name;
    private ModelAttributes $attributes;
    private ?Database $database;

    private Collection $values;

    private array $originalOptions;


    static public function from(Model $model)
    {
        return (new Model(
            $model->getName()->originalName(),
            $model->getAttributes()->attributes(),
            $model->originalOptions(),
            $model->getDatabase()
        ));
    }


    public function __construct(
        string $name, array $attributes = [], array $options = [],
        ?Database $database = null
    )
    {

        $options = [
            'incrementable' => true,
            ...$options,
        ];

        $incrementable = $options['incrementable'];

        if (true === $incrementable) {
            $attributes['id'] = 'id';
        }

        $this->name = new ModelName($name, $options['table'] ?? null);
        $this->attributes = new ModelAttributes($attributes);

        $this->database = $database;

        $this->models = (new Collection())
            ->setFunction('get', function (int $id): ?Model {
                foreach ($this->models->entries() as $model) if ($id === $model->id) return $model;
                return null;
            });

        $this->values = (new Collection())
            ->setFunction('get', fn(string $name): ?string => $this->values->entries()[$name] ?? null)
            ->setFunction('set', function (array $values) {
                foreach ($values[0] as $name => $value) {
                    $this->values->map[$name] = $value;
                }
            })
            ->setFunction('add', function(string $name, string $value) {
                // dd($name, $value);
                // $this->values->entries()[$name] = $value;
            });

        $this->originalOptions = $options;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getName()
    {
        return $this->name;
    }

    public function find(array $parameters)
    {
        $tableName = $this->name->tableName();

        $result = $this->database->query($tableName)->select('*')->where($parameters)->first();

        $model = Model::from($this)->setValues($result);

        return $model;
    }

    public function findById($id)
    {
        return $this->find(['id', $id]);
    }

    public function all()
    {
        $tableName = $this->name->tableName();

        $result = $this->database->query($tableName)->select('*')->get();

        return $result;
    }

    public function setDatabase(?Database $database)
    {
        $this->database = $database;
        return $this;
    }

    public function setValues(array $values)
    {
        $this->values->call('set', $values);
        return $this;
    }

    public function originalOptions()
    {
        return $this->originalOptions;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function values() {
        return $this->values;
    }

}