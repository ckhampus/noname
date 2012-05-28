<?php

namespace Blog\Controllers;

use Blog\Application;

class BlogController extends Controller
{
    public function defineRoutes()
    {
        $this->get('/', function (Application $app) {
            return 'Index';
        });

        $this->get('/archive', function (Application $app) {
            return 'Archive';
        });

        $this->get('/blog/{slug}', function (Application $app) {
            return 'Single';
        });
    }
}
