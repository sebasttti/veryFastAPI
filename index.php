<?php

ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);

require_once __DIR__.'/environments/environment.php';
require_once __DIR__.'/environments/parameters.php';
require_once __DIR__.'/helpers/helpers.php';

require_once __DIR__.'/libs/Database.php';
require_once __DIR__.'/libs/Entity.php';
require_once __DIR__.'/libs/Controller.php';
require_once __DIR__.'/libs/Core.php';

$core = new Core();

?>