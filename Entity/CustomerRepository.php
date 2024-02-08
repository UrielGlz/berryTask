<?php

class CustomerRepository extends EntityRepository {



    private $table = 'customers';

    private $flashmessenger = null;    

    private $options = array(

        'name'=>null,       

        'address'=>null,

        'phone'=>null,

        'email1'=>null,

        'email2'=>null,

        'email3'=>null,

        'email4'=>null,

        'contact'=>null,

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



    public function save(array $data, $table = null) {         

        if(!isset($data['status'])){$data['status'] = '1';}

        if(is_null($data['status'])){$data['status'] = '1';}      



        $this->startTransaction();

        $rs = parent::save($data, $this->table);

        $this->setLastInsertId($this->getInsertId());

        

        if(!$rs){

            $this->rollback();

            return null;

        }

        $this->commit();

        return true;        

    }

    

    public function delete($id, $table = null) {

        return parent::delete($id, $this->table);

    }

    

    public function update($id, $data, $table = null) {           

        return parent::update($id, $data, $this->table);        

    }



    public function getById($id, $table = null,$selectAux = null) {

        $query = "SELECT *,fxGetStatusName(status,'Customer')as status_name FROM $this->table WHERE id = '$id'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result)[0];

        }

        return null;

    }



    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {

        $query = "SELECT id FROM projects WHERE customer_id = '$id'";

        $result = $this->query($query);

        if($result->num_rows > 0){

            return true;

        }

        

        return null;

    }

    

    public function getListSelectCustomers(){        

        $query = "SELECT id,name,phone "              

                . "FROM $this->table "

                . "WHERE 1 = 1 "

                . "ORDER BY name ASC";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $array = array();

            while($row = $result->fetch_object()){

                $array[$row->id] = $row->phone.' - '.$row->name; 

            }            

            return $array;

        }

        

        return null;

    }

    

     public function getListSelectCustomersForBill(){        

        $query = "SELECT id,name,phone "              

                . "FROM $this->table "

                . "WHERE for_billing = 1 "

                . "ORDER BY name ASC";

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

    

     public function getListCustomers($options = null){          

        $query = "SELECT * "                

                //. ",fxGetStatusName(status,'Customer')as status "

                . "FROM $this->table ";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        

        return null;

    }

    

    public function getListStatus(){

        $query = "SELECT * FROM status_code WHERE operation = 'Customer'";

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

    

    public function getListMetodoPago(){

        $query = "SELECT id,description FROM payment_methods WHERE tipo = 'Metodo pago'";

        $result = $this->query($query);

        

        if($result){

            $array = array();

            while($row = $result->fetch_object()){

                $array[$row->id] = $row->description;

            }

            

            return $array;

        }

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