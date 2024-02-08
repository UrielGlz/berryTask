<?php

class StoreRepository extends EntityRepository {



    private $table = 'stores';

    private $flashmessenger = null;    

    private $options = array(

        'name'=>null,       

        'address'=>null,

        'city'=>null,

        'state'=>null,

        'country'=>'USA',

        'zipcode'=>null,

        'contact_name'=>null,

        'phone'=>null,

        'fax'=>null,

        'email'=>null,

        'webpage'=>null,

        'default_location'=>null,

        'status'=>null,

    );

    

    public function __construct() {

        if(!$this->flashmessenger instanceof FlashMessenger){

            $this->flashmessenger = new FlashMessenger();

        }

    }

    

    public function _getTranslation($text){

        $translator = new Translator();

        return $translator->_getTranslation($text);

    }

    

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

    

    public function getTable(){

        return $this->table;

    }

    

    public function getName(){

        return $this->options['name'];

    }

    

    public function getAddress(){

        return $this->options['address'];

    }

    

    public function getCity(){

        return $this->options['city'];

    }

    

    public function getState(){

        return $this->options['state'];

    }

    

    public function getZipCode(){

        return $this->options['zipcode'];

    }

    

    public function getPhone(){

        return $this->options['phone'];

    }



    public function save(array $data, $table = null) {       

        if(is_null($data['status'])){$data['status'] = '1';}      

        

        $this->startTransaction();

        $rs = parent::save($data, $this->table);

        

        if($rs){

            $store_id = $this->getInsertId();

            $repoLocation = new LocationRepository();

            $newLocation = array(

                'system_variable'=>1,

                'description'=>'Sin locacion',

                'store_id'=>$store_id,

                'status'=>1,            

            );



            if($repoLocation->save($newLocation)){

                $location_id = $this->getInsertId();

                $query = "UPDATE $this->table SET default_location = '$location_id' WHERE id = '$store_id'";

                if(parent::query($query)){

                    $this->commit();

                    return true;

                }

            }

        }       

        

        $this->rollback();

        return null;   

    }

    

    public function delete($id, $table = null) {

        return parent::delete($id, $this->table);

    }

    

    public function update($id, $data, $table = null) {           

        return parent::update($id, $data, $this->table);        

    }



    public function getById($id, $table = null,$selectAux = null) {

        $query = "SELECT *,fxGetStatusName(status,'Store')as status_name FROM $this->table WHERE id = '$id'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result)[0];

        }

        return null;

    }



    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {

        $query = "SELECT GROUP_CONCAT(id)stores FROM users WHERE find_in_set(store_id,'$id')";

        $result = $this->query($query);

        if($result->num_rows > 0){

            $result = $result->fetch_object();

            $query = "SELECT * FROM sales_record WHERE find_in_set(creado_por,'{$result->stores}')";

            $rs = $this->query($query);



            if($rs->num_rows > 0){

                return true;

            }

        }

        

        return null;

    }

    

    

    /*Siempre se deben enviar todas las stores, porque se usa para reporte de produccion de panaderia en ReportListRepository->getBakeryProduction */

    /*Si se modifica esta funcion validar que reporte seguira funcionando correctamente*/

    public function getListSelectStores($id = null){        

        if($id){$id = " AND id = '$id'";}

        

        $query = "SELECT id,name "              

                . "FROM $this->table "

                . "WHERE 1 = 1 $id "

                . "ORDER BY name ASC ";

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

    

     public function getListStores($options = null){      

        $login = new Login();        

        $store_id = null;

        

        if($login->getRole() != '1'){

            $store_id = " AND FIND_IN_SET(id,'{$login->getStoreId()}') ";

        }

        

        $query = "SELECT *,fxGetStatusName(status,'Store')as status_name FROM $this->table WHERE 1 = 1 $store_id ";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        

        return null;

    }

    

    public function getListStatus(){

        $query = "SELECT * FROM status_code WHERE operation = 'Store'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $array = array();

            foreach($result as $status){

                $array[$status['code']] = $status['description'];

            }

            return $array;

        }

        return null;

    }

    

     public function createFilterFecha($options,$campoFecha = null ){

        if(!isset($options['fechaInicio']) && !isset($options['fechaFin'])){return null;}        

        $fechaInicio = $options['fechaInicio'];

        $fechaFin = $options['fechaFin'];

        $fecha = null;

        $tools = new Tools();

        if($fechaInicio!=null){

            $fechaInicio = $tools->setFormatDateToDB($fechaInicio);

            if($fechaFin!=null){

                $fechaFin = $tools->setFormatDateToDB($fechaFin);

                $fecha .=" AND $campoFecha BETWEEN '{$fechaInicio}' AND '{$fechaFin}' ";

            }else{

                $fecha .=" AND $campoFecha BETWEEN '{$fechaInicio}' AND '{$fechaInicio}' ";

            }

        }elseif($fechaFin!=null){

            $fecha .=" AND $campoFecha BETWEEN '{$fechaFin}' AND '{$fechaFin}' ";

        }

        

        return $fecha;

    }

}