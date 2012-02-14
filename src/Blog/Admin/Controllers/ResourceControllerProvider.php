<?php

namespace Blog\Admin\Controllers;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;

use Doctrine\ORM\Mapping\ClassMetadata;

class ResourceControllerProvider implements ControllerProviderInterface 
{
    private $metadata;

    function __construct(ClassMetadata $metadata) {
        $this->metadata = $metadata;
    }

    public function connect(Application $app)
    {
        $em = $app['db.entity_manager'];
        $resource = underscore($this->metadata->getTableName());

        $controllers = new ControllerCollection();

        // display a list of all resources
        $controllers->get("/{$resource}", function (Application $app) {
            return 'Admin Index';
        });

        // return an HTML form for creating a new resource
        $controllers->get("/{$resource}/new", function (Application $app) {
            return 'Admin Index';
        });

        // create a new resource
        $controllers->post("/{$resource}", function (Application $app) {
            return 'Admin Index';
        });

        // display a specific resource
        $controllers->get("/{$resource}/{id}", function (Application $app, $id) {
            $em = $app['db.entity_manager'];

            
            $form = $app['form.factory']->createBuilder();

            return 'Admin Index'.$id;
        });

        // return an HTML form for editing a resource
        $controllers->get("/{$resource}/{id}/edit", function (Application $app, $id) {
            return 'Admin Index';
        });

        // update a specific resource
        $controllers->put("/{$resource}/{id}", function (Application $app, $id) {
            return 'Admin Index';
        });

        // delete a specific resource
        $controllers->delete("/{$resource}/{id}", function (Application $app, $id) {
            return 'Admin Index';
        });

        return $controllers;
    }
}