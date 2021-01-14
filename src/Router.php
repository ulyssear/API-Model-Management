<?php
declare(strict_types=1);


namespace App;


class Router
{

    static public int $STATUS_OK = 0;
    static public int $STATUS_ERROR = 1;


    private Collection $staticRoutes;
    private Collection $dynamicRoutes;

    private App $context;

    private Collection $postParameters;
    private Collection $getParameters;


    static function errorRoute()
    {
        return [
            'method' => 'GET',
            'address' => '/404',
            'route' => function () {
                echo 'Error 404 !';
            }
        ];
    }

    function response()
    {
        return new Response($this->context);
    }


    public function __construct(App $context)
    {
        $this->dynamicRoutes = new Collection();
        $this->staticRoutes = new Collection();

        $this->context = $context;

        $this->postParameters = (new Collection($_POST ?? []))
            ->setFunction('get', fn($name) => $this->postParameters->entries()[$name[0]] ?? null);

        $this->getParameters = (new Collection($_GET ?? []))
            ->setFunction('get', fn($name) => $this->getParameters->entries()[$name[0]] ?? null);
    }

    public function get(string $address, callable $route)
    {
        return $this->route('GET', $address, $route);
    }

    public function post(string $address, callable $route)
    {
        return $this->route('POST', $address, $route);
    }

    public function put(string $address, callable $route)
    {
        return $this->route('PUT', $address, $route);
    }

    public function delete(string $address, callable $route)
    {
        return $this->route('DELETE', $address, $route);
    }

    public function route(string $method, string $address, callable $route)
    {

        $isDynamicRoute = $this->isDynamicRoute($address);

        // Si c'est une route dynamique
        if (true === $isDynamicRoute) {
            $this->dynamicRoutes->add([
                'method' => $method,
                'address' => $address,
                'route' => $route
            ]);
        } // Si c'est une route statique
        else {
            $this->staticRoutes->add([
                'method' => $method,
                'address' => $address,
                'route' => $route
            ]);
        }

        return $this;

    }


    public function runAtAddress(string $address)
    {
        $isAddressInRoutes = $this->isAddressInRoutes($address);

        // dd($isAddressInRoutes);

        if (!!$isAddressInRoutes) {
            // On remplace la variable par un autre avec un nom plus approprié à la situation.
            $routeAtAddress = $isAddressInRoutes;
            unset($isAddressInRoutes);

            $routeAtAddress['route']($routeAtAddress['parameters']);
        }

    }


    public static function getParameter($parameters, $name)
    {

        foreach ($parameters as $parameter) {
            if ($parameter['name'] === $name) {
                return $parameter['value'];
            }
        }

        throw new \Exception("Parameter $name not found !");

    }

    public function dynamicRoutes(): Collection
    {
        return $this->dynamicRoutes;
    }

    public function staticRoutes(): Collection
    {
        return $this->staticRoutes;
    }

    public function postParameters(): Collection {
        return $this->postParameters;
    }

    public function getParameters(): Collection {
        return $this->getParameters;
    }

    private function isDynamicRoute(string $address): bool
    {
        $pattern = "({\w+})";

        $matches = [];
        if (preg_match_all($pattern, $address, $matches)) {
            return 0 < count($matches);
        }

        return false;
    }


    private function isAddressInRoutes(string $address)
    {

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $targetRoute = null;

        $exploded = explode('/', $address);
        array_shift($exploded);

        $parameters = [];

        $isSimpleRoute = 2 > count($exploded);

        if (true === $isSimpleRoute) {
            $this->isAddressInStaticRoutes($address);
            /*foreach ($this->staticRoutes->entries() as $route) {

                $routeMethod = strtolower($route['method']);
                $routeFunction = $route['route'];
                $routeAddress = $route['address'];

                $explodedRouteAddress = explode('/', $routeAddress);
                array_shift($explodedRouteAddress);

                if ($routeMethod !== $method) continue;

                try {
                    if ($explodedRouteAddress[0] === $exploded[0]) {
                        $targetRoute = $route;
                        break;
                    }
                } catch (\Throwable $exception) {
                    // Do nothing ?
                }

            }*/
        } else {

            foreach ($this->dynamicRoutes->entries() as $route) {

                $cursor = -1;

                $routeAddress = $route['address'];
                $routeMethod = strtolower($route['method']);

                $explodedRouteAddress = explode('/', $routeAddress);
                array_shift($explodedRouteAddress);

                if ($routeMethod !== $method) continue;

                foreach ($exploded as $part) {
                    $cursor++;

                    if (count($explodedRouteAddress) - 1 < $cursor) break;

                    $partOfRouteAddress = $explodedRouteAddress[$cursor];

                    $isParameter = (function ($part) {
                        $matches = [];
                        preg_match('/{(\w+)}/', $part, $matches);
                        return 1 < count($matches);
                    })($partOfRouteAddress);

                    if (true === $isParameter) {
                        $parameter = (function ($partOfRouteAddress, $partOfAddress) use ($exploded) {
                            $matchesOfName = [];
                            preg_match('/{(\w+)}/', $partOfRouteAddress, $matchesOfName);

                            $matchesOfValue = null;

                            switch ($matchesOfName[1]) {

                                case 'path':
                                    $indexOfPart = array_search($partOfAddress, $exploded);
                                    $pathValue = array_slice($exploded, $indexOfPart);
                                    $pathValue = implode('/', $pathValue);
                                    $matchesOfValue = $pathValue;
                                    break;

                                default:
                                    $matchesOfValue = $partOfAddress;
                                    break;

                            }

                            return [
                                'name' => $matchesOfName[1],
                                'value' => $matchesOfValue
                            ];
                        })($partOfRouteAddress, $part);

                        $parameters[] = $parameter;

                        if ('path' === $parameter['name']) {
                            $targetRoute = $route;
                            break;
                        }

                        continue;
                    }

                    if (strtolower($part) !== strtolower($partOfRouteAddress)) {
                        break;
                    }

                    if ($cursor >= count($exploded) - 1) {
                        $targetRoute = $route;
                        break;
                    }
                }

            }

            foreach ($this->staticRoutes->entries() as $route) {

                $cursor = -1;

                $routeAddress = $route['address'];
                $routeMethod = strtolower($route['method']);

                $explodedRouteAddress = explode('/', $routeAddress);
                array_shift($explodedRouteAddress);

                if ($routeMethod !== $method) continue;

                foreach ($exploded as $part) {
                    $cursor++;

                    if (count($explodedRouteAddress) - 1 < $cursor) break;

                    $partOfRouteAddress = $explodedRouteAddress[$cursor];

                    if (strtolower($part) !== strtolower($partOfRouteAddress)) {
                        break;
                    }

                    if ($cursor === count($exploded) - 1) {
                        $targetRoute = $route;
                        break;
                    }
                }

            }
        }

        if (null === $targetRoute) {
            $targetRoute = self::errorRoute();
        }

        $targetRoute['parameters'] = $parameters;

        return $targetRoute;

    }

    private function isAddressInStaticRoutes(string $address) {
        foreach ($this->staticRoutes->entries() as $route) {

            $routeMethod = strtolower($route['method']);
            $routeFunction = $route['route'];
            $routeAddress = $route['address'];

            $explodedRouteAddress = explode('/', $routeAddress);
            array_shift($explodedRouteAddress);

            if ($routeMethod !== $method) continue;

            try {
                if ($explodedRouteAddress[0] === $exploded[0]) {
                    $targetRoute = $route;
                    break;
                }
            } catch (\Throwable $exception) {
                // Do nothing ?
            }

        }
    }

}