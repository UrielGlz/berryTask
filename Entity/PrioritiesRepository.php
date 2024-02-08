<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

class PrioritiesRepository extends EntityRepository {
    private $table = 'priorities';

    private $options = array(

        'name'=>null,

        'color'=>null,

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

    public function save(array $data, $table = null) {

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

    public function getListSelectPriorities($idsPriorities = null){

        if($idsPriorities !== null){ $idsPriorities = " AND find_in_set(id,'$idsPriorities')";}
        
        $query = "SELECT * FROM $this->table WHERE 1 = 1 $idsPriorities ORDER BY name ASC";

        $result = $this->query($query);
        

        if($result->num_rows > 0){

            $array = array();

            while($row = $result->fetch_object()){

                $array[$row->id] = $row->name; 

            }

            return $array;

        }

        return null;

    }

    public function getListPriorities(){

        $query = "SELECT * FROM $this->table ORDER BY name ASC";

        $result = $this->query($query);
    
        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }        

        return null;

    }

}