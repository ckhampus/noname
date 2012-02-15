<?php

namespace Blog;

use Doctrine\Common\Persistence\ManagerRegistry as ManagerRegistryInterface,
    Doctrine\ORM\ORMException;

class ManagerRegistry implements ManagerRegistryInterface 
{
    private $name;
    private $connections;
    private $managers;

    function __construct($connection, $manager) {
      $this->connections = array('default' => $connection);
      $this->managers = array('default' => $manager);
    }

    /**
     * @inheritdoc
     */
    public function getConnection($name = null)
    {
        if ($name === null) {
            $name = 'default';
        }

        if (!isset($this->connections[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Connection named "%s" does not exist.', $name));
        }

        return $this->connections[$name];
    }

    /**
     * @inheritdoc
     */
    public function getConnectionNames()
    {
        return array_keys($this->connections);
    }

    /**
     * @inheritdoc
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultConnectionName()
    {
        return $this->connections['default'];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultManagerName()
    {
        return $this->managers['default'];
    }

    /**
     * @inheritdoc
     */
    public function getManager($name = null)
    {
        if ($name === null) {
            $name = 'default';
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }

        return $this->managers[$name];
    }

    /**
     * @inheritdoc
     */
    public function getManagerForClass($class)
    {
      $manager = $this->getDefaultManagerName();

      if (!$manager->getMetadataFactory()->isTransient($class)) {
          return $manager;
      }
    }

    /**
     * @inheritdoc
     */
    public function getManagerNames()
    {
        return array_keys($this->managers);
    }

    /**
     * @inheritdoc
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * @inheritdoc
     */
    public function getRepository($persistentObjectName, $persistentManagerName = 'default')
    {
        return $this->getManager($persistentManagerName)->getRepository($persistentObjectName);
    }

    /**
     * @inheritdoc
     */
    public function resetManager($name = null)
    {
        if ($name === null) {
            $name = 'default';
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }
    }

    /**
     * @inheritdoc
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