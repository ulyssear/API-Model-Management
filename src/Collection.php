<?php
declare(strict_types=1);


namespace App;


class Collection
{

    public static int $STYLE_DEFAULT = 0;
    public static int $STYLE_HEAP = 1;
    public static int $STYLE_STACK = 2;

    public array $map;

    private array $functions = [];

    public function __construct(array $map = [], array $parameters = [])
    {
        $this->map = $map;
    }

    public function keys()
    {
        return array_keys($this->map);
    }

    public function values()
    {
        return array_values($this->map);
    }

    public function entries()
    {
        return $this->map;
    }

    public function add(...$arguments)
    {

        if (in_array('add', array_keys($this->functions))) {
            $this->functions['add'](...$arguments);
            return $this;
        }

        $entry = $arguments[0];
        $type = $arguments[1] ?? null;

        switch ($type) {
            default:
            case self::$STYLE_DEFAULT:
            case self::$STYLE_HEAP:
                $this->map = [...$this->map, $entry];
                break;

            case self::$STYLE_STACK:
                $this->map = [$entry, ...$this->map];
                break;
        }

        return $this;

    }

    public function addIfNotExists(...$arguments)
    {
        if (in_array('addIfNotExists', array_keys($this->functions))) {
            return $this->functions['addIfNotExists']($arguments);
        }

        $entry = $arguments[0];

        if (false === in_array($entry, $this->map)) {
            $this->add($entry);
        }

        return $this;
    }

    public function pop()
    {
        return array_pop($this->map);
    }

    public function shift()
    {
        return array_shift($this->map);
    }

    public function setFunction(string $name, callable $function)
    {
        $this->functions[$name] = $function;
        return $this;
    }

    public function get(...$arguments)
    {
        return $this->call('get', ...$arguments);
    }

    public function call($function, ...$arguments)
    {
        if (in_array($function, array_keys($this->functions))) {
            return $this->functions[$function](...$arguments);
        }

        throw new \Exception('No function named "' . $function . '" defined for collections !');
    }

    public function toArray(...$arguments)
    {
        return $this->call('toArray', ...$arguments);
    }

    public function clear()
    {
        $this->map = [];
    }

    public function functions() {
        return $this->functions;
    }
}