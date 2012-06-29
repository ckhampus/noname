<?php

namespace Blog;

use Blog\Controllers\BlogController,
    Blog\Admin\Controllers\DashboardController,
    Blog\Admin\Controllers\DatabaseController,
    Blog\Admin\Controllers\AssetController,
    Blog\Admin\Controllers\ResourceController,
    Blog\Providers\DoctrineOrmServiceProvider,
    Blog\Providers\FormServiceProvider,
    Blog\RequestListener;

use Symfony\Component\Yaml\Yaml,
    Symfony\Component\Routing,
    Symfony\Component\Finder\Finder;

use Silex\Provider\SymfonyBridgesServiceProvider,
    Silex\Provider\TranslationServiceProvider,
    Silex\Provider\TwigServiceProvider,
    Silex\Provider\UrlGeneratorServiceProvider;

class Application extends \Silex\Application
{
    public function __construct()
    {
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

        // Register twig service provider.
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => array(ROOT_DIR.'/admin/views', ROOT_DIR.'/themes/default'),
        ));

        // Register url service provider.
        $app->register(new UrlGeneratorServiceProvider());

        // Register translation provider.
        $this->register(new TranslationServiceProvider(), array(
            'translator.messages' => array()
        ));

        $this['dispatcher']->addSubscriber(new RequestListener());

        // Register form service provider.
        $this->register(new FormServiceProvider());

        // Mount blog controller under root.
        $this->mount('/', new BlogController());

        // Mount admin dashboard under admin root.
        $this->mount('/admin', new DashboardController());

        // Mount database admin under admin root.
        $this->mount('/admin', new DatabaseController());

        // Mount database admin under admin root.
        $this->mount('/admin', new AssetController(ROOT_DIR.'/admin/assets'));

        $em = $this['db.entity_manager'];
        $classes = $em->getMetadataFactory()->getAllMetadata();

        // Mount all resources under admin root.
        foreach ($classes as $class) {
            $this->mount('/admin', new ResourceController($class));
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

    private function createDatabaseSchema()
    {
        $em = $this['db.entity_manager'];

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
    }
}
