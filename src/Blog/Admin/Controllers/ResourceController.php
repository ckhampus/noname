<?php

namespace Blog\Admin\Controllers;

use Blog\Controllers\Controller;

use Doctrine\ORM\Mapping\ClassMetadata;

class ResourceController extends Controller
{
    private $metadata;

    function __construct(ClassMetadata $metadata) {
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

        return "Resources: " . count($entities);
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
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $em = $this->getEntityManager();
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
        $em = $this->getEntityManager();
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return "Resource: {$id}";
    }

    public function editAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return 'edit';
    }

    public function updateAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository($this->metadata->getName())->find($id);

        return 'update';
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
     * @param object $entity 
     * @return Form  The entity form
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