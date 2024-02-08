<?php 

session_start();



$root = realpath($_SERVER["DOCUMENT_ROOT"].'');



//Data Base parameters

include $root . '/app/config/dbConfig.php';



//Autoloader

include $root.'/app/config/autoloader.php';

spl_autoload_register('autoloader::autoloadClasses');   



//Globals Vars

include $root.'/app/config/appConfig.php';



//ACL

include $root.'/app/include/acl.php';


// setLenguage() Esta seteado en Login

$_translator = new Translator();



//FlashMessenger

$flashmessenger = new FlashMessenger();



//Settings Default

$_settings = new SettingsRepository();



//Login

$_login = new Login();

$_login->setCurrentController($controller);

