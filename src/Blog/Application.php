<?php

namespace Blog;

use Blog\Controllers\BlogControllerProvider,
    Blog\Admin\Controllers\ResourceControllerProvider,
    Blog\Providers\DoctrineOrmServiceProvider,
    Blog\Providers\FormServiceProvider;

use Symfony\Component\Yaml\Yaml,
    Symfony\Component\Routing,
    Symfony\Component\Finder\Finder;

use Silex\Provider\SymfonyBridgesServiceProvider,
    Silex\Provider\TranslationServiceProvider;

class Application extends \Silex\Application
{
    function __construct() {
        parent::__construct();

        $app = $this;

        $this->loadConfigurationFiles();

        // Set default environment to devlopemnt
        // if no other environment has been set.
        if (!isset($this['environment'])) {
            $this['environment'] = isset($_ENV['environment']) ? $_ENV['environment'] : 'development';
        }

        // Set debug mode to true if envronment is not production.
        if ($this['environment'] !== 'production') {
            $this['debug'] = true;
        }

        // Default development config.
        if (!isset($this['config']['database'][$this['environment']])) {
            $db_options = $this['config']['database']['development'];
        } else {
            $db_options = $this['config']['database'][$this['environment']];
        }

        // Register doctrine orm service provider.
        $this->register(new DoctrineOrmServiceProvider(), array(
            'db.options' => $db_options,
            'db.entities' => array(APP_DIR.'/Blog/Entities'),
        ));

        $this->register(new SymfonyBridgesServiceProvider());

        $this->register(new TranslationServiceProvider(), array(
            'translator.messages' => array()
        ));

        $this->register(new FormServiceProvider());
        
        $ff = $this['form.factory'];

        $this->createDatabaseSchema();

        $this->mount('/', new BlogControllerProvider());

        $em = $this['db.entity_manager'];
        $classes = $em->getMetadataFactory()->getAllMetadata();

        foreach ($classes as $class) {
            $this->mount('/admin', new ResourceControllerProvider($class));
        }
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

    public function createRewriteRules()
    {
        $dumper = new Routing\Matcher\Dumper\ApacheMatcherDumper($this['routes']);
 
        $rules = $dumper->dump(array(
            'script_name' => 'index.php',
            'base_uri'    => '/projects/blog/public',
        ));

        var_dump($rules);

        if (!file_exists(PUBLIC_DIR.'/.htaccess')) {
            file_put_contents(PUBLIC_DIR.'/.htaccess', $rules);
        }
    }

    private function createDatabaseSchema()
    {
        $em = $this['db.entity_manager'];

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
    }
}