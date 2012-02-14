<?php

namespace Blog\Tests\Providers;

use Silex\Application;
use Blog\Providers\DoctrineOrmServiceProvider;

class DoctrineOrmServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProviderRegistration()
    {
        $app = new Application();
        $app->register(new DoctrineOrmServiceProvider(), array(
            'db.options' => array('driver' => 'pdo_sqlite', 'memory' => true),
            'db.entities' => array(APP_DIR.'/Blog/Entity'),
            'db.common.class_path' => ROOT_DIR.'/vendor/doctrine/common/lib',
            'db.dbal.class_path' => ROOT_DIR.'/vendor/doctrine/dbal/lib',
            'db.orm.class_path' => ROOT_DIR.'/vendor/doctrine/orm/lib',
        ));

        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $app['db.entity_manager']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['db.event_manager']);
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $app['db']);
    }
}