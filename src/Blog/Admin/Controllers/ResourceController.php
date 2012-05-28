<?php

namespace Blog\Admin\Controllers;

use Blog\Controllers\Controller;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * This is a general controller for cases
 * where there doesn't exist a specialized one.
 */
class ResourceController extends Controller
{
    private $metadata;

    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function defineRoutes()
    {
        $resource = underscore($this->metadata->getTableName());

        // display a list of all resources
        $this->get("/{$resource}", array($this, 'indexAction'))->bind("{$resource}_index_path");

        // return an HTML form for creating a new resource
        $this->get("/{$resource}/new", array($this, 'newAction'))->bind("new_{$resource}_path");

        // create a new resource
        $this->post("/{$resource}", array($this, 'createAction'))->bind("create_{$resource}_path");

        // display a specific resource
        $this->get("/{$resource}/{id}", array($this, 'showAction'))->bind("show_{$resource}_path");

        // return an HTML form for editing a resource
        $this->get("/{$resource}/{id}/edit", array($this, 'editAction'))->bind("edit_{$resource}_path");

        // update a specific resource
        $this->put("/{$resource}/{id}", array($this, 'updateAction'))->bind("update_{$resource}_path");

        // delete a specific resource
        $this->delete("/{$resource}/{id}", array($this, 'deleteAction'))->bind("delete_{$resource}_path");
    }

    public function indexAction()
    {
        $em = $this->getEntityManager();
        $entities = $em->getRepository($this->metadata->getName())->findAll();
        $resource = underscore($this->metadata->getTableName());

        return $this->render('resource/index.html.twig', array(
            'show_resource_path' => "show_{$resource}_path",
            'edit_resource_path' => "edit_{$resource}_path",
            'entities' => $entities,
        ));
    }

    public function newAction()
    {
        $class = $this->metadata->getName();
        $entity = new $class();
        $resource = underscore($this->metadata->getTableName());

        $form = $this->createForm($entity);

        return $this->render('resource/new.html.twig', array(
            'create_resource_path' => "create_{$resource}_path",
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function createAction()
    {
        $class = $this->metadata->getName();
        $entity = new $class();

        $form = $this->createForm($entity);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $em = $this->getEntityManager();
            $em->persist($entity);
            $em->flush();

            $resource = underscore($this->metadata->getTableName());

            return $this->redirect($this->generateUrl("show_{$resource}_path", array('id' => $entity->getId())));
        }

        return $this->render('resource/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function showAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return "Resource: {$id}";
    }

    public function editAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository($this->metadata->getName())->find($id);
        $resource = underscore($this->metadata->getTableName());

        $form = $this->createForm($entity);

        return $this->render('resource/edit.html.twig', array(
            'update_resource_path' => "update_{$resource}_path",
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function updateAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        if ($form->isValid()) {
            $em = $this->getEntityManager();
            $em->persist($entity);
            $em->flush();

            $resource = underscore($this->metadata->getTableName());

            return $this->redirect($this->generateUrl("show_{$resource}_path", array('id' => $entity->getId())));
        }

        return $this->render('resource/edit.html.twig', array(
            'update_resource_path' => "update_{$resource}_path",
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return 'delete';
    }

    /**
     * Create a basic form for the resource.
     *
     * @param  object $entity
     * @return Form   The entity form
     */
    private function createForm($entity)
    {
        $app = $this->getApplication();

        $builder = $app['form.factory']->createBuilder('form', $entity);

        $class = $this->metadata->getReflectionClass();

        foreach ($this->metadata->getFieldNames() as $field) {
            $setter = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

            if ($class->hasMethod($setter)) {
                $builder->add($field);
            }
        }

        return $builder->getForm();
    }
}
