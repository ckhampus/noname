<?php

namespace Blog\Controllers;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;

class BlogControllerProvider implements ControllerProviderInterface 
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        $controllers->get('/', function (Application $app) {
            return 'Index';
        });

        $controllers->get('/archive', function (Application $app) {
            return 'Archive';
        });

        $controllers->get('/{id}/{slug}', function (Application $app) {
            return 'Single';
        });

        return $controllers;
    }
}