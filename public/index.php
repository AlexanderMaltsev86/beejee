<?php
require  __DIR__ . '/../vendor/autoload.php';
const BASE_PATH = __DIR__ . '/../';

use App\Application;

$application = new Application();
$application->handle();