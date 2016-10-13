<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;

Router::plugin(
    'DejwCake/ExtendedAuthenticate',
    ['path' => '/extended-authenticate'],
    function ($routes) {
        $routes->connect('/get-token', ['controller' => 'UserTokens', 'action' => 'getToken', 'plugin' => 'DejwCake/ExtendedAuthenticate']);
    }
);
