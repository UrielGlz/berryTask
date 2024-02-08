<?php
$controller = 'Home';
$action = '';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$vista = 'Home.php';
include ROOT . '/View/Template.php';