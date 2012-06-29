<?php

namespace Blog\Admin\Controllers;

use Blog\Controllers\Controller;

use Assetic\AssetManager,
    Assetic\Asset\FileAsset,
    Assetic\Asset\GlobAsset;

use Symfony\Component\Finder\Finder,
    Symfony\Component\HttpFoundation\Response;

class AssetController extends Controller
{
    private $folder;

    private $am;

    public function __construct($folder)
    {
        $this->folders = array(
            $folder.'/images',
            $folder.'/javascripts',
            $folder.'/stylesheets',
        );

        $this->am = new AssetManager();

        foreach ($this->folders as $path) {
            $finder = new Finder();

            $iterator = $finder
                ->files()
                ->in($path);

            foreach ($iterator as $file) {
                $asset_name = str_replace($path.'/', '', $file->getRealpath());
                $asset_name = str_replace('.', '_', $asset_name);
                $asset_name = str_replace('-', '_', $asset_name);

                $this->am->set($asset_name, new FileAsset($file->getRealpath()));
            }
        }

    }

    public function defineRoutes()
    {
        $this->get('/assets/{name}', array($this, 'assetAction'))->bind('asset');
    }

    public function assetAction($name)
    {
        $info = pathinfo($name);
        $this->getRequest()->setRequestFormat($info['extension']);

        $name = str_replace('.', '_', $name);
        $name = str_replace('-', '_', $name);

        $asset = $this->am->get($name);

        $response = new Response();
        $response->setContent($asset->dump());

        return $response;
    }
}
