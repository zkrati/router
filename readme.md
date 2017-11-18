# zkrati/router

PHP router as simple as can be...

  - simple and fast yet complex routing
  - both static and dynamic routes
  - supports GET, POST, PUT, DELETE and PATCH requests

### Version
0.4


### Basic usage

```php
$router = new Zkrati\Routing\SimpleRouter();

$router->get("/test", function() {
    // handle GET request at /test
});

$router->post("/test", function() {
    // handle POST request at /test
});

$router->run();
```
 simple right?
 
 You can also use other handler declarations
```php
$router->get("/test/path", "Class:methodName");
// this code will create an instance of Class and call it's method methodName

$router->get("/test/path", "Namespace\Class:methodName");
// if you are using namespaces
```

If you need to create your own instance for some reason, you can pass the created instance
```php
$instance = new Class();
$router->get("/testuju/path", array($instance, "methodName"));
// this code will use the given instance of Class and call it's method methodName
```
 
### Variables

You will propably need to map routes with some varibles in it. It was never been easier.
```php
$router->get("/test/<variable>/<next_variable>/path", function($variables) {
    // variables <variable> and <next_variable> are available in array $variables by it's keys
    // for example with url /test/example/showcase/path
    
    echo $variables["variable"];      // will output "example"
    echo $variables["next_variable"]; // will output "showcase"
});

```
 
### Headers

Sometimes is useful to know the request headers. You don't need to search it somewhere anymore. Just add second parameter to your handler function.
```php
$router->get("/test/<variable>/<next_variable>/path", function($variables, $headers) {
    // variable $headers is array which contains all request headers 
});

```

### Exceptions

It is a good idea to wrap route declarations into try catch because in case of invalid handler it will throw InvalidHandlerException
```php
try{

    $router->get("/test/", "Class:firstMethod");
    $router->get("/other/route", "Class:secondMethod");
    $router->get("/cool/route", "invalid handler");
    
} catch(InvalidHandlerException $e) {
    echo $e->getMessage();
}
```

also $router->run(); throws RouteNotFoundException when it founds no route for current url
```php
try{
    $router->run();
} catch(Routing\RouteNotFoundException $e) {
    echo $e->getMessage();
}
```

### Instances

If you have custom class in your app which manages instances of all other classes and you want tou use string handler definition you can pass your instantiator into router. The router will get class instances from your custom instantiator.
```php
$router->setInstantiator($instantiator, "getInstance");
// where $instantiator is your custom instantiator and "getInstance" is name of it´s method to get instance
```

### Todos

 - define variable types and regexp
 - add cache support

License
----
MIT
