<?php

$controller = 'File';

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

$_fileForm = new FileForm();

$_file = new FileRepository();

$_listPriorities = $_file->getListPriorities();

switch ($action) {

    case 'insert':

        $_fileForm->populate($_POST);

        $_POST['attachments'] = $_FILES;

        if ($_fileForm->isValid()) {

            $_file->setOptions($_POST);
            
            $task_id = $_file->getOptions()['task_id'];
            
            
            $result = $_file->save($_file->getOptions());

            if ($result) {

                $flashmessenger->addMessage(array('success' => 'Genial !! El archivo se registro exitosamente.'));

                header("Location: Project.php?action=edit&id={$_POST['project_id']}&task={$task_id}");


            } else {

                $flashmessenger->addMessage(array('danger' => 'Oops !!, algo salio mal al intentar registrar el archivo. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        } else {



            // $vista = "Project.php";
            header("Location: Project.php?action=edit&id={$_POST['project_id']}");

            //                         $vista = 'Project.php';
            include $root . '/View/Template.php';
      
            $_noValid = true;


        }

        break;


    case 'edit':

        if ($_GET) {
            $_fileData = $_file->getById($id);
        }

        if ($_POST) {
            $_fileData = $_POST;
        }



        $_fileForm->setActionController('edit');

        $_fileForm->setId($id);

        $_fileForm->populate($_fileData);



        if (isset($_POST['id'])) {

            if ($_fileForm->isValid()) {

                $_file->setOptions($_fileData);

                $result = $_file->update($id, $_file->getOptions()); //no tengo id porque viene de post

                if ($result) {

                    $flashmessenger->addMessage(array('success' => 'Estupendo !! La prioridad se actualizo exitosamente.'));

                    header('Location: Priorities.php');

                } else {

                    $flashmessenger->addMessage(array('danger' => 'Oops !!, algo salio mal al intenta actualizar la prioridad. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }

            } else {

                $_noValid = true;

                $vista = 'Priorities.php';

                include $root . '/View/Template.php';

            }

        } else {

            $vista = 'Priorities.php';

            include $root . '/View/Template.php';

        }

        break;



    case 'delete':

        if (!$_file->isUsedInRecord($id)) {

            if ($_file->delete($id)) {

                $flashmessenger->addMessage(array('success' => 'Hecho !! La prioridad se elimino satisfactoriamente.'));

            }

        } else {

            $message = 'Oops !!, esta prioridad no puede ser eliminada, esta siendo utilizada en almenos un registro.';

            $flashmessenger->addMessage(array('info' => $message));

        }

        header('Location: Priorities.php');

        break;



    case 'ajax':

        $ajaxPrioritiesAjax = new FileAjax();



        $json = $ajaxPrioritiesAjax->getResponse($_POST['request'], $_POST);

        echo json_encode($json);

        break;



    default:

        $vista = 'Priorities.php';

        include $root . '/View/Template.php';

}