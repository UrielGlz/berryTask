<?php 

$controller = 'CategoryFiles';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}

include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$_paymentForm = new CategoryFilesForm();

$_brand = new CategoryFilesRepository();

$_listCategoryFiles  = $_brand->getListCategoryFiles();



switch($action){

    case 'insert':

        $_paymentForm->populate($_POST);

        if($_paymentForm->isValid()){

            $_brand->setOptions($_POST);

            $result = $_brand->save($_brand->getOptions());

            if($result){

                $flashmessenger->addMessage(array('success'=>'Genial !! La nueva categoria de archivo se registro exitosamente.'));

                header('Location: CategoryFiles.php');

            }else{

                $flashmessenger->addMessage(array('danger'=>'Oops !!, algo salio mal al intentar registrar la nueva categoria de archivo. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        }else{

            $_noValid = true;

            $vista = 'CategoryFiles.php';

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

                    $flashmessenger->addMessage(array('success'=>'Estupendo !! La categoria de la archivo se actualizo exitosamente.'));

                    header('Location: CategoryFiles.php');

                }else{

                    $flashmessenger->addMessage(array('danger'=>'Oops !!, algo salio mal al intenta actualizar la categoria de la archivo. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }       

            }else{

                $_noValid = true;

                $vista = 'CategoryFiles.php';

                include $root . '/View/Template.php';

            }

        }else{

            $vista = 'CategoryFiles.php';

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete': 

        if(!$_brand->isUsedInRecord($id)){

            if($_brand->delete($id)){

                $flashmessenger->addMessage(array('success'=>'Hecho !! La categoria de la archivo se elimino satisfactoriamente.'));                

            }

        }else{

            $message = 'Oops !!, esta categoria de la archivo no puede ser eliminada, esta siendo utilizada en almenos un registro.';

            $flashmessenger->addMessage(array('info'=>$message));

        }

        header('Location: CategoryFiles.php');

        break;    

        

     case 'ajax':

        $ajaxCategoryFilesAjax = new CategoryFilesAjax();

        $json = $ajaxCategoryFilesAjax->getResponse($_POST['request'], $_POST);

        echo json_encode($json);

        break;

    

    default:

        $vista = 'CategoryFiles.php';

        include $root.'/View/Template.php';

}