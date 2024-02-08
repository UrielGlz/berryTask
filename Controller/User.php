<?php 

$controller = 'User';

$action = '';

if(isset($_POST['action'])){

    $action = $_POST['action'];

    if (isset($_POST['id'])) {$id = $_POST['id'];}

}elseif(isset($_GET['action'])){

    $action = $_GET['action'];

    if (isset($_GET['id'])) {$id = $_GET['id'];}

}


include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';



$_form = new UserForm();

$_user = new UserRepository();



$searchFilter = null;

if(isset($_POST['search'])){$searchFilter = $_POST;}

$_listUsers = $_user->getListUsers($searchFilter);



$_vistaName = 'User.php';

if($_login->getRole() != '1'){

    //$action = 'edit';

    //$id = $_login->getId();

    $_vistaName = 'User.php'; /*Aqui va Profile.php*/

}



switch($action){

    case 'insert':

        $_form->populate($_POST);

        if($_form->isValid()){

            $_user->setOptions($_POST);

            $_user->setImage($_FILES['photo']);

            $result = $_user->save($_user->getOptions());

            if($result){

                $flashmessenger->addMessage(array('success'=>'Usuario registrado exitosamente.'));

                header('Location: User.php');

            }else{

                $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        }else{

            $_noValid = true;

            $vista = $_vistaName;

            include $root.'/View/Template.php';

        }

        break;

   

    case 'edit':

        if($_GET){

            $_userData = $_user->getById($id); 

            $_userData['password'] = null;

            // $_userData['nip'] = null;

            // $_userData['store_id'] = $_user->storeStringToArray($_userData['store_id']);

        }elseif($_POST){

            $_userData = $_POST;}

        else{

            $_userData = $_user->getById($id); 

            $_userData['password'] = null;

            $_userData['nip'] = null;

            $_form->deleteElement('role');

            // $_form->deleteElement('store_id');

            $_form->deleteElement('status');

        }

        

        $_login = new Login();

        if($_login->getId() != $id && $_login->getRole() != '1'){

            $flashmessenger->addMessage(array('info'=>'No tienes permiso para acceder a este modulo.'));

            header('Location: Home.php');

        }

        

        $_form->setActionController('edit');

        $_form->setId($id);

        $_form->setEditForm();

        $_form->populate($_userData);

        

        if(isset($_POST['id'])){

            if($_form->isValid()){

                $_user->setOptions($_userData);

                $_user->setImage($_FILES['photo']);

                $result = $_user->update($id,$_user->getOptions()); //no tengo id porque viene de post

                if($result){

                    $flashmessenger->addMessage(array('success'=>'Usuario actualizado exitosamente.'));

                    header("Location: User.php");

                }else{

                    $flashmessenger->addMessage(array('danger'=>'Error. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }       

            }else{

                $_noValid = true;

                $vista = $_vistaName;

                include $root . '/View/Template.php';

            }

        }else{

            $vista = $_vistaName;

            include $root . '/View/Template.php';

        }        

        break;

    

    case 'delete': 

        if(!$_user->isUsedInRecord($id)){

            if($_user->delete($id)){

                $flashmessenger->addMessage(array('success'=>'Usuario eliminado exitosamente.'));

            }

        }else{

            $message = 'Este Usuario no puede ser eliminado, esta siendo utilizado en almenos un registro.';

            $flashmessenger->addMessage(array('info'=>$message));

        }

       header("Location: User.php");

        break;

        

     case 'ajax':

        $ajaxUsuario = new UserAjax(); 
      

        $json = $ajaxUsuario->getResponse($_POST['request'], $_POST);

        echo json_encode($json);

        break;

    

    default:



        $vista = $_vistaName;

        include $root.'/View/Template.php';

}