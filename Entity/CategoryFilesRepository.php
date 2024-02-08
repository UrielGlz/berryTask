<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

class CategoryFilesRepository extends EntityRepository {
    private $table = 'category_files';

    private $options = array(

        // 'name'=>null,

        // 'type'=>null,

        // 'expiration_date'=>null,

        // 'id_category_file'=>null,

        // 'email'=>null,

        // 'contact'=>null,

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

        return parent::isUsedInRecord($id, array('files' => 'id_category_file'));

    }

    public function getListSelectCategory_task($idsCategoryFiles = null){

        if($idsCategoryFiles !== null){ $idsCategoryFiles = " AND find_in_set(id,'$idsCategoryFiles')";}
        
        $query = "SELECT * FROM $this->table WHERE 1 = 1 $idsCategoryFiles ORDER BY name ASC";

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
    //UG Modifique esta funcion para poder ser utilizada en el save de file, modal addfile - view projectDetails
    public function getListCategoryFiles(){

        $query = "SELECT * FROM $this->table ORDER BY name ASC";

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

}