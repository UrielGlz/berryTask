<?php

$controller = 'Project';

$action = '';

if (isset($_POST['action'])) {

    $action = $_POST['action'];

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }

} elseif (isset($_GET['action'])) {

    $action = $_GET['action'];

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

}

include $_SERVER["DOCUMENT_ROOT"] . '/app/include/bootstrap.php';


$_form = new ProjectForm();

$_brand = new ProjectRepository();

//$_listProject  = $_brand->getListProject();



switch ($action) {

    case 'insert':

        $_form->populate($_POST);

        if ($_form->isValid()) {

            $_brand->setOptions($_POST);

            //var_dump($_brand->getOptions());exit;
            $result = $_brand->save($_brand->getOptions());

            if ($result) {

                $flashmessenger->addMessage(array('success' => 'Genial !! El nuevo proyecto se registro exitosamente.'));

                header('Location: Project.php?action=list.php');

            } else {

                $flashmessenger->addMessage(array('danger' => 'Oops !!, algo salio mal al intentar registrar el nuevo proyecto. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        } else {

            $_noValid = true;

            $vista = 'Project.php';

            include $root . '/View/Template.php';

        }

        break;



    case 'edit':
        
        if ($_GET) {
            $entity = new EntityRepository();
            $_brandData = $_brand->_getBId($id);
          
            $_brandData['members'] = $_brand->UsersStringToArray($_brandData['members']);

            //var_dump($_brandData);exit;
        }

        if ($_POST) {
            $_brandData = $_POST;
        }
        //var_dump($_brandData);exit;



        $_form->setActionController('edit');

        $_form->setId($id);

        $_form->populate($_brandData);



        if (isset($_POST['id'])) {

            if ($_form->isValid()) {

                $_brand->setOptions($_brandData);

                $result = $_brand->update($id, $_brand->getOptions()); //no tengo id porque viene de post

                if ($result) {

                    $flashmessenger->addMessage(array('success' => 'Estupendo !! El proyecto se actualizo exitosamente.'));

                    header("Location: Project.php?action=edit&id=$id");

                } else {

                    $flashmessenger->addMessage(array('danger' => 'Oops !!, algo salio mal al intenta actualizar el proyecto. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }

            } else {

                $_noValid = true;

                $vista = 'Purchese.php';

                include $root . '/View/Template.php';

            }

        } else {

            $vista = 'ProjectDetail.php';

            include $root . '/View/Template.php';

        }

        break;

        case 'list':        

            $_queryString = NULL;
    
            $searchFilter = null;           
   
    
            $_listProjects  = $_brand->GetProjectsByUser($login->getId(),$login->getRole());
    
    
    
            $vista = 'ProjectList.php';       
    
            include $root . '/View/Template.php';
    
            break;



    case 'delete':

        if (!$_brand->isUsedInRecord($id)) {

            if ($_brand->delete($id)) {

                $flashmessenger->addMessage(array('success' => 'Hecho !! El proyecto se elimino satisfactoriamente.'));

            }

        } else {

            $message = 'Oops !!, el proyecto no puede ser eliminado, esta siendo utilizada en almenos un registro.';

            $flashmessenger->addMessage(array('info' => $message));

        }

        header('Location: Project.php');

        break;



    case 'ajax':

        $ajaxPrioritiesAjax = new ProjectAjax();

        $json = $ajaxPrioritiesAjax->getResponse($_POST['request'], $_POST);

        echo json_encode($json);

        break;


    default:

        $vista = 'Project.php';

        include $root . '/View/Template.php';

}