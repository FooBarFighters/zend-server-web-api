# Zend Server Web API

A [Guzzle](https://docs.guzzlephp.org/en/stable/) based wrapper for the [Zend Server Web API](https://help.zend.com/zend/current/content/web_api_reference_guide.htm). Has no dependencies with any framework.

## Installation

The package can be installed with composer:

````bash
composer require foobarfighters/zend-server-web-api
````

## Usage

When making use of the ClientFactory the Zend Server credentials can be passed as an array. Alternatively these values can be passed directly into the constructor of the client when instantiating it manually.
````php
<?php

$config = [
    'baseUrl' => 'https://your.zend.server.url',

    //== ZendServer admin > Administration > Web API Keys > UserName
    'username' => 'foobar',

    //== ZendServer admin > Administration > Web API Keys > Hash
    'hash' => 'f1ghtErs6fddd00ccec4eb1b261a8f6cc2dad94c1eb100eab',

    //== see https://help.zend.com/zend/current/content/web_api_reference_guide.htm
    'version' => 1.23,
];
````

#### Core Client
The core client is responsible for making the API request, returning the response as an associative array or throwing exceptions if anything goes wrong.

````php
use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;

try{
    //== instantiate a core client
    $client = ClientFactory::createClient($config);
    
    //== get the raw API data as an associative array
    $res = $client->getApplicationStatus();
    
    //== do something useful with it
    print_r($res['responseData']);
}

//== one size fits all
catch(Exception $e){
    error_log($e);
    echo 'stay calm, the internet police has been notified!';
}

````

#### Extended Client
The extended client decorates the core client and maps the raw API output to data models. These models offer additional benefits like filtering, date parsing and autocompletion in IDE's.

````php
use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;

try{
    //== instantiate an extended client
    $client = ClientFactory::createExtendedClient($config);
    
    //== reduce the list of applications to a single app model
    $myApp = $client->getAppList()->filterByName('myAppName');
    
    //== do something very exciting with it
    echo $myApp->getId();
}

//== several types of specific exceptions can be thrown, like Guzzle or Api related
catch(\FooBarFighters\ZendServer\WebApi\Exception\ApiException $e){
    echo 'You broke the API!';
}
````

#### Custom Guzzle
A custom Guzzle client can be passed to either the ClientFactory, or the Client class. This can be useful for adding headers, middleware or custom handlers.
See the examples bootstrap file for how to add [Monolog](https://github.com/Seldaek/monolog) logging middleware.

````php
$handlerStack = HandlerStack::create();
//== add middleware or custom handlers
...
$client = ClientFactory::createExtendedClient($config, new Guzzle(['handler' => $handlerStack]));
````

## Examples
For more examples see the examples folder. 

````bash
cd vendor/foobarfighters/ZendServer
php -S localhost:8080 -t examples
````
The examples can make actual calls to a Zend Server instance or be run either in mocked mode.

#### Mock mode
- http://localhost:8080/app-names.php?mock

When running an example in mocked mode all the API responses are injected from a set of json files instead of connecting to a Zend Server instance.

#### Live mode
- http://localhost:8080/app-names.php

Note that in live mode:
- ZS credentials need to be stored in vhost environmentals or alter the config array in the bootstrap file.
- Requests/responses are logged in the logs folder. This requires write access.
