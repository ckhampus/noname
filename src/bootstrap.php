<?php

require __DIR__.'/../vendor/.composer/autoload.php';

define('APP_DIR', __DIR__);
define('ROOT_DIR', realpath(APP_DIR.'/../'));
define('CONFIG_DIR', ROOT_DIR.'/config');
define('PUBLIC_DIR', ROOT_DIR.'/public');
define('VENDOR_DIR', ROOT_DIR.'/vendor');

require APP_DIR.'/functions.php';

return require APP_DIR.'/app.php';
