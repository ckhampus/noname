<?php
namespace Blog\Controllers;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;

abstract class Controller implements ControllerProviderInterface
{
    private $app;

    private $controllers;

    /**
     * @inheritdoc
     */
    public function connect(Application $app)
    {
        $this->app = $app;
        $this->controllers = new ControllerCollection();
        $this->defineRoutes();
        return $this->controllers;
    }

    /**
     * Defines routes for the controller.
     * 
     * @return Silex\ControllerCollection
     */
    abstract public function defineRoutes();

    /**
     * Maps a pattern to a callable.
     *
     * You can optionally specify HTTP methods that should be matched.
     *
     * @param string $pattern Matched route pattern
     * @param mixed $to Callback that returns the response when matched
     * 
     * @return Silex\Controller
     */
    public function match($pattern, $to)
    {
        return $this->controllers->match($pattern, $to);
    }

    /**
     * Maps a GET request to a callable.
     *
     * @param string $pattern Matched route pattern
     * @param mixed $to Callback that returns the response when matched
     * 
     * @return Silex\Controller
     */
    public function get($pattern, $to)
    {
        return $this->controllers->get($pattern, $to);
    }

    /**
     * Maps a POST request to a callable.
     *
     * @param string $pattern Matched route pattern
     * @param mixed $to Callback that returns the response when matched
     * 
     * @return Silex\Controller
     */
    public function post($pattern, $to)
    {
        return $this->controllers->post($pattern, $to);
    }

    /**
     * Maps a PUT request to a callable.
     *
     * @param string $pattern Matched route pattern
     * @param mixed $to Callback that returns the response when matched
     * 
     * @return Silex\Controller
     */
    public function put($pattern, $to)
    {
        return $this->controllers->put($pattern, $to);
    }

    /**
     * Maps a DELETE request to a callable.
     *
     * @param string $pattern Matched route pattern
     * @param mixed $to Callback that returns the response when matched
     * 
     * @return Silex\Controller
     */
    public function delete($pattern, $to)
    {
        return $this->controllers->delete($pattern, $to);
    }

    /**
     * Returns the application instance.
     * 
     * @return Blog\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    public function getEntityManager()
    {
        return $this->app['db.entity_manager'];
    }

    /**
     * Returns a rendered view.
     *
     * @param string   $view The view name
     * @param array    $parameters An array of parameters to pass to the view
     *
     * @return string  The renderer view
     */
    public function render($name, array $context = array())
    {
        return $this->app['twig']->render($name, $context);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string  $url The URL to redirect to
     * @param integer $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return $this->app->redirect($url, $status);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string  $route      The name of the route
     * @param mixed   $parameters An array of parameters
     * @param Boolean $absolute   Whether to generate an absolute URL
     *
     * @return string The generated URL
     */
    public function generateUrl($name, $parameters = array(), $absolute = false)
    {
        return $this->app['url_generator']->generate($name, $parameters, $absolute);
    }

    private function createFormBuilder($data)
    {
        $builder = $this->app['form.factory']->createBuilder('form', $data);
        return $builder;
    }
}