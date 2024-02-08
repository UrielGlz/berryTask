<?php

$controller = 'Task';

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



$_taskForm = new TaskForm();

$_brand = new TaskRepository();

//$_listPriorities  = $_brand->getListPriorities();


//1.uno 2.dos 3.tres 4 5
switch ($action) {

    case 'insert':

        $_taskForm->populate($_POST);
        //$tu ='Direccion:  <a href="https://'.$_SERVER['HTTP_HOST'] . '/Controller/Project.php?action=edit&id='.$_POST['project_id'].'&task='.$_POST['project_id'].'">URL</a> ';

        //var_dump($_POST);exit;


        if ($_taskForm->isValid()) {

            $_brand->setOptions($_POST);
            $_task_id = $_brand->getOptions()['task_id'];
            $_task_name = $_brand->getOptions()['task_name'];
            $_description = $_brand->getOptions()['description'];

            $_responsable = $_brand->getOptions()['responsable'];

            $_due_date = $_brand->getOptions()['due_date'];


            $result = $_brand->save($_brand->getOptions());
            // var_dump($_brand->getOptions());exit;
            if ($result) {

                $flashmessenger->addMessage(array('success' => 'Genial !! La tarea se inserto exitosamente.'));


                $user = new UserRepository();
                $dataUser = $user->getById($_responsable);

                if (!empty($dataUser['email'])) {
                    try {
                        $dataTask = $_brand->getTaskById($result);

                        $message = 'Una nueva tarea ha sido asignada con la siguiente descripción:  ' . $dataTask[0]['description'] . ' <br />'
                            . '<br /> Fecha de entrega: ' . $dataTask[0]['format_due_date'] . ' '
                            . '<br /> Estado: ' . $dataTask[0]['status_name'] . ' '
                            . '<br /> Categoria: ' . $dataTask[0]['category_name'] . ' '
                            . '<br /> Prioridad: ' . $dataTask[0]['prioritie_name'] . ' '

                            . '<br /> <br />  Descripcion: ' . $dataTask[0]['description'] . ' '
                            . '<br /> Direccion: <a href="https://' . $_SERVER['HTTP_HOST'] . '/Controller/Project.php?action=edit&id=' . $_POST['project_id'] . '&task=' . $result . '">URL </a>  ';
                        $emailer = new Emailer();
                        $emailer->sendEmail(
                            array(
                                'to' => $dataUser['email'],
                                //$data['to']
                                //'cc' => 'uriel.glz.sj@gmail.com', //$data['cc'],
                                'subject' => $_task_name,
                                //$data['subject'],
                                'from_title' => 'Berry Task',
                                //$company->getName(),
                                'message' => $message,
                                //'attachment'=>$specialOrderPdf
                            )
                        );

                        //$flashmessenger->addMessage(array('success' => 'El correo se envio correctamente.'));

                    } catch (Exception $ex) {
                        $flashmessenger->addMessage(array('danger' => 'Oops =(. Algo salio mal al tratar de enviar el correo. Intenta nuevamente' . $ex->getMessage()));

                    }
                }


                if ($_task_id) {
                    $result = $_task_id;
                }
                header("Location: Project.php?action=edit&id={$_POST['project_id']}&task={$result}");


            } else {

                $flashmessenger->addMessage(array('danger' => 'Oops !!, algo salio mal al intentar actualizar. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

            }

        } else {

            $_noValid = true;

            $vista = 'Priorities.php';

            include $root . '/View/Template.php';

        }

        break;



    case 'edit':

        //if($_GET){$_brandData = $_brand->getById($id);}

        if (isset($_FILES)) {
            $_POST['attachments'] = $_FILES; //FILE Controller
        }

        if ($_POST) {
            $_brandData = $_POST;
        }

        // var_dump($_brandData);exit;
        $id = $_brandData['task_id'];


        $_taskForm->setActionController('edit');

        $_taskForm->setId($id);

        $_taskForm->populate($_brandData);





        if (isset($_POST['task_id'])) {

            if ($_taskForm->isValid()) {

                $_brand->setOptions($_brandData);

                $_responsable = $_brand->getOptions()['responsable'];

                $originData = $_brand->getById($id, 'tasks');

                $result = $_brand->update($id, $_brand->getOptions()); //no tengo id porque viene de post

                if ($result) {

                    $entity = new EntityRepository();


                    $modifications = $entity->getUpdatedFields($originData, $_brand->getOptions());
                  
                    if (!empty($modifications['responsable'])) { //Se envia notificacion cuando el responsable ha cambiado
                        $user = new UserRepository();
                        $dataUser = $user->getById($_responsable);
                       

                        if (!empty($dataUser['email'])) {
                            try {
                                $dataTask = $_brand->getTaskById($id);
                                //var_dump($dataTask);exit;
                                $message = 'Una nueva tarea ha sido asignada con la siguiente descripción:  ' . $dataTask[0]['description'] . ' <br />'
                                    . '<br /> Fecha de entrega: ' . $dataTask[0]['format_due_date'] . ' '
                                    . '<br /> Estado: ' . $dataTask[0]['status_name'] . ' '
                                    . '<br /> Categoria: ' . $dataTask[0]['category_name'] . ' '
                                    . '<br /> Prioridad: ' . $dataTask[0]['prioritie_name'] . ' '
                                    . '<br /> <br />  Descripcion: ' . $dataTask[0]['description'] . ' '
                                    . '<br /> Direccion: <a href="https://' . $_SERVER['HTTP_HOST'] . '/Controller/Project.php?action=edit&id=' . $_POST['project_id'] . '&task=' . $result . '">URL </a>  ';
                                $emailer = new Emailer();
                                $emailer->sendEmail(
                                    array(
                                        'to' => $dataUser['email'],
                                        //$data['to']
                                        //'cc' => 'uriel.glz.sj@gmail.com', //$data['cc'],
                                        'subject' => $_task_name,
                                        //$data['subject'],
                                        'from_title' => 'Berry Task',
                                        //$company->getName(),
                                        'message' => $message,
                                        //'attachment'=>$specialOrderPdf
                                    )
                                );

                                //$flashmessenger->addMessage(array('success' => 'El correo se envio correctamente.'));

                            } catch (Exception $ex) {
                                $flashmessenger->addMessage(array('danger' => 'Oops =(. Algo salio mal al tratar de enviar el correo. Intenta nuevamente' . $ex->getMessage()));

                            }
                        }
                    }


                    $flashmessenger->addMessage(array('success' => 'Genial !! La tarea se actualizó exitosamente.'));

                    header("Location: Project.php?action=edit&id={$_POST['project_id']}&task={$id}");


                } else {

                    $flashmessenger->addMessage(array('danger' => 'Oops !!, algo salio mal al intenta actualizar la tarea. Intenta nuevamente o contacta a tu proveedor de sistemas.'));

                }

            } else {

                $_noValid = true;

                $vista = 'Project.php';

                include $root . '/View/Template.php';

            }

        } else {

            $vista = 'Project.php';

            include $root . '/View/Template.php';

        }

        break;



    case 'delete':

        if (!$_brand->isUsedInRecord($id)) {

            if ($_brand->delete($id)) {

                $flashmessenger->addMessage(array('success' => 'Hecho !! La prioridad se elimino satisfactoriamente.'));

            }

        } else {

            $message = 'Oops !!, esta prioridad no puede ser eliminada, esta siendo utilizada en almenos un registro.';

            $flashmessenger->addMessage(array('info' => $message));

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

        include $root . '/View/Template.php';

}