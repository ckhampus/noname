<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blog\Providers;

use Silex\Application,
    Silex\ServiceProviderInterface;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider,
    Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider,
    Symfony\Component\Form\FormFactory,
    Symfony\Component\Form\Extension\Core\CoreExtension,
    Symfony\Component\Form\Extension\Validator\ValidatorExtension,
    Symfony\Component\Form\Extension\Csrf\CsrfExtension;

/**
 * Symfony Form component Provider.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FormServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['form.secret'] = md5(__DIR__);

        $app['form.factory'] = $app->share(function () use ($app) {
            $extensions = array(
                new CoreExtension(),
                new CsrfExtension($app['form.csrf_provider']),
            );

            if (isset($app['db'])) {
                new \Blog\Registry($app['db'], $app['db.entity_manager']);

                //$registry = new \Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension($app['db.entity_manager']);
            }

            if (isset($app['validator'])) {
                $extensions[] = new ValidatorExtension($app['validator']);
            }

            return new FormFactory($extensions);
        });

        $app['form.csrf_provider'] = $app->share(function () use ($app) {
            if (isset($app['session'])) {
                return new SessionCsrfProvider($app['session'], $app['form.secret']);
            }

            return new DefaultCsrfProvider($app['form.secret']);
        });

        if (isset($app['form.class_path'])) {
            $app['autoloader']->registerNamespace('Symfony\\Component\\Form', $app['form.class_path']);
        }
    }
}