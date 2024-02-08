<?php 
$controller = 'UM';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new UMForm();
$_um = new UMRepository();
$_listUMs = $_um->getListUMs();

switch($action){
    case 'insert': 
        $form->populate($_POST);
        if($form->isValid()){            
            $_um->setOptions($_POST);
            $result = $_um->save($_um->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Unidad de medida registrada exitosamente.'));
                header('Location: UM.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'UM.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){
            $_umData = $_um->getById($id);
        }
        if($_POST){$_umData = $_POST;}
        
        $_login = new Login();
        if($_login->getRole() != '1'){
            $flashmessenger->addMessage(array('info'=>'No tienes permiso para acceder a este modulo.'));
            header('Location: Home.php');
        }
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_umData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_um->setOptions($_umData);
                $result = $_um->update($id,$_um->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Unidad de medida actualizado exitosamente.'));
                    header("Location: UM.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'UM.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'UM.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_um->isUsedInRecord($id)){
            if($_um->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Unidad de medida eliminada satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Unidad de medida no puede ser eliminada esta siendo utilizada en almenos un registro.'));
        }
        
        header("Location: UM.php");
        break;
        
     case 'ajax':
        $ajaxUM = new UMAjax();
        $json = $ajaxUM->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        
        $vista = 'UM.php';
        include $root.'/View/Template.php';
}