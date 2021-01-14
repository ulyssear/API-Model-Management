<?php


namespace App;


trait Routable
{

    private Router $router;

    public function runAtAddress()
    {
        $this->router->runAtAddress($_SERVER['REQUEST_URI'] ?? '');
    }

    public function router()
    {
        return $this->router;
    }

    public function response() {
        return $this->router()->response();
    }

    private function initializeRouter()
    {
        $this->router = new Router($this);

        $this->initializeRoutes();
    }

    private function initializeRoutes()
    {
        $this->router()
            ->get('/vendor/{path}', function (array $parameters) {
                $path = Router::getParameter($parameters, 'path');
                $filePath = __DIR__ . '/../vendor/' . $path;
                $file = file_get_contents($filePath);
                $fileInfos = pathinfo($filePath);
                $fileMime = $this->getMimeFile($fileInfos['extension']);

                header('Content-type: ' . $fileMime);

                echo $file;
            })
            ->get('/login', function () {
                $this->response()->php('auth/login.php');
            })
            ->get('/register', function () {
                $this->response()->php('auth/register.php');
            });
    }

}