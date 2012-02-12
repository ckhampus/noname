<?php

namespace Blog\Tests\Entity;

use Blog\Entity\Post;

use Doctrine\Tests\OrmTestCase,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\ORM\Mapping\Driver\DriverChain,
    Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class PostTest extends OrmTestCase
{
    private $em;

    protected function setUp()
    {
        $reader = new AnnotationReader();
        //$reader->setIgnoreNotImportedAnnotations(true);
        //$reader->setEnableParsePhpImports(true);

        $metadataDriver = new AnnotationDriver(
            $reader,
            // provide the namespace of the entities you want to tests
            'Blog\\Entity'
        );

        $this->em = $this->_getTestEntityManager();

        $this->em->getConfiguration()->setMetadataDriverImpl($metadataDriver);
    }

    public function testCreatePost()
    {
        $p = new Post();
        $p->setTitle('Hello');

        $this->em->persist($p);
        $this->em->flush();
    }
}