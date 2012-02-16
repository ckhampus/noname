<?php

namespace Blog\Tests\Controllers;

use Silex\WebTestCase;

class BlogController extends WebTestCase
{
    public function createApplication()
    {
        $app = require APP_DIR.'/app.php';
        $app['environment'] = 'test';
        unset($app['exception_handler']);

        return $app;
    }

    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
    }
}