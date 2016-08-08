<?php
namespace Zkrati\Routing;

/**
 * Class Route
 * @package ZkratiServices\Routing
 */
class Route
{
    /** @var array */
    private $pattern = [];

    /** @var array */
    private $varKeys = [];

    /** @var array */
    private $matchedVariables = [];

    /** @var string */
    private $path;

    /** @var callable */
    private $handler;

    /** Pattern for parsing variables from path string*/
    const VARIABLE_PATTERN = '#<(.*?)>#';

    /**
     * Route constructor.
     *
     * @param string $pattern
     * @param callable $handler
     */
    public function __construct($pattern, callable $handler)
    {
        $this->pattern = $this->prepare($pattern);
        $this->path = $pattern;
        $this->handler = $handler;
    }

    /**
     * Get raw path of route
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get handler assigned to the route
     *
     * @return callable
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Check whether given path matches the route
     *
     * @param $path
     * @return bool
     */
    public function match($path)
    {
        $path = explode("/", $path);
        array_shift($path);

        if(count($path) != count($this->pattern)){
            return false;
        }

        $variables = [];
        foreach($path as $key => $part){
            if($this->pattern[$key] !== null AND $this->pattern[$key] != $part){
                return false;
            } else if($this->pattern[$key] === null){
                $variables[] = $part;
            }
        }

        $this->matchedVariables = $variables;
        return true;
    }

    /**
     * Get variables from given path
     *
     * @return array
     */
    function matchedVariables() {
        return $this->matchedVariables;
    }

    /**
     * Prepare given route to usable form
     *
     * @param string $pattern
     * @return array
     */
    private function prepare($pattern)
    {
        $pattern = explode("/", rtrim($pattern, "/"));
        array_shift($pattern);

        foreach($pattern as $key => $part){
            if(preg_match(self::VARIABLE_PATTERN, $part)){
                $pattern[$key] = null;
                $this->varKeys[] = $key;
            }
        }

        return $pattern;
    }
}