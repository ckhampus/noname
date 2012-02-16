<?php

namespace Blog\Tests\Entities;

use Blog\Entities\Post;

use Silex\WebTestCase;

class PostTest extends WebTestCase
{
    private $em;

    public function createApplication()
    {
        $app = require APP_DIR.'/app.php';
        $app['environment'] = 'test';
        unset($app['exception_handler']);
        $this->em = $app['db.entity_manager'];

        return $app;
    }

    public function testPersistingEntity()
    {
        $client = $this->createClient();
        $client->request('GET', '/admin/db/create');

        $em = $this->em;

        $p = new Post();
        $p->setTitle('Hello');
        $p->setContent('World');

        $this->assertNull($p->getId());

        $em->persist($p);
        $em->flush();

        $this->assertNotNull($p->getId());
    }

    public function testSettingAndGettingTitle()
    {
        $p = new Post();

        $this->assertEquals('', $p->getTitle());
        $p->setTitle('Hello');
        $this->assertEquals('Hello', $p->getTitle());
    }

    public function testSettingAndGettingContent()
    {
        $p = new Post();

        $this->assertEquals('', $p->getContent());
        $p->setContent('World');
        $this->assertEquals('World', $p->getContent());
    }

    public function testSettingAndGettingCreatedAtDate()
    {
        $p = new Post();

        $date = new \DateTime();
        $date->add(new \DateInterval('P1D'));

        $this->assertNotNull($p->getCreatedAt());
        $this->assertInstanceOf('DateTime', $p->getCreatedAt());
        $this->assertNotEquals($date, $p->getCreatedAt());
        $p->setCreatedAt($date);
        $this->assertEquals($date, $p->getCreatedAt());
    }

    public function testGettingUpdatedAtDate()
    {
        $p = new Post();

        $date = new \DateTime();
        $date->add(new \DateInterval('P1D'));

        $this->assertNotNull($p->getUpdatedAt());
        $this->assertInstanceOf('DateTime', $p->getUpdatedAt());
        $this->assertNotEquals($date, $p->getUpdatedAt());
    }
}