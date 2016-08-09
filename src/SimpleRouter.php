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
     * @param string $pattern Matched route pattern
     * @param callable $action Callback that returns the response when matched
     */
    public function get($pattern, callable $action)
    {
        $this->controllers['GET'][] = new Route($pattern, $action);
    }

    /**
     * Map a POST request to a handler
     *
     * @param string $pattern Matched route pattern
     * @param callable $action Callback that returns the response when matched
     */
    public function post($pattern, callable $action)
    {
        $this->controllers['POST'][] = new Route($pattern, $action);
    }

    /**
     * Map a PUT request to a handler
     *
     * @param string $pattern Matched route pattern
     * @param callable $action Callback that returns the response when matched
     */
    public function put($pattern, callable $action)
    {
        $this->controllers['PUT'][] = new Route($pattern, $action);
    }

    /**
     * Map a DELETE request to a handler
     *
     * @param string $pattern Matched route pattern
     * @param callable $action Callback that returns the response when matched
     */
    public function delete($pattern, callable $action)
    {
        $this->controllers['DELETE'][] = new Route($pattern, $action);
    }

    /**
     * Map a PATCH request to a handler
     *
     * @param string $pattern Matched route pattern
     * @param callable $action Callback that returns the response when matched
     */
    public function patch($pattern, callable $action)
    {
        $this->controllers['PATCH'][] = new Route($pattern, $action);
    }

    /**
     * Render response for request
     */
    public function run()
    {
        foreach($this->controllers[$this->getMethod()] as $url){
            $match = $url->match($this->getPath());
            if($match){
                echo call_user_func_array($url->getHandler(), $url->matchedVariables());
                return;
            }
        }

        // todo better error resolutions
        echo "error 404";
    }

    /**
     * Get relative path from URI
     *
     * @return mixed
     */
    private function getPath() {
        return rtrim(str_replace(str_replace("index.php", "", $_SERVER['PHP_SELF']), "/", $_SERVER['REQUEST_URI']), "/");
    }

    /**
     * Get request method
     *
     * @return string
     */
    private function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

}