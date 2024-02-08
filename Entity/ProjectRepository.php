<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

class ProjectRepository extends EntityRepository
{
    private $table = 'projects';

    private $options = array(

        'name' => null,

        'description' => null,

        'members' => null,

        'date_start' => null,

        'date_end' => null,

        'status' => null,

        'customer_id' => null,

        'customer_name' => null,

        'uuid' => null,

    );

    public function setOptions($data)
    {

        foreach ($this->options as $option => $value) {

            if (isset($data[$option])) {

                $this->options[$option] = $data[$option];

            }

        }

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

    public function getOptions()
    {


        return $this->options;

    }

    public function save(array $data, $table = null)
    {
        $tools = new Tools();

        //var_dump($data['members']);exit;
        //Get el customer name where el customer_id en caso de ser seleccionado

       
        if ($data['customer_id']) {
            $customer = new CustomerRepository();

            $result = $customer->getById($data['customer_id']);
            $data['customer_name'] = $result['name'];

        }
        $data['date_start'] = $tools->setFormatDateToDB($data['date_start']);

        $data['date_end'] = $tools->setFormatDateToDB($data['date_end']);

        $data['members'] = implode(',', $data['members']);

        $data['uuid'] = implode($this->getUUID()[0]) ;
        $data['status'] = '1' ;
        
        return parent::save($data, $this->table);

    }

    public function delete($id, $table = null)
    {

        return parent::delete($id, $this->table);

    }
    public function UsersStringToArray($users){

        $arrayUser = array();

        $users = explode(',', $users);

        foreach($users as $key => $user){

            $arrayUser[$user] = $user;

        }

        return $arrayUser;

    }

    public function update($id, $data, $table = null)
    {
        //echo "<pre>";var_dump($data['members']);echo "</pre>";exit;
        $tools = new Tools();

        $data['date_start'] = $tools->setFormatDateToDB($data['date_start']);

        $data['date_end'] = $tools->setFormatDateToDB($data['date_end']);

        $data['members'] = implode(',', $data['members']);    
        
        if ($data['customer_id']) {
            $customer = new CustomerRepository();

            $result = $customer->getById($data['customer_id']);
            $data['customer_name'] = $result['name'];

        }

    


        return parent::update($id, $data, $this->table);

    }

    public function getById($id, $table = null, $selectAux = null)
    {

        return parent::getById($id, $this->table, $selectAux);

    }

    public function _getBId($id){
       
      
        $query = "SELECT *, IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 5 AND t_.project_id = p.id) /  (select COUNT(IFNULL(_t.id,0))  from tasks _t WHERE _t.project_id = p.id )  * 100 ,0) as progreso, " 
        ."IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where  t_.project_id = p.id),0) as total_tareas, "
        ."IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 5 AND t_.project_id = p.id),0) as completas, "
        ."IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 2 AND t_.project_id = p.id),0) as proceso, "
        ."IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 3 AND t_.project_id = p.id),0) as riesgo, "
        ."IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 4 AND t_.project_id = p.id),0) as retrazo, "
        ."IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 1 AND t_.project_id = p.id),0) as nueva "
        ."FROM $this->table p  WHERE id = $id ";
        
        $result = $this->query($query);

        if ($result->num_rows>0) {

            $set = $this->resultToArray($result);

            return $set[0];

        }

        return null;

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

    public function getListProject($id = null)
    {
        if ($id) {
            $filterUser = "WHERE members like '%$id%' ";
        }
        $query = "SELECT * FROM $this->table $filterUser ORDER BY name ASC";

        $result = $this->query($query);

        // if ($result->num_rows > 0) {

        //     return $this->resultToArray($result);

        // }

        if ($result) {

            $array = array();

            while ($row = $result->fetch_assoc()) {

                $array[$row['id']] = $row['name'];

            }

            return $array;

        }

        return null;

    }

    //UG Obtener todos los proyectos donde esta asignado el empleado que hace login
    public function GetProjectsByUser($user,$rol){
        $filterUser = "";
        if ($rol !== "1") {
            $filterUser = "WHERE members like '%$user%' ";
        }else{
            $filterUser = " ";
        }
      
        $query = "SELECT *,fxGetUsersName(p.members), date_format(date_start,'%M %d, %Y')as format_date_start,  IFNULL((select COUNT(IFNULL(t_.id,0)) from tasks t_ where t_.status = 5 AND t_.project_id = p.id) /  (select COUNT(IFNULL(_t.id,0))  from tasks _t WHERE _t.project_id = p.id )  * 100 ,0) as progreso ,fxGetStatusName(p.status,'Project')as status_name FROM $this->table p    $filterUser";
        //var_dump($query);exit;
        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;

    }

    public function GetUsersGroup($users){
      
        $query = "SELECT  GROUP_CONCAT( CONCAT( name) SEPARATOR ', ') as names FROM users where  id in ($users)";
        //var_dump($query);exit;
        $result = $this->query($query);

        if ($result->num_rows > 0) {

            return $this->resultToArray($result);

        }

        return null;

    }
    public function getListStatus()
    {

        $query = "SELECT * FROM status_code WHERE operation = 'Project'";

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
}