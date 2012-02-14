<?php

namespace Blog;

use Doctrine\Common\Persistence\AbstractManagerRegistry;

class Registry extends AbstractManagerRegistry 
{
    /**
     * @var Pimple
     */
    protected $container;

    function __construct($connection, $entity_manager) {
        parent::__construct('db', array($connection), array($entity_manager), $connection, $entity_manager, '');
    }

    /**
     * @inheritdoc
     */
    protected function getService($name)
    {
        return $this->container[$name];
    }

    /**
     * @inheritdoc
     */
    protected function resetService($name)
    {
        $this->container[$name] = null;
    }

    /**
     * @inheritdoc
     */
    public function setContainer(\Pimple $container = null)
    {
        $this->container = $container;
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered entity managers.
     *
     * @param string $alias The alias
     *
     * @return string The full namespace
     *
     * @see Configuration::getEntityNamespace
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
            }
        }

        throw ORMException::unknownEntityNamespace($alias);
    }
}