<?php

namespace Blog\Admin\Controllers;

use Blog\Controllers\Controller;

class DashboardController extends Controller
{
    public function defineRoutes()
    {
        $this->get('/', function () {
            return 'Admin index';
        });
    }
}
