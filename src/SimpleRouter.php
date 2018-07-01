<?php
namespace Zkrati\Routing;

/**
 * Class SimpleRouter
 * @package Zkrati\Routing
 */
class SimpleRouter
{
    /** @var array */
    private $controllers = [];

    /** @var array|null */
    private $instantiator = null;

    /**
     * SimpleRouter constructor.
     *
     * @param string $cache Path to caching folder
     */
    public function __construct($cache = null)
    {
        if($cache !== null){
            //todo add caching
        }
    }

    /**
     * Map a GET request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function get($pattern, $action)
    {
        $this->controllers['GET'][] = new Route($pattern, $action);
    }

    /**
     * Map a HEAD request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function head($pattern, $action)
    {
        $this->controllers['HEAD'][] = new Route($pattern, $action);
    }

    /**
     * Map a POST request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function post($pattern, $action)
    {
        $this->controllers['POST'][] = new Route($pattern, $action);
    }

    /**
     * Map a PUT request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function put($pattern, $action)
    {
        $this->controllers['PUT'][] = new Route($pattern, $action);
    }

    /**
     * Map a DELETE request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function delete($pattern, $action)
    {
        $this->controllers['DELETE'][] = new Route($pattern, $action);
    }

    /**
     * Map a CONNECT request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function connect($pattern, $action)
    {
        $this->controllers['CONNECT'][] = new Route($pattern, $action);
    }

    /**
     * Map a OPTIONS request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function options($pattern, $action)
    {
        $this->controllers['OPTIONS'][] = new Route($pattern, $action);
    }

    /**
     * Map a TRACE request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function trace($pattern, $action)
    {
        $this->controllers['TRACE'][] = new Route($pattern, $action);
    }

    /**
     * Map a PATCH request to a handler
     *
     * @param string $pattern
     * @param mixed $action
     */
    public function patch($pattern, $action)
    {
        $this->controllers['PATCH'][] = new Route($pattern, $action);
    }

    /**
     * Render response for request
     *
     * @throws RouteNotFoundException
     */
    public function run()
    {
        foreach($this->controllers[$this->getMethod()] as $url){
            $match = $url->match($this->getPath());
            if($match){
                $handler = $url->getHandler();
                $params = array(
                    $url->getMatchedVariables(),
                    $this->getParams(),
                    $this->getHeaders()
                );
                if($handler["type"] == "callable"){
                    echo call_user_func_array($handler["action"], $params);
                } else if($handler["type"] == "class"){
                    if($this->instantiator != null){
                        $method = $this->instantiator["method"];
                        $class = $this->instantiator["class"]->$method($handler["action"][0]);
                    } else {
                        $class = new $handler["action"][0]();
                    }
                    echo call_user_func_array(array($class, $handler["action"][1]), $params);
                } else {
                    echo call_user_func_array(array($handler["action"][0], $handler["action"][1]), $params);
                }
                return;
            }
        }

        throw new RouteNotFoundException("No route found for " . $this->getPath());
    }

    /**
     * Set custom instance creator
     *
     * @param object $instantiator
     * @param string $getter
     */
    public function setInstantiator($instantiator, $getter)
    {
        $this->instantiator = array(
            "class" => $instantiator,
            "method" => $getter
        );
    }

    /**
     * Get relative path from URI
     *
     * @return mixed
     */
    private function getPath() {
        return rtrim(parse_url(rtrim(str_replace(str_replace("index.php", "", $_SERVER['PHP_SELF']), "/", $_SERVER['REQUEST_URI']), "/"))['path'], "/");
    }

    /**
     * Get URI parameters
     *
     * @return mixed
     */
    private function getParams() {
        parse_str(parse_url(rtrim(str_replace(str_replace("index.php", "", $_SERVER['PHP_SELF']), "/", $_SERVER['REQUEST_URI']), "/"))['query'], $parameters);
        return $parameters;
    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get request headers
     *
     * @return array
     */
    private function getHeaders() {
        return getallheaders();
    }

}