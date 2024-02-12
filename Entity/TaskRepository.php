<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

class TaskRepository extends EntityRepository
{
    private $table = 'tasks';

    private $options = array(

        'uuid' => null,

        'parent_task_id' => null,

        'project_id' => null,

        'task_name' => null,

        'description' => null,

        'due_date' => null,

        'responsable' => null,

        'prioritie_id' => null,

        'category_id' => null,

        'customer_id' => null,

        'date_start' => null,

        'date_end' => null,

        'due_time' => null,

        'status' => null,

        'date' => null,

        'attachments' => null,

        'task_id' => null,

        'read' => null

    );

    public function setOptions($data)
    {

        foreach ($this->options as $option => $value) {

            if (isset($data[$option])) {

                $this->options[$option] = $data[$option];

            }

        }

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


        $tools = new Tools();

        $data['due_date'] = $tools->setFormatDateToDB($data['due_date']);
        $data['due_time'] = $tools->setFormatTimeToDB($data['due_time']);

        $data['uuid'] = implode($this->getUUID()[0]);
        $data['status'] = '1';

        
        if ($data['parent_task_id'] == "") {
            unset($data['parent_task_id']);
        }
        //var_dump($data);exit;
        unset($data['attachments']);

        unset($data["date"]);

        unset($data["task_id"]);

        $rs = parent::save($data, $this->table);

        $idFile = $this->getInsertId();

        if ($rs) {
            return $idFile;
        }
        return null;

        //$rs = parent::save($data, $this->table);

    }

    public function delete($id, $table = null)
    {

        return parent::delete($id, $this->table);

    }
    public function getFormatDate()
    {

        $date = substr($this->getDate(), 0, 10);

        $date = strftime('%m/%d/%Y', strtotime($date));

        return $date;

    }

    public function getDate()
    {

        return date('d-m-y h:i:s');

    }
    public function update($id, $data, $table = null)
    {
        $tools = new Tools();
        if (isset($data['due_date'])) {

            $data['due_date'] = $tools->setFormatDateToDB($data['due_date']);

        }
        if (isset($data['status'])) {

            $data['date_start'] = date('m/d/y h:i:s');

        }
        if (isset($data['date_start'])) {

            $data['date_start'] = $tools->setFormatDateTimeToDB($data['date_start']);

        }

        if (isset($data['due_time'])) {

            $data['due_time'] = $tools->setFormatTimeToDB($data['due_time']);

        }
        $data['read'] = '1'; //Task pendiente de leer notificacion

        //Proceso de insert files and upload file FTP
        if (isset($data['attachments'])) {
            $fileRepository = new FileRepository();

            $dataFile = array();

            $dataFile['attachments'] = $data['attachments'];
            $dataFile['task_id'] = $id;



            $resultUpload = $fileRepository->save($dataFile);
            //$attachments = $data['attachments'];

        }
        //echo "<pre>";var_dump($data);echo "</pre>";exit;


        unset($data["date"]);
        unset($data['attachments']);
        unset($data["task_id"]);

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

    public function getTaskByProject($project_id, $idUser, $rol, $parent_task_id)
    {


        $filterUser = "";
        $filterTask = "";

        if ($rol !== "1") {
            $filterUser = " AND find_in_set(responsable,'$idUser')";
        }
        if ($parent_task_id !== "") {
            $filterTask = " AND find_in_set(parent_task_id,'$parent_task_id')";

        } else {
            $filterTask = " AND parent_task_id = 0";

        }

        //responsable like '%$idUser%'

        $query = "SELECT *,t.id as task_id,  date_format(due_date,'%M %d, %Y')as format_due_date, fxGetUserName(responsable) as userName,fxGetInitialName(responsable) as initialName,fxGetColorNameUser(responsable) as colorNameUser,fxGetStatusName(status,'Project')as status_name, CURDATE() as actualDate "
            . ",p.name prioritie_name,p.color, "
            . "(select count(t_.id) from tasks t_ where t_.parent_task_id = t.id) as parent, "
            . "	IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 5 AND (t_.id = t.id OR t_.parent_task_id = t.id)) /  ((1 + (select COUNT(IFNULL(_t.id,0))  from tasks _t WHERE  _t.parent_task_id = t.id )) )  * 100 ,0) as progreso_task "
            . ",ct.name as category_name,ct.color as color_category , fxGetCustomerName(t.customer_id) as customer_name "
            . " FROM $this->table t LEFT JOIN priorities p on t.prioritie_id = p.id LEFT JOIN category_task ct on t.category_id = ct.id where project_id = '$project_id' $filterTask  $filterUser order by due_date asc";

          //var_dump($query);exit;
        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        } else { //UG no encuentra tareas hijas asi que va por el total de tareas por proyecto
            $query = "SELECT *,t.id as task_id,  date_format(due_date,'%M %d, %Y')as format_due_date, fxGetUserName(responsable) as userName,fxGetInitialName(responsable) as initialName,fxGetColorNameUser(responsable) as colorNameUser,fxGetStatusName(status,'Project')as status_name, CURDATE() as actualDate "
                . ",p.name prioritie_name,p.color, "
                . "(select count(t_.id) from tasks t_ where t_.parent_task_id = t.id) as parent, "
                . "	IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 5 AND (t_.id = t.id OR t_.parent_task_id = t.id)) /  ((1 + (select COUNT(IFNULL(_t.id,0))  from tasks _t WHERE  _t.parent_task_id = t.id )) )  * 100 ,0) as progreso_task "
                ." ,ct.name as category_name,ct.color as color_category,  fxGetCustomerName(t.customer_id) as customer_name "
                . " FROM $this->table t LEFT JOIN priorities p on t.prioritie_id = p.id LEFT JOIN category_task ct on t.category_id = ct.id  where project_id = '$project_id' AND parent_task_id = 0  $filterUser order by due_date asc";

            //var_dump($query);exit;
            $result = $this->query($query);

            if ($result->num_rows > 0) {

                return $this->resultToArray($result);

            }
        }

        return null;

    }

    public function getTaskById($id)
    {
        //responsable like '%$idUser%'

        $query = " SELECT *,t.id as task_id,  date_format(due_date,'%M %d, %Y')as format_due_date, fxGetUserName(responsable) as userName, CURDATE() as actualDate "
            . " ,p.name prioritie_name,p.color, "
            . " fxGetStatusName(status,'Task')as status_name, "
            . " ct.name as category_name,ct.color as color_category, "
            . " (select count(t_.id) from tasks t_ where t_.parent_task_id = t.id) as parent, "
            . " (SELECT  IF(parent_task_id = '0', 'NA', task_name) FROM tasks padre WHERE id = '$id') as last_task, "
            . "	IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 5 AND (t_.id = t.id OR t_.parent_task_id = t.id)) /  ((1 + (select COUNT(IFNULL(_t.id,0))  from tasks _t WHERE  _t.parent_task_id = t.id )) )  * 100 ,0) as progreso_task "
            . " FROM $this->table t LEFT JOIN priorities p on t.prioritie_id = p.id LEFT JOIN category_task ct on t.category_id = ct.id where t.id = '$id' order by due_date asc";

        //var_dump($query);exit;
        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;

    }

    public function SaveCopyTask($data)
    {

        $tools = new Tools();

        $data['due_date'] = $tools->setFormatDateToDB($data['due_date']);
        $data['due_time'] = $tools->setFormatTimeToDB($data['due_time']);

        $data['uuid'] = implode($this->getUUID()[0]);
        $data['status'] = '1';

        $data['task_name'] = $data['task_name'] . ' Copy';

        if ($data['parent_task_id'] == "") {
            unset($data['parent_task_id']);
        }
        //var_dump($data);exit;
        unset($data['attachments']);

        unset($data["date"]);

        unset($data["task_id"]);

        $rs = parent::save($data, $this->table);

        $id = $this->getInsertId();

        if ($rs) {
            return $id;
        }
        return null;

        //$rs = parent::save($data, $this->table);

    }
    public function saveChildTask($padre, $id_anterior)
    {

        $query = "INSERT INTO $this->table (uuid,parent_task_id,project_id,task_name,description,due_date, "
            . "responsable,category_id,prioritie_id,status,creado_por) "
            . "SELECT UUID(),$padre,project_id,task_name,description,due_date, "
            . "responsable,category_id,prioritie_id,status,creado_por FROM $this->table WHERE parent_task_id = $id_anterior ";


        $result = $this->query($query);

        if ($result) {
            return $result;
        }
        return null;
    }
    public function getNotification() //Get todas las tareas que son nuevas y necesitas mostrarse en las notificaciones
    {

        $query = "SELECT * FROM $this->table t WHERE t.read = '1'";
        //var_dump($query);
        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;

    }
    public function readNotification()
    {
        $query = "UPDATE $this->table t "
            . "SET t.read = NULL";

        $result = $this->query($query);
        if ($result) {
            return $result;
        }
        return null;
    }
    public function getTaskByResponsable($id,$data = null){
        $filter = "";
        foreach ($data as $key => $value) {
            if($value !== ''){
                if($key == 'status'){
                    $filter .= " AND t.$key = '$value'";
                } else if($key == 'customer_id'){
                    $filter .= " AND t.$key = '$value'";

                }
                else{
                    $filter .= " AND $key = '$value'";
                }
            }
        }
       
        $query = "SELECT t.*,pr.name project_name,t.id as task_id,  date_format(due_date,'%M %d, %Y')as format_due_date, fxGetUserName(responsable) as userName,fxGetInitialName(responsable) as initialName,fxGetColorNameUser(responsable) as colorNameUser,fxGetStatusName(t.status,'Project')as status_name, CURDATE() as actualDate "
        . ",p.name prioritie_name,p.color, "
        . "(select count(t_.id) from tasks t_ where t_.parent_task_id = t.id) as parent, "
        . "	IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 5 AND (t_.id = t.id OR t_.parent_task_id = t.id)) /  ((1 + (select COUNT(IFNULL(_t.id,0))  from tasks _t WHERE  _t.parent_task_id = t.id )) )  * 100 ,0) as progreso_task "
        . " ,ct.name as category_name,ct.color as color_category, fxGetCustomerName(t.customer_id) customer_task "
        ." FROM $this->table t INNER JOIN projects pr "
        . "on t.project_id = pr.id LEFT JOIN priorities p on t.prioritie_id = p.id "
        . " LEFT JOIN category_task ct on t.category_id = ct.id "
        . " WHERE t.responsable = '$id' $filter order by t.creado_fecha asc";
        //echo "<pre>";var_dump($query);echo "</pre>";exit;
        $result = $this->query($query);
         
        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;
    }

}