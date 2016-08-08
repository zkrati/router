# zkrati/router

PHP router as simple as can be...

  - simple and fast yet complex routing
  - both static and dynamic routes
  - supports GET, POST, PUT, DELETE and PATCH requests

### Version
0.1


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
 
 
### Variables

```php
$router->get("/test/<variable>/<next_variable>/path", function($variable, $next_variable) {
    // handle GET request at /test/whatever/whatever/path
    // variables $variable and $next_variable are available here 
});

```

### Todos

 - better error handling
 - add cache support
 - define variable types and regexp

License
----
GNU GPL
