<?php

/**

 * Description of Ajax

 *

 * @author Uriel

 */

class ProjectAjax extends ProjectRepository
{



    public $flashmessenger = null;



    public function __construct()
    {

        if (!$this->flashmessenger instanceof FlashMessenger) {

            $this->flashmessenger = new FlashMessenger();

        }

    }


    public function getResponse($request, $options)
    {

        return $this->$request($options);

    }


    public function _getTranslation($text)
    {

        $translator = new Translator();

        return $translator->_getTranslation($text);

    }

    public function getTranslation($options)
    {

        $msj = $options['msj'];



        return array(

            'response' => true,

            'translation' => $this->_getTranslation($msj)

        );

    }



    public function getDataToEdit($options)
    {

        $qualityRepo = new ProjectRepository();

        $data = $qualityRepo->getById($options['id']);

        $data['action'] = 'edit';



        return array(

            'response' => true,

            'prioritiesData' => $data

        );

    }


    public function deletePriorities($options)
    {

        $PrioritiesRepo = new ProjectRepository();

        if (!$PrioritiesRepo->isUsedInRecord($options['id'])) {
            if ($PrioritiesRepo->delete($options['id'])) {

                $this->flashmessenger->addMessage(array('success' => 'El proyecto se elimino exitosamente.'));

            }
        } else {
            $message = 'Oops !!, este proyecto no puede ser eliminada, esta siendo utilizada en almenos un registro.';

            $this->flashmessenger->addMessage(array('info' => $message));

        }

        return array(

            'response' => true

        );

    }

    public function saveTask($options)
    {
        $data = array();
        //$dataPartenTask = array();
        //$resultPartenTask = "";
        foreach ($options['options'] as $row) {
            $data[$row['name']] = $row['value'];
        }
        if (isset($_FILES)) {
            $_POST['attachments'] = $_FILES; //FILE Controller
        }


        $entity = new TaskRepository();
        $entity->setOptions($data);
        //        if (trim($data['task_name']) == '' || trim($data['description']) == '' || trim($data['due_date']) == '' || trim($data['responsable']) == '' || trim($data['category_id']) == '' || trim($data['prioritie_id']) == '') {

        if (trim($data['task_name']) == '' || trim($data['responsable']) == '' || trim($data['prioritie_id']) == '') {
            $this->flashmessenger->addMessage(array('danger' => 'Lo sentimos. El campo de tarea,responsable y la prioridad son requeridos.'));
            return array(
                'response' => false,
                'message' => $this->flashmessenger->getMessageString()
            );
        }

        if ($data['action'] == 'insert') {
            $result = $entity->save($entity->getOptions());
            $lastCliente = $entity->getLastInsertId();

        } elseif ($data['action'] == 'edit') {
            unset($data['token_form'], $data['action'], $data['id']);
            $result = $entity->update($options['customer'], $data);
            $lastCliente = $options['customer'];
        }

        if ($result) {
            $this->flashmessenger->addMessage(array('success' => 'Excelente!! La tarea se registro exitosamente.'));
            // $dataPartenTask[0]['name'] = 'id';
            // $dataPartenTask[0]['value'] = $data['project_id'];

            // $resultPartenTask = $this->getTaskByProject($dataPartenTask);
            // echo "<pre>";var_dump($resultPartenTast);echo "</pre>";exit;
        } else {
            $this->flashmessenger->addMessage(array('danger' => 'Opss. Algo salio mal al intetar registrar la tarea.'));
        }

        return array(
            'response' => true,
            'message' => $this->flashmessenger->getMessageString(),
        );
    }
    public function getTaskByProject($options)
    {
        $listTask = "";
        //echo "<pre>";var_dump($options);echo "</pre>";exit;

        foreach ($options['options'] as $row) {
            $data[$row['name']] = $row['value'];
        }
        $login = new Login();
        $userId = $login->getId();
        $_rol = $login->getRole();
        $taskList = new TaskRepository();
        $result = $taskList->getTaskByProject($data['id'], $userId, $_rol, $data['parent_task_id']);
        $count = 0;
        $in = "in";
        $box_type = "";
        $button = "";
        $button_parent = "";
        // echo "<pre>";var_dump($result);echo "</pre>";exit;
        if (isset($result)) {
            foreach ($result as $detalle) {

                switch ($detalle['status']) {
                    case '1':
                        $box_type = "class='label label-primary'"; // Nueva
                        break;
                    case '2':
                        $box_type = "class='label label-info'"; // En proceso
                        break;
                    case '3':
                        $box_type = "class='label label-warning'"; // En riesgo
                        break;
                    case '4':
                        $box_type = "class='label label-danger'"; // Retresado
                        break;
                    case '5':
                        $box_type = "class='label label-success'"; // Terminado
                        break;
                }


                if ($detalle['status'] == "5") {

                    $button = "<div class='input-group-btn'> </div>";

                } else if ($detalle['due_date'] >= $detalle['actualDate']) {

                    $button = "<div class='input-group-btn'> <button type='button' id='{$detalle['task_id']}' onclick='closeTask({$detalle['task_id']})'  class='btn btn-warning'><i class='fa fa-check-square-o'></i></button></div>";
                } else if ($detalle['due_date'] < $detalle['actualDate']) {

                    $button = "<div class='input-group-btn'> <button type='button' id='{$detalle['task_id']}' onclick='closeTask({$detalle['task_id']})'  class='btn btn-warning'><i class='fa fa-check-square-o'></i></button></div>";

                }
                if ($count > 0) {
                    $in = "";
                }
                if (isset($detalle['parent'])) {
                    // if($detalle['parent_task_id'] == '0'){
                    //     $button_parent = "<button type='button'  onclick='getParentTask({$detalle['task_id']})' class='btn btn-sm btn-default'><i class='fa fa-link'></i></button>";

                    // }else{
                    //     $button_parent = "<button type='button'  onclick='getParentTask({$detalle['parent_task_id']})' class='btn btn-sm btn-default'><i class='fa fa-link'></i></button>";
                    // }
                    if ($detalle['parent'] > 0) {
                        $button_parent = "<a onclick='getParentTask({$detalle['task_id']})' class='btn btn-sm btn-default'><i class='fa fa-link'></i></a>";
                    } else {
                        $button_parent = "";
                    }

                } else {
                    $button_parent = "";
                }
                // $listTask .= "<div class='panel box {$box_type}'>"
                //     . "<div class='box-header with-border'>"

                //     . "<h5 class='box-title'>"
                //     . " <a data-toggle='collapse' data-parent='#accordion' href='#collapse_{$detalle['id']}'>"
                //     . $detalle['format_due_date'] . " - " . $detalle['task_name']
                //     . "</a>"
                //     . "</h5>"
                //     . "<small class='label label-default  pull-right'><i class='fa fa-user'></i>"
                //     . $detalle['userName']    
                //     . "</small>"
                //     . "<a class='btn btn-info btn-xs _showTask' id='task_{$detalle['id']}' onclick='getTaskById({$detalle['id']})'>    Ver mas</a>"
                //     . "</div>"


                //     . "<div id='collapse_{$detalle['id']}' class='panel-collapse collapse {$in}'>"
                //     . "<div class='box-body'>"
                //     . $detalle['description']
                //     . "</div>"
                //     . "</div>"
                //     . "</div>";


                $listTask .= "<tr>"
                    //."<td><label><input type='checkbox' class='flat-red' checked></label></td>"
                    . "<td class='text-center>"
                    . $button
                    . "</td> "
                    //."<td><button id='{$detalle['id']}' type='button' class='btn btn-default btn-sm checkbox-toggle'><i class='fa fa-square-o'></i></button></td>"
                    . "<td class='text-center'>" . $detalle['task_name'] . "</td> "
                    . "<td class='text-center'>" . $detalle['format_due_date'] . "</td> "

                    . "<td class='text-center'><span style='color:{$detalle['color_category']}'>" . $detalle['category_name'] . "</span></td> "


                    . "<td class='text-center'><span style='color:{$detalle['color']}'>" . $detalle['prioritie_name'] . "</span></td> "

                    //."<td class='text-center'><span class='badge bg-gree' style='color:{$detalle['color']}'>".$detalle['prioritie_name']."</span></td>"
                    . "<td class='text-center'><span style='color:{$detalle['colorNameUser']}'>" . $detalle['initialName'] . "</span></td> "

                    . "<td class='text-center'><span {$box_type}>" . $detalle['status_name'] . "</span></td> "
                    . "<td class='text-right' style='white-space: nowrap'> "
                    //. "<div class='input-group-btn'> "
                    . $button_parent
                    . " <a id='{$detalle['task_id']}' onclick='getTaskById({$detalle['task_id']})'  class='btn btn-info btn-sm'><i class='fa fa-eye'></i></a> "
                    //. "</div>"

                    . "</td> "
                    . "</tr> ";
                $count++;
            }
            //echo "<pre>";var_dump($listTask);echo "</pre>";exit;

            return array(
                'response' => true,
                'accordionProject' => $listTask
            );
        }
        $this->flashmessenger->addMessage(array('info' => 'Opss. No hay registros que mostrar.'));
        return array(

            'response' => false,
            'message' => $this->flashmessenger->getMessageString(),

        );

    }

    public function getTaskById($id)
    {

        $tools = new Tools();
        $TaskRepo = new TaskRepository();
        $fileRepo = new FileRepository();

        $user = new UserRepository();
        $data = $TaskRepo->getTaskById($id['id_task']);
        $ulFiles = $fileRepo->getListFiles($id['id_task']);

        $userName = $user->getById($data[0]['responsable']);

        //Validar fecha due_date, ver si ya es vencida la tarea

        if ($data[0]['status'] == "5") {
            $data[0]['button_due_date'] = "btn-success";

        } else if ($data[0]['due_date'] >= $data[0]['actualDate']) {

            $data[0]['button_due_date'] = "btn-warning";

        } else if ($data[0]['due_date'] < $data[0]['actualDate']) {

            $data[0]['button_due_date'] = "btn-danger";

        }

        $data['action'] = 'edit';
        $data[0]['action'] = 'edit';
        if (isset($userName)) {
            $data['userName'] = $userName['name'];
            $data['initials'] = $userName['initials'];
            $data['userColor'] = $userName['color'];

        } else {
            $data['userName'] = "Sin Asigar";
            $data['initials'] = "";
            $data['userColor'] = "";
        }

        $data[0]['due_date'] = $tools->setFormatDateToForm($data[0]['due_date']);

        $data[0]['progreso_task'] = round($data[0]['progreso_task'], 2);

        //  echo "<pre>";var_dump($data);echo "</pre>";exit;

        return array(

            'response' => true,

            'taskDetail' => $data,

            'ulFiles' => $ulFiles

        );

    }

    public function closeTaskById($options)
    {


        $TaskRepo = new TaskRepository();
        unset($options["action"]);
        unset($options["request"]);
        unset($options["token_form"]);

        $result = $TaskRepo->update($options['id'], $options);
        //$data['action'] = 'edit';    
        // echo "<pre>";var_dump($result);echo "</pre>";exit;

        if ($result) {
            $this->flashmessenger->addMessage(array('success' => 'Excelente!! La tarea se cerro exitosamente.'));
        } else {
            $this->flashmessenger->addMessage(array('danger' => 'Opss. Algo salio mal al intetar cerrar la tarea.'));
        }

        return array(
            'response' => true,
            'message' => $this->flashmessenger->getMessageString(),
        );


    }
    public function startTaskById($options)
    {

        foreach ($options['options'] as $row) {
            $data[$row['name']] = $row['value'];
        }

        // echo "<pre>";var_dump($data);echo "</pre>";exit;
        $TaskRepo = new TaskRepository();
        unset($data["action"]);
        unset($data["request"]);
        unset($data["token_form"]);
        $data["id"] = $data["task_id"];
        unset($data["task_id"]);



        $data['date_start'] = date('m/d/y h:i:s');



        $result = $TaskRepo->update($data["id"], $data);


        //$data['action'] = 'edit';    


        if ($result) {
            $this->flashmessenger->addMessage(array('success' => 'Excelente!! La tarea se inicio exitosamente.'));
        } else {
            $this->flashmessenger->addMessage(array('danger' => 'Opss. Algo salio mal al intetar cerrar la tarea.'));
        }

        return array(
            'response' => true,
            'message' => $this->flashmessenger->getMessageString(),
        );


    }
    public function saveComment($options)
    {
        $data = array();
        foreach ($options['options'] as $row) {
            $data[$row['name']] = $row['value'];
        }

        // echo "<pre>";var_dump($data);echo "</pre>";exit;

        $entity = new CommentRepository();
        $entity->setOptions($data);

        if (trim($data['comment']) == '') {
            $this->flashmessenger->addMessage(array('danger' => 'Lo sentimos. Todos los campos son requeridos.'));
            return array(
                'response' => false,
                'message' => $this->flashmessenger->getMessageString()
            );
        }

        if ($data['action'] == 'insert') {
            $result = $entity->save($entity->getOptions());


        } elseif ($data['action'] == 'edit') {
            unset($data['token_form'], $data['action'], $data['id']);
            $result = $entity->update($options['id'], $data);
        }

        if ($result) {
            $this->flashmessenger->addMessage(array('success' => 'Excelente!! El comentaro se registro exitosamente.'));
        } else {
            $this->flashmessenger->addMessage(array('danger' => 'Opss. Algo salio mal al intentar registrar el comentario.'));
        }

        return array(
            'response' => true,
            'message' => $this->flashmessenger->getMessageString(),
        );
    }
    function getCommentsByIdTask($options)
    {
        $list_comment = "";
        foreach ($options['options'] as $row) {
            $data[$row['name']] = $row['value'];
        }

        $taskList = new CommentRepository();
        $result = $taskList->getCommentById($data['task_id']);
        //echo "<pre>";var_dump($result);echo "</pre>";exit;
        if (isset($result)) {
            foreach ($result as $detalle) {

                $list_comment .= "<li class='time-label'>"
                    // . "<span class='bg-red'>" . $detalle['creado_fecha'] . " </span>"
                    . "</li>"
                    . "<li>"
                    . "<i class='fa fa-envelope bg-blue'></i>"
                    . "<div class='timeline-item'>"
                    . "<span class='time'><i class=fa fa-clock-o'></i>  " . $detalle['creado_fecha'] . " </span>"
                    . "<h3 class='timeline-header'><a href='#'>" . $detalle['userName'] . "</a></h3>"
                    . "<div class='timeline-body'> " . $detalle['comment'] . " </div> "
                    . "</div>"
                    . "</li>";

            }
        } else {
            return array(
                'response' => false

            );
        }

        return array(
            'response' => true,
            'list_comment' => $list_comment
        );
    }
    public function copyTask($options)
    {
        $data = array();
        //$dataPartenTask = array();
        //$resultPartenTask = "";
        foreach ($options['options'] as $row) {
            $data[$row['name']] = $row['value'];
        }

        $entity = new TaskRepository();
        $entity->setOptions($data);

        $new_padre = $entity->SaveCopyTask($entity->getOptions());

        if ($new_padre) {
            //insertar las subtareas
            $result_child = $entity->saveChildTask($new_padre, $data['task_id']);


            $this->flashmessenger->addMessage(array('success' => 'Excelente!! La tarea se registro exitosamente.'));

        } else {
            $this->flashmessenger->addMessage(array('danger' => 'Opss. Algo salio mal al intetar registrar la tarea.'));
            return array(
                'response' => false,
                'message' => $this->flashmessenger->getMessageString()
            );
        }

        return array(
            'response' => true,
            'message' => $this->flashmessenger->getMessageString(),
        );
    }
    public function getNotification()
    {
        $notificationList = new TaskRepository();
        $result = $notificationList->getNotification();
        //echo "<pre>";var_dump($result);echo "</pre>";exit;
        if (isset($result)) {
            $list_notification = "<a href='#' class='dropdown-toggle' data-toggle='dropdown'>"
                . "<i class='fa fa-bell-o'></i>"
                . "<span class='label label-success'>" . count($result) . "</span>"
                . "</a>"
                . "<ul class='dropdown-menu'>"
                . "<li class='header'>Usted tiene " . count($result) . " notificaciones</li>";
            foreach ($result as $detalle) {

                $list_notification .= "<li>"
                    . "<ul class='menu'>"
                    . "<li>"
                    . "<a href='#'>"
                    . "<i class='fa fa-circle text-aqua'></i> " . $detalle['task_name'] . " - completada "
                    . "</a>"
                    . "</li>"
                    . "</ul>"
                    . "</li>";
            }
            $list_notification .= "<li class='footer'><a href='#' onclick='readNotification()'>Marcar como leidas</a></li>";
        } else {
            $list_notification = "<a href='#' class='dropdown-toggle' data-toggle='dropdown'>"
                . "<i class='fa fa-bell-o'></i>"
                . "<span class='label label-success'>0</span>"
                . "</a>";                     

        }

        return array(
            'response' => true,
            'list_notification' => $list_notification
        );
    }
    public function readNotification(){
        $TaskRepo = new TaskRepository();
    

        $result = $TaskRepo->readNotification();
        //$data['action'] = 'edit';    
        // echo "<pre>";var_dump($result);echo "</pre>";exit;

        if ($result) {
            $this->flashmessenger->addMessage(array('success' => 'Excelente!! Notificaciones marcadas como leidas.'));
        } else {
            $this->flashmessenger->addMessage(array('danger' => 'Opss. Algo salio mal al intetar .'));
        }

        return array(
            'response' => true,
            'message' => $this->flashmessenger->getMessageString(),
        );

    }

    public function getTaskByResponsable($options = null){
        $data = array();
       // echo "<pre>";var_dump($options['options']);echo "</pre>";exit;
        if($options['options']){
            foreach ($options['options'] as $row) {
                $data[$row['name']] = $row['value'];
            }
        }
       
        //echo "<pre>";var_dump($data);echo "</pre>";exit;
        $login = new Login();
        $TaskRepo = new TaskRepository();
        $responsable = $login->getId();
    
        $result = $TaskRepo->getTaskByResponsable($responsable,$data);

        $count = 0;
        $in = "in";
        $box_type = "";
        $button = "";
        $button_parent = "";
        $listTask = "";
         //echo "<pre>";var_dump($result);echo "</pre>";exit;
        if (isset($result)) {
            foreach ($result as $detalle) {

                switch ($detalle['status']) {
                    case '1':
                        $box_type = "class='label label-primary'"; // Nueva
                        break;
                    case '2':
                        $box_type = "class='label label-info'"; // En proceso
                        break;
                    case '3':
                        $box_type = "class='label label-warning'"; // En riesgo
                        break;
                    case '4':
                        $box_type = "class='label label-danger'"; // Retresado
                        break;
                    case '5':
                        $box_type = "class='label label-success'"; // Terminado
                        break;
                }
                
                if ($count > 0) {
                    $in = "";
                }             
               

                $listTask .= "<tr>"
                    
                    . "<td class='text-center'>" .$detalle['project_name']. " </td> "
                    . "<td class='text-center'>" . $detalle['task_name'] . " </td> "
                    . "<td class='text-center'>" . $detalle['format_due_date'] . " </td> "
                    
                    . "<td class='text-center'><span style='color:{$detalle['color_category']}'>" . $detalle['category_name'] . "</span></td> "

                    . "<td class='text-center'>" . $detalle['customer_task'] . "</td> "
                    

                    . "<td class='text-center'><span style='color:{$detalle['color']}'>" . $detalle['prioritie_name'] . "</span></td> "

                    . "<td class='text-center'><span style='color:{$detalle['colorNameUser']}'>" . $detalle['initialName'] . "</span></td> "

                    . "<td class='text-center'><span {$box_type}>" . $detalle['status_name'] . "</span></td> "
                    . "<td class='text-center' style='white-space: nowrap'> "                    
                    . " <a id='{$detalle['task_id']}' onclick='getTaskById({$detalle['task_id']})' href='Project.php?action=edit&id=".$detalle['project_id']."&task=".$detalle['task_id']."' class='btn btn-info btn-sm'><i class='fa fa-eye'></i></a> "

                    . "</td> "
                    . "</tr> ";
                $count++;
            }
             //echo "<pre>";var_dump($listTask);echo "</pre>";exit;
            return array(
                'response' => true,
                'accordionProject' => $listTask
            );
        }else{
            return array(
                'response' => true,
                'accordionProject' => ""
            );
        }

    }
}