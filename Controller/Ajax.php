<?php 
$controller = 'Ajax';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

switch($action){
    case 'ajax':
        $ajax = new Ajax();
        if(isset($_POST['request'])){$data = $_POST;}
        if(isset($_GET['request'])){$data = $_GET;}
        
        $json = $ajax->getResponse($data['request'],$data);
        echo json_encode($json);
        break;
    
    case 'ajaxModalForms':
        $ajax = new AjaxModalForms();   
        $json = $ajax->getResponse($_POST['request'],$_POST);
        echo json_encode($json);
        break;
    
    
}