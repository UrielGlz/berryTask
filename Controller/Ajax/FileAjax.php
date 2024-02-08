<?php

/**

 * Description of Ajax

 *

 * @author Uriel

 */

class FileAjax extends FileRepository
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


    
    //Actualmente no funciona
    public function saveFile($options)
    {
       // echo "<pre>";var_dump($options);echo "</pre>";exit;
      //  $file = $_FILES['image'];
        $data = array();
        foreach ($options['options'] as $row) {
            $data[$row['name']] = $row['value'];
        }

      

        $entity = new TaskRepository();
        $entity->setOptions($data);

        if (trim($data['task_name']) == '' || trim($data['description']) == '' || trim($data['due_date']) == '' || trim($data['responsable']) == '' || trim($data['category_id']) == '' || trim($data['prioritie_id']) == '') {
            $this->flashmessenger->addMessage(array('danger' => 'Lo sentimos. Todos los campos son requeridos.'));
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
        } else {
            $this->flashmessenger->addMessage(array('danger' => 'Opss. Algo salio mal al intetar registrar la tarea.'));
        }

        return array(
            'response' => true,
            'message' => $this->flashmessenger->getMessageString(),
        );
    }
   

    public function getTaskById($id)
    {

        $TaskRepo = new TaskRepository();

        $user = new UserRepository();
        $data = $TaskRepo->getTaskById($id['id_task']);
       
       

       
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

        $data['userName'] = $userName['name'];
        $data['initials'] = $userName['initials'];
        $data['userColor'] = $userName['color'];
        

        // echo "<pre>";var_dump($data);echo "</pre>";exit;

        return array(

            'response' => true,

            'taskDetail' => $data

        );

    }

}