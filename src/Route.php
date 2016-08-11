<?php
namespace Zkrati\Routing;

/**
 * Class Route
 * @package Zkrati\Routing
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

    /** @var array */
    private $handler = [];

    /** Pattern for parsing variables from path string */
    const VARIABLE_PATTERN = '#<(.*?)>#';

    /** Pattern for parsing variables from path string */
    const CLASS_PATTERN = ':';

    /** Internal variable identifier */
    const VARIABLE = 'var:';

    /**
     * Route constructor.
     *
     * @param string $pattern
     * @param mixed $handler
     */
    public function __construct($pattern, $handler)
    {
        $this->path = $pattern;
        $this->pattern = $this->preparePattern($pattern);
        $this->handler = $this->prepareHandler($handler);
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
            if(strpos($this->pattern[$key], self::VARIABLE) === false AND $this->pattern[$key] != $part){
                return false;
            } else if(strpos($this->pattern[$key], self::VARIABLE) !== false){
                $variables[str_replace(self::VARIABLE, "", $this->pattern[$key])] = $part;
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
        return array($this->matchedVariables);
    }

    /**
     * Prepare given route to usable form
     *
     * @param string $pattern
     * @return array
     */
    private function preparePattern($pattern)
    {
        $pattern = explode("/", rtrim($pattern, "/"));
        array_shift($pattern);

        foreach($pattern as $key => $part){
            if(preg_match(self::VARIABLE_PATTERN, $part)){
                $pattern[$key] = self::VARIABLE . str_replace("<", "", str_replace(">", "", $pattern[$key]));
                $this->varKeys[] = $key;
            }
        }

        return $pattern;
    }

    /**
     * Prepare route handler
     *
     * @param $handler
     * @return array
     * @throws InvalidHandlerException
     */
    public function prepareHandler($handler)
    {
        if(is_callable($handler)){
            return array(
                "type" => "callable",
                "action" => $handler
            );
        } else if(is_string($handler) AND strpos($handler, self::CLASS_PATTERN) !== false AND is_callable(explode(":", $handler))){
            return array(
                "type" => "class",
                "action" => explode(":", $handler)
            );
        } else if(is_array($handler) AND is_object($handler[0]) AND is_callable($handler[1])){
            return array(
                "type" => "instance",
                "action" => $handler
            );
        }

        throw new InvalidHandlerException("Invalid handler for " . $this->getPath());
    }
}