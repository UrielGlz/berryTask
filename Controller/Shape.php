<?php 
$controller = 'Shape';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new ShapeForm();
$_brand = new ShapeRepository();
$_listShapes = $_brand->getListShapes();
$_noValid = null;

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_brand->setOptions($_POST);
            $result = $_brand->save($_brand->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Forma registrada exitosamente.'));
                header('Location: Shape.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Shape.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_brandData = $_brand->getById($id);
        }
        if($_POST){$_brandData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_brandData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_brand->setOptions($_brandData);
                $result = $_brand->update($id,$_brand->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Forma actualizado exitosamente.'));
                    header("Location: Shape.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Shape.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Shape.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_brand->isUsedInRecord($id)){
            if($_brand->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Forma eliminada satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Forma no puede ser eliminada esta siendo utilizada en almenos un registro.'));
        }
        
        header("Location: Shape.php");
        break;
        
     case 'ajax':
        $ajaxShape = new ShapeAjax();
        $json = $ajaxShape->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        $vista = 'Shape.php';
        include $root.'/View/Template.php';
}