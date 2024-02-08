<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

class CommentRepository extends EntityRepository {
    private $table = 'comments';

    private $options = array(

        'uuid'=>null,
      

        'comment'=>null,

        'task_id'=> null,        

    );

    public function setOptions($data){

      foreach ($this->options as $option => $value){

          if(isset($data[$option])){

            $this->options[$option] = $data[$option];

          }

      }

    }  

    public function getOptions(){

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

    public function save(array $data, $table = null) {
        
        $tools = new Tools();
     
        $data['uuid'] = implode($this->getUUID()[0]) ;      
       
        return parent::save($data, $this->table);

    }    

    public function delete($id, $table = null) {

        return parent::delete($id, $this->table);

    }

    public function update($id, $data, $table = null) {     
      
       
        return parent::update($id, $data, $this->table);

    }

    public function getById($id, $table = null,$selectAux = null) {

        return parent::getById($id, $this->table,$selectAux);

    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {

        return parent::isUsedInRecord($id, array('tasks' => 'prioritie_id'));

    }

    public function getCommentById($id ){                  

        //responsable like '%$idUser%'

        $query = "SELECT *,t.id as task_id,   date_format(c.creado_fecha,'%m/%d/%Y , %h:%i:%s %p') as creado_fecha, fxGetUserName(c.creado_por) as userName "
                //.",p.name prioritie_name,p.color, "
                //. "fxGetStatusName(status,'Task')as status_name "
                . "FROM $this->table c JOIN tasks t on c.task_id = t.id where t.id = '$id' order by c.creado_fecha asc";
       

        $result = $this->query($query);

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }       

        return null;

    }

}