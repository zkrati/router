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

    /** @var boolean */
    private $wildcard = false;

    /** @var array */
    private $handler = [];

    /** Pattern for parsing variables from path string */
    const VARIABLE_PATTERN = '#<(.*?)>#';

    /** Pattern for parsing class and method from handler name */
    const CLASS_PATTERN = ':';

    /** Internal variable identifier */
    const VARIABLE = 'var:';

    /** Pattern to determine whether there is wildcard in path string */
    const WILDCARD_PATTERN = '*';

    /**
     * Route constructor.
     *
     * @param string $pattern
     * @param mixed $handler
     * @throws InvalidHandlerException
     */
    public function __construct($pattern, $handler)
    {
        $this->path = $pattern;
        $this->pattern = $this->preparePattern($pattern);
        $this->wildcard = $pattern == self::WILDCARD_PATTERN || in_array(self::WILDCARD_PATTERN, $this->pattern);
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

        if(!$this->wildcard AND count($path) != count($this->pattern)){
            return false;
        }

        if($this->wildcard AND count($path) < count($this->pattern)){
            return false;
        }

        $variables = [];
        foreach($path as $key => $part){
            if($this->pattern[$key] === self::WILDCARD_PATTERN || $this->pattern[$key] === NULL) {
                break;
            }

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
    public function getMatchedVariables() {
        return $this->matchedVariables;
    }

    /**
     * Prepare given route to usable form
     *
     * @param string $pattern
     * @return array
     */
    private function preparePattern($pattern)
    {
        $pattern = explode("/", strlen($pattern) > 1 ? rtrim($pattern, "/") : $pattern);
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
                "action" => explode(self::CLASS_PATTERN, $handler)
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