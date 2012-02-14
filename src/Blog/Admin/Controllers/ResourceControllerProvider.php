<?php

namespace Blog\Admin\Controllers;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;

use Doctrine\ORM\Mapping\ClassMetadata;

class ResourceControllerProvider implements ControllerProviderInterface 
{
    public $metadata;

    public $em;

    public $form;

    function __construct(ClassMetadata $metadata) {
        $this->metadata = $metadata;
    }

    public function connect(Application $app)
    {
        $controller = $this;
        $this->em = $app['db.entity_manager'];
        $this->form = $this->createResourceForm($app, $this->metadata);

        $resource = underscore($this->metadata->getTableName());

        $controllers = new ControllerCollection();

        // display a list of all resources
        $controllers->get("/{$resource}", function (Application $app) {
            return 'Admin Index';
        })->bind("{$resource}_index_path");

        // return an HTML form for creating a new resource
        $controllers->get("/{$resource}/new", function (Application $app) use ($controller) {
            $class = $controller->metadata->getName();
            $resource = new $class();
            
            $form = $controller->form;

            return $app['twig']->render('new.html.twig', array('form' => $form->createView()));
        })->bind("new_{$resource}_path");

        // create a new resource
        $controllers->post("/{$resource}", function (Application $app) use ($controller) {
            $class = $controller->metadata->getName();
            $form = $controller->form;
            $form->bindRequest($app['request']);

            if ($form->isValid()) {
                $data = $form->getData();

                var_dump($data);

                return 'hello';
            }
        })->bind("save_{$resource}_path");;

        // display a specific resource
        $controllers->get("/{$resource}/{id}", function (Application $app, $id) use ($controller) {
            /*
            $em = $app['db.entity_manager'];
            $resource = $em->find($metadata->getName(), $id);
            
            $form = $controller->form;

            return $app['twig']->render('edit.html.twig', array('form' => $form->createView()));
            */
        })->bind("show_{$resource}_path");

        // return an HTML form for editing a resource
        $controllers->get("/{$resource}/{id}/edit", function (Application $app, $id) use ($controller) {
            $em = $controller->em;
            $resource = $em->find($metadata->getName(), $id);
            
            $form = $controller->form->setData($resource);

            return $app['twig']->render('edit.html.twig', array(
                'form' => $form->createView(),
                'resource' => $resource,
            ));
        })->bind("edit_{$resource}_path");

        // update a specific resource
        $controllers->put("/{$resource}/{id}", function (Application $app, $id) {
            return 'Admin Index';
        })->bind("update_{$resource}_path");;

        // delete a specific resource
        $controllers->delete("/{$resource}/{id}", function (Application $app, $id) {
            return 'Admin Index';
        })->bind("delete_{$resource}_path");;

        return $controllers;
    }

    private function createResourceForm(Application $app, $metadata)
    {
        $fb = $app['form.factory']->createBuilder('form', array(), array('data_class' => $metadata->getName()));

        foreach ($metadata->fieldMappings as $field) {
            switch ($field['type']) {
                case 'string':
                    $type = 'text';
                    break;
                
                default:
                    $type = $field['type'];
                    break;
            }

            $fb = $fb->add($field['fieldName'], $type);
        }

        return $fb->getForm();
    }
}