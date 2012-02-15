<?php

namespace Blog\Admin\Controllers;

use Blog\Admin\Forms\GenericType;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;

use Doctrine\ORM\Mapping\ClassMetadata;

class ResourceControllerProvider implements ControllerProviderInterface 
{
    private $metadata;

    private $em;

    private $app;

    function __construct(ClassMetadata $metadata) {
        $this->metadata = $metadata;
    }

    public function connect(Application $app)
    {
        $this->em = $app['db.entity_manager'];
        $this->app = $app;

        $resource = underscore($this->metadata->getTableName());

        $controllers = new ControllerCollection();

        // display a list of all resources
        $controllers->get("/{$resource}", array($this, 'indexAction'))->bind("{$resource}_index_path");

        // return an HTML form for creating a new resource
        $controllers->get("/{$resource}/new", array($this, 'newAction'))->bind("new_{$resource}_path");

        // create a new resource
        $controllers->post("/{$resource}", array($this, 'createAction'))->bind("create_{$resource}_path");

        // display a specific resource
        $controllers->get("/{$resource}/{id}", array($this, 'showAction'))->bind("show_{$resource}_path");

        // return an HTML form for editing a resource
        $controllers->get("/{$resource}/{id}/edit", array($this, 'editAction'))->bind("edit_{$resource}_path");

        // update a specific resource
        $controllers->put("/{$resource}/{id}", array($this, 'updateAction'))->bind("update_{$resource}_path");

        // delete a specific resource
        $controllers->delete("/{$resource}/{id}", array($this, 'deleteAction'))->bind("delete_{$resource}_path");

        return $controllers;
    }

    public function indexAction()
    {
        return 'index';
    }

    public function newAction()
    {
        $class = $this->metadata->getName();
        $entity = new $class();

        $form = $this->createForm($entity);

        return $this->render('new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function createAction()
    {
        $class = $this->metadata->getName();
        $entity = new $class();

        $form = $this->createForm($entity);
        $form->bindRequest($this->app['request']);

        if ($form->isValid()) {
            $em = $this->em;
            $em->persist($entity);
            $em->flush();

            $resource = underscore($this->metadata->getTableName());

            return $this->redirect($this->generateUrl("show_{$resource}_path", array('id' => $entity->getId())));
        }

        return $this->render('new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function showAction($id)
    {
        $em = $this->em;
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return 'show';
    }

    public function editAction($id)
    {
        $em = $this->em;
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return 'edit';
    }

    public function updateAction($id)
    {
        $em = $this->em;
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return 'update';
    }

    public function deleteAction($id)
    {
        $em = $this->em;
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return 'delete';
    }

    private function createForm($entity)
    {
        $form = $this->app['form.factory']->createBuilder(new GenericType($this->metadata), $entity)->getForm();

        return $form;
    }

    public function render($name, array $context = array())
    {
        return $this->app['twig']->render($name, $context);
    }

    public function redirect($url, $status = 302)
    {
        $this->app->redirect($url, $status);
    }

    public function generateUrl($name, $parameters = array(), $absolute = false)
    {
        $this->app['url_generator']->generate($name, $parameters, $absolute);
    }
}