<?php 

$controller = 'Priorities';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$_paymentForm = new PrioritiesForm();

$_brand = new PrioritiesRepository();

$_listPriorities  = $_brand->getListPriorities();



switch($action){

    case 'insert':

        $_paymentForm->populate($_POST);

        if($_paymentForm->isValid()){

            $_brand->setOptions($_POST);

            $result = $_brand->save($_brand->getOptions());

            if($result){

                $flashmessenger->addMessage(array('success'=>'Genial !! La nueva prioridad se registro exitosamente.'));

                header('Location: Priorities.php');

            }else{

                $flashmessenger->addMessage(array('danger'=>'Oops !!, algo salio mal al intentar registrar la nueva prioridad. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        }else{

            $_noValid = true;

            $vista = 'Priorities.php';

            include $root.'/View/Template.php';

        }

        break;

   

    case 'edit':

        if($_GET){$_brandData = $_brand->getById($id);}

        if($_POST){$_brandData = $_POST;}

        

        $_paymentForm->setActionController('edit');

        $_paymentForm->setId($id);

        $_paymentForm->populate($_brandData);

        

        if(isset($_POST['id'])){

            if($_paymentForm->isValid()){

                $_brand->setOptions($_brandData);

                $result = $_brand->update($id,$_brand->getOptions()); //no tengo id porque viene de post

                if($result){

                    $flashmessenger->addMessage(array('success'=>'Estupendo !! La prioridad se actualizo exitosamente.'));

                    header('Location: Priorities.php');

                }else{

                    $flashmessenger->addMessage(array('danger'=>'Oops !!, algo salio mal al intenta actualizar la prioridad. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }       

            }else{

                $_noValid = true;

                $vista = 'Priorities.php';

                include $root . '/View/Template.php';

            }

        }else{

            $vista = 'Priorities.php';

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete': 

        if(!$_brand->isUsedInRecord($id)){

            if($_brand->delete($id)){

                $flashmessenger->addMessage(array('success'=>'Hecho !! La prioridad se elimino satisfactoriamente.'));                

            }

        }else{

            $message = 'Oops !!, esta prioridad no puede ser eliminada, esta siendo utilizada en almenos un registro.';

            $flashmessenger->addMessage(array('info'=>$message));

        }

        header('Location: Priorities.php');

        break;    

        

     case 'ajax':

        $ajaxPrioritiesAjax = new PrioritiesAjax();

        $json = $ajaxPrioritiesAjax->getResponse($_POST['request'], $_POST);

        echo json_encode($json);

        break;

    

    default:

        $vista = 'Priorities.php';

        include $root.'/View/Template.php';

}