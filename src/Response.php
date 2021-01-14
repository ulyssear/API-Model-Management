<?php
declare(strict_types=1);


namespace App;


/**
 * Class Response
 * @package App
 */
class Response
{

    /**
     * Headers par défaut
     * @var array
     */
    private array $headers = [
        "Content-Length" => null,
        "charset" => 'utf-8'
    ];

    /**
     * @var App
     */
    private App $app;

    /**
     * Response constructor.
     * @constructor
     */
    public function __construct(App $app) {
        $this->app = $app;
    }


    /**
     * @param array $array
     * @return Response
     */
    public function json(array $array) {
        $this->headers['Content-Type'] = 'application/json';

        $this->print(json_encode($array));

        return $this;
    }


    /**
     * @param string $content
     * @return Response
     */
    public function html(string $content) {
        $this->headers['Content-Type'] = 'text/html';

        $this->print($content);

        return $this;
    }


    /**
     * @param string $filepath
     * @param array $variables
     * @return $this
     */
    public function php(string $filepath, array $variables = []) {

        $variables['app'] = $this->app;

        extract($variables);

        ob_start();
        ob_get_clean();
        try {
            require_once __DIR__ . '/../views/' . $filepath . '.php';
        }
        catch (\Throwable $exception) {
            return $this->php('errors/404');
        }

        return $this;
    }


    /**
     * @param $content
     * @return Response
     */
    private function print($content) {

        $contentLength = null;
        $contentType = null;
        $charset = null;

        $contentLength = $this->headers['Content-Length'];
        $contentType = $this->headers['Content-Type'];
        $charset = $this->headers['charset'];

        $headers = !!$contentType ? "Content-Type: $contentType;" : null;
        $headers .= !!$contentLength ? "Content-Length: $contentLength;" : null;
        $headers .= !!$charset ? "charset=$charset;" : null;

        header($headers);

        echo $content;

        return $this;
    }

}