<?php 
$controller = 'TimeClock';
$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if (isset($_POST['id'])) {$id = $_POST['id'];}
}elseif(isset($_GET['action'])){
    $action = $_GET['action'];
    if (isset($_GET['id'])) {$id = $_GET['id'];}
}
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';

$_form = new TimeClockForm();
$_timeclock = new TimeClockRepository();
$searchFilter = null;
$_noValid = null;

if(isset($_POST['search'])){$searchFilter = $_POST;}
$_listaTimeClocks = $_timeclock->getListaTimeClock($searchFilter);

switch($action){
    case 'insert':
        $_form->populate($_POST);
        if($_form->isValid()){
            $_timeclock->setOptions($_POST);
            $result = $_timeclock->save($_timeclock->getOptions());
            if($result){
                $flashmessenger->addMessage(array('success'=>'Genial !! El tiempo se registro exitosamente.'));
                header('Location: TimeClock.php');
            }else{
                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
            }
        }else{
            $_noValid = true;
            $vista = 'TimeClock.php';
            include $root.'/View/Template.php';
        }
        break;
   
    case 'edit':
        if($_GET){$_timeclockData = $_timeclock->getById($id);}
        if($_POST){$_timeclockData = $_POST;}
        
        $_form->setActionController('edit');
        $_form->setId($id);
        $_form->populate($_timeclockData);
        
        if(isset($_POST['id'])){
            if($_form->isValid()){
                $_timeclock->setOptions($_timeclockData);
                $result = $_timeclock->update($id,$_timeclock->getOptions()); //no tengo id porque viene de post
                if($result){
                    $flashmessenger->addMessage(array('success'=>'Estupendo !! El Tiempo se actualizo exitosamente.'));
                    header("Location: TimeClock.php?action=edit&id=$id");
                }else{
                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));
                }       
            }else{
                $vista = 'TimeClock.php';
                include $root . '/View/Template.php';
            }
        }else{
            $vista = 'TimeClock.php';
            include $root . '/View/Template.php';
        }        
        break;
    
    case 'delete': 
        if($_timeclock->delete($id)){
            $flashmessenger->addMessage(array('success'=>'Hecho !! El Tiempo fue eliminado satisfactoriamente.'));
        }
       
        header('Location: TimeClock.php');
        break;
        
    case 'ajax':
        $ajaxTimeClock = new TimeClockAjax();
        $json = $ajaxTimeClock->getResponse($_POST['request'],$_POST);
        
        echo json_encode($json);
        break;       
    
    default:
        $vista = 'TimeClock.php';
        include $root.'/View/Template.php';
}