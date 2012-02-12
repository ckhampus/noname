<?php

namespace Blog\Provider;

use Silex\Application,
    Silex\ServiceProviderInterface;

use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Tools\SchemaTool;

class DoctrineOrmServiceProvider implements ServiceProviderInterface 
{
    public function register(Application $app)
    {
        $app['db.config'] = $app->share(function () use ($app) {
            $entities = $app['db.entities'];
            return Setup::createAnnotationMetadataConfiguration($entities, $app['debug']);
        });

        $app['db.entity_manager'] = $app->share(function () use ($app) {
            if (isset($app['db.options'])) {
                return EntityManager::create($app['db.options'], $app['db.config']);
            } else {
                throw new \Exception('No options defined.');
            }
        });

        $app['db.event_manager'] = $app->share(function () use ($app) {
            return $app['db.entity_manager']->getEventManager();
        });

        $app['db'] = $app->share(function () use ($app) {
            return $app['db.entity_manager']->getConnection();
        });

        if (isset($app['db.orm.class_path'])) {
            $app['autoloader']->registerNamespace('Doctrine\\ORM', $app['db.orm.class_path']);
        }

        if (isset($app['db.dbal.class_path'])) {
            $app['autoloader']->registerNamespace('Doctrine\\DBAL', $app['db.dbal.class_path']);
        }

        if (isset($app['db.common.class_path'])) {
            $app['autoloader']->registerNamespace('Doctrine\\Common', $app['db.common.class_path']);
        }
    }
}