<?php

namespace Blog\Tests\Controllers;

use Silex\WebTestCase;

class BlogControllerProviderTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require APP_DIR.'/app.php';
        $app['debug'] = true;
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