<?php

namespace Blog;

use Blog\Controller\BlogControllerProvider,
    Blog\Provider\DoctrineOrmServiceProvider;

use Symfony\Component\Yaml\Yaml,
    Symfony\Component\Finder\Finder;

class Application extends \Silex\Application
{
    function __construct() {
        parent::__construct();

        $this->loadConfigurationFiles();

        // Set default environment to devlopemnt
        // if no other environment has been set.
        if (!isset($this['environment'])) {
            $this['environment'] = isset($_ENV['environment']) ? $_ENV['environment'] : 'development';
        }

        if (!isset($this['config']['database'][$this['environment']])) {
            $db_options = $this['config']['database']['development'];
        } else {
            $db_options = $this['config']['database'][$this['environment']];
        }

        $this->register(new DoctrineOrmServiceProvider(), array(
            'db.options' => $db_options,
            'db.entities' => array(APP_DIR.'/Blog/Entity'),
        ));
        
        $this->mount('/', new BlogControllerProvider());
    }

    /**
     * Loads all configuration files.
     */
    private function loadConfigurationFiles()
    {
        $finder = new Finder();

        $iterator = $finder
            ->files()
            ->name('*.yml')
            ->in(CONFIG_DIR);

        $config = array();

        foreach ($iterator as $file) {
            $content = file_get_contents($file->getRealPath());

            if (!empty($content)) {
                $array = Yaml::parse($content);
                $config[$file->getBasename('.'.$file->getExtension())] = $array;
            }
        }

        $this['config'] = $config;
    }

    private function createDatabaseSchema()
    {
        $em = $this['db.entity_manager'];

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();
        $tool->createSchema($classes);
    }
}