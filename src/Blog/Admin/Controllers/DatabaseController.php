<?php

namespace Blog\Admin\Controllers;

use Blog\Controllers\Controller;

use Doctrine\ORM\Tools\SchemaTool,
    Doctrine\ORM\Tools\SchemaValidator;

class DatabaseController extends Controller
{
    public function defineRoutes()
    {
        $this->get('/db', array($this, 'indexAction'));

        $this->get('/db/create', array($this, 'createAction'));
    }

    public function indexAction()
    {
        return 'Database index';
    }

    public function createAction()
    {
        $em = $this->getEntityManager();
        $st = $this->getSchemaTool();
        $sv = $this->getSchemaValidator();
        $classes = $em->getMetadataFactory()->getAllMetadata();

        $result = $sv->validateMapping();

        if (empty($result)) {
            $st->createSchema($classes);
        }

        return 'Database created';
    }

    public function getSchemaTool()
    {
        return new SchemaTool($this->getEntityManager());
    }

    public function getSchemaValidator()
    {
        return new SchemaValidator($this->getEntityManager());
    }
}
