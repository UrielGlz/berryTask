<?php 
$controller = 'Presentation';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new PresentationForm();
$_presentation = new PresentationRepository();
$_listPresentations = $_presentation->getListPresentations();

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_presentation->setOptions($_POST);
            $result = $_presentation->save($_presentation->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Presentacion registrada exitosamente.'));
                header('Location: Presentation.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Presentation.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_presentationData = $_presentation->getById($id);
        }
        if($_POST){$_presentationData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_presentationData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_presentation->setOptions($_presentationData);
                $result = $_presentation->update($id,$_presentation->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Presentacion actualizado exitosamente.'));
                    header("Location: Presentation.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Presentation.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Presentation.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_presentation->isUsedInRecord($id)){
            if($_presentation->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Presentacion eliminada satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Presentacion no puede ser eliminada esta siendo utilizada en almenos un registro.'));
        }
        
        header("Location: Presentation.php");
        break;
        
     case 'ajax':
        $ajaxPresentation = new PresentationAjax();
        $json = $ajaxPresentation->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        
        $vista = 'Presentation.php';
        include $root.'/View/Template.php';
}