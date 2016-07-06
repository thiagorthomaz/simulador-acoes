<?php

$core_path = "/var/www/html/stphp";
$app_path = "/var/www/html/simulador-acoes";

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once $core_path . '/stphp/config/config.php';
require_once $core_path . '/stphp/STPHP.class.php';


stphp\config\AutoLoad::addNamespace("stphp", $core_path . "/stphp");
stphp\config\AutoLoad::addNamespace("stphp\\Database", $core_path . "/stphp/Database");
stphp\config\AutoLoad::addNamespace("stphp\\Exception", $core_path . "/stphp/Exception");
stphp\config\AutoLoad::addNamespace("stphp\\rest", $core_path . "/stphp/rest");
stphp\config\AutoLoad::addNamespace("stphp\\http", $core_path . "/stphp/http");

stphp\config\AutoLoad::addNamespace("app\\config", $app_path . "/config");
stphp\config\AutoLoad::addNamespace("app\\controller", $app_path . "/controller");
stphp\config\AutoLoad::addNamespace("app\\model", $app_path . "/model");
stphp\config\AutoLoad::addNamespace("app\\view", $app_path . "/view");
stphp\config\AutoLoad::addNamespace("app\\exception", $app_path . "/exception" );

stphp\STPHP::registerExtensions();
stphp\STPHP::registerAutoload();

$imp = new \app\controller\Importador();
$imp->importarBase();

