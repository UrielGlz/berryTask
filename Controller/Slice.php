<?php 
$controller = 'Slice';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$form = new SliceForm();
$_slice = new SliceRepository();
$_listSlices = $_slice->getListSlices();
$_noValid = null;

switch($action){
    case 'insert': 
        $settings = new SettingsRepository();
        if($_POST['category'] == $settings->_get('id_category_for_extra_cakes')){
            $form->noRequired(array('size','shape'));
        }
        $form->populate($_POST);
        if($form->isValid()){            
            $_slice->setOptions($_POST);
            $result = $_slice->save($_slice->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Parte del pastel registrada exitosamente.'));
                header('Location: Slice.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'Slice.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        $settings = new SettingsRepository();
        if($_POST['category'] == $settings->_get('id_category_for_extra_cakes')){
            $form->noRequired(array('size','shape'));
        }
        
        if($_GET){
            $_sliceData = $_slice->getById($id);
        }
        if($_POST){$_sliceData = $_POST;}
        
        $form->setActionController('edit');
        $form->setId($id);
        $form->populate($_sliceData);
        
        if(isset($_POST['id'])){
            if($form->isValid()){
                $_slice->setOptions($_sliceData);
                $result = $_slice->update($id,$_slice->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Parte del pastel actualizado exitosamente.'));
                    header("Location: Slice.php");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $_noValid = true;
                $vista = 'Slice.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'Slice.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if(!$_slice->isUsedInRecord($id)){
            if($_slice->delete($id)){
                $flashmessenger->addMessage(array('success'=>'Parte del pastel eliminado satisfactoriamente.'));
            }
        }else{
             $flashmessenger->addMessage(array('success'=>'Parte del pastel no puede ser eliminado esta siendo utilizado en almenos un registro.'));
        }
        
        header("Location: Slice.php");
        break;
        
     case 'ajax':
        $ajaxSlice = new SliceAjax();
        $json = $ajaxSlice->getResponse($_POST['request'], $_POST);
        echo json_encode($json);
        break;
    
    default:       
        $vista = 'Slice.php';
        include $root.'/View/Template.php';
}