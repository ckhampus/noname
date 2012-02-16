<?php

namespace Blog\Tests\Providers;

use Silex\Application;

use Blog\Providers\FormServiceProvider;

class FormServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProviderRegistration()
    {
        $app = new Application();
        
        $app->register(new FormServiceProvider(), array(
            'form.class_path' => ROOT_DIR.'/vendor/symfony/form',
        ));

        $this->assertInstanceOf('\Symfony\Component\Form\FormFactory', $app['form.factory']);
    }
}