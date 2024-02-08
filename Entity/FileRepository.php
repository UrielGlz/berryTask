<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

class FileRepository extends EntityRepository
{
    private $table = 'files';
    private $images = null;

    private $options = array(

        'uuid' => null,

        'task_id' => null,

        'name' => null,

        'type' => null,

        'expiration_date' => null,

        'id_category_file' => null,

        'attachments' => null,

    );

    public function setOptions($data)
    {

        foreach ($this->options as $option => $value) {

            if (isset($data[$option])) {

                $this->options[$option] = $data[$option];

            }
        }
    }


    public function setImage($images)
    {

        $this->images = $images;

    }

    public function getOptions()
    {

        return $this->options;

    }

    public function getUUID()
    {

        $query = "SELECT UUID() as uuid ";
        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }
        return null;
    }

    public function save(array $data, $table = null)
    {
        //var_dump($data);exit;
        $tools = new Tools();
        $attachments = $data['attachments'];
        unset($data['expiration_date']);
        // if (isset($data['expiration_date'])) {
        //     $data['expiration_date'] = $tools->setFormatDateToDB($data['expiration_date']);
        // }
        if(isset($attachments['attachement_file']['name'][0]) && $attachments['attachement_file']['name'][0] != ''){
            $data['type'] = pathinfo($attachments['attachement_file']['name'], PATHINFO_EXTENSION);
            $data['name'] = pathinfo($attachments['attachement_file']['name'], PATHINFO_FILENAME);

        }

        $data['uuid'] = implode($this->getUUID()[0]);


        


        unset($data['attachments']);
      // echo "<pre>";var_dump($attachments);echo "</pre>";exit;


        $rs = parent::save($data, $this->table);

        $idFile = $this->getInsertId();

        if ($rs) {
            $fileManagement = new FileManagement();

            $settings = new SettingsRepository();

            if (isset($attachments['attachement_file']['name'][0]) && $attachments['attachement_file']['name'][0] != '') {

                $ext = pathinfo($attachments['attachement_file']['name'], PATHINFO_EXTENSION);

                //$attachments['attachement_file']['name'] = $settings->_get('name_for_attachment_file') . '.' . $ext;

                $attachments['attachement_file']['name'] =  pathinfo($attachments['attachement_file']['name'], PATHINFO_FILENAME) . '.' . $ext;

                $fileManagement->saveFile($attachments['attachement_file'], $data['task_id'], 'task');

                //$fileManagement->saveFile($attachments['attachement_file'], $idFile, 'task');

            }

            // if (isset($attachments['attachments']['name'][0]) && $attachments['attachments']['name'][0] != '') {

            //     $fileManagement->saveFile($attachments['attachments'], $idFile, 'purchase');

            // }
            return true;
        }
        else{
            $this->flashmessenger->addMessage(array(

                'error'=>$this->_getTranslation('Error. Intenta nuevamente o contacta a tu proveedor de sistemas.')));
    
            return null;  
        }

    }

    public function getListFiles($id)
    {
       // var_dump($id);exit;
        $fileManagement = new FileManagement();

        return $fileManagement->getStringListFilesByOperationAndPrefix('task', $id);

    }
    public function delete($id, $table = null)
    {

        return parent::delete($id, $this->table);

    }

    public function update($id, $data, $table = null)
    {


        return parent::update($id, $data, $this->table);

    }

    public function getById($id, $table = null, $selectAux = null)
    {

        return parent::getById($id, $this->table, $selectAux);

    }

    public function isUsedInRecord($id, array $buscarEn = null, $andWhere = null)
    {

        return parent::isUsedInRecord($id, array('tasks' => 'prioritie_id'));

    }

    public function getListSelectPriorities($idsPriorities = null)
    {

        if ($idsPriorities !== null) {
            $idsPriorities = " AND find_in_set(id,'$idsPriorities')";
        }

        $query = "SELECT * FROM $this->table WHERE 1 = 1 $idsPriorities ORDER BY name ASC";

        $result = $this->query($query);


        if ($result->num_rows > 0) {

            $array = array();

            while ($row = $result->fetch_object()) {

                $array[$row->id] = $row->name;

            }

            return $array;

        }

        return null;

    }

    public function getListPriorities()
    {

        $query = "SELECT * FROM $this->table ORDER BY name ASC";

        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;

    }

    public function getListStatus()
    {

        $query = "SELECT * FROM status_code WHERE operation = 'Task'";

        $result = $this->query($query);



        if ($result->num_rows > 0) {

            $array = array();

            foreach ($result as $status) {

                $array[$status['code']] = $status['description'];

            }

            return $array;

        }

        return null;

    }


    public function getTaskByProject($project_id, $idUser, $rol)
    {

        $filterUser = "";

        if ($rol !== "1") {
            $filterUser = " AND find_in_set(responsable,'$idUser')";
        }

        //responsable like '%$idUser%'

        $query = "SELECT *,t.id as task_id,  date_format(due_date,'%M %d, %Y')as format_due_date, fxGetUserName(responsable) as userName,fxGetInitialName(responsable) as initialName,fxGetColorNameUser(responsable) as colorNameUser, CURDATE() as actualDate "
            . ",p.name prioritie_name,p.color "
            . "FROM $this->table t JOIN priorities p on t.prioritie_id = p.id where project_id = '$project_id' $filterUser order by due_date asc";


        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;

    }

    public function getTaskById($id)
    {



        //responsable like '%$idUser%'

        $query = "SELECT *,t.id as task_id,  date_format(due_date,'%M %d, %Y')as format_due_date, fxGetUserName(responsable) as userName, CURDATE() as actualDate "
            . ",p.name prioritie_name,p.color, "
            . "fxGetStatusName(status,'Task')as status_name "
            . "FROM $this->table t JOIN priorities p on t.prioritie_id = p.id where t.id = '$id' order by due_date asc";


        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;

    }

}