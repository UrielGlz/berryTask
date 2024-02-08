<?php

class SalesRecordRepository extends EntityRepository {



    private $table = 'sales_record';

    private $flashmessenger = null;    

    private $options = array(

        'store_id'=>null,

        'date'=>null,

        'initial_cash'=>null,       

        'final_cash'=>null,

        'debit_card'=>null,

        'credit_card'=>null,

        'check'=>null,

        'stamp'=>null,

        'withdrawal'=>null,

        'status'=>null,

        'comments'=>null

    );

    

    private $options_aux = array(

        'token_form'=>null,

        'statusName'=>null

    );

    

    /*Input double y que no son hide*/

    public $inputs_double = array(

        'initial_cash','debit_card','credit_card','check','stamp','withdrawal'

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

      

      foreach ($this->options_aux as $option => $value){

            if(isset($data[$option])){

              $this->options_aux[$option] = $data[$option];

            }

        }

        

    }

  

    public function getOptions(){

        return $this->options;

    }

    

    public function getTable(){

        return $this->table;

    }

    

    public function getTokenForm(){

        return $this->options_aux['token_form'];

    }



    public function save(array $data, $table = null) {        

        $tools = new Tools();

        if(is_null($data['status'])){$data['status'] = '1';}      

        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);

        

        $data = parent::_rawNumber($data, $this->inputs_double);

            

        $this->startTransaction();

        $rs = parent::save($data, $this->table);

        $idSaleRecord = $this->getInsertId();

        $this->setLastInsertId($idSaleRecord);

        

        if($rs){

            $storeRequestDetallesTemp = new SalesRecordExpesnsesDetailsTemp();       

            if($storeRequestDetallesTemp->saveDetalles($idSaleRecord,$this->getTokenForm())){ 

                    $this->commit();

                    $storeRequestDetallesTemp->truncate($this->getTokenForm());

                    return true;   

            }

        }   

        

        $this->rollback();

        return null;         

    }

    

    public function delete($id, $table = null) {

        $currentData = $this->getById($id);

        if($currentData['status'] === '2'){return true;}

        

        return parent::update($id, array('status'=>'2'), $this->table);

    }

    

    public function update($id, $data, $table = null) {            

        $tools = new Tools();

        $data['date'] = $tools->setFormatDateTimeToDB($data['date']);        

                

        unset($data['status']);

        $data = parent::_rawNumber($data, $this->inputs_double);

        

        $this->startTransaction();

        $result = parent::update($id, $data, $this->table);      

        

        if($result){

            $repository = new SalesRecordExpesnsesDetailsTemp();

            if($repository->updateDetalles($id,$this->getTokenForm())){      

                $this->commit();

                $repository->truncate($this->getTokenForm());                   

                return true;

            }

        }

        

        $this->rollback();

        return null;

    }



    public function getById($id, $table = null,$selectAux = null) {

        $query = "SELECT *,"

                . "DATE_FORMAT(date,'%m/%d/%Y %h:%i %p')as date,"

                . "fxGetStatusName(status,'SalesRecord')as status_name "

                . "FROM $this->table WHERE id = '$id'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result)[0];

        }

        return null;

    }



    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {        

        return null;

    }

    

    public function getListSalesRecords($options = null){          

        $store = null;

        $status = " AND status = '1' ";

        $limit = null;

        $date = $this->createFilterFecha($options,'DATE(`date`)');

        

        $login = new Login();

        if($login->getRole() != '1'){

            $store = " AND find_in_set(store_id,'{$login->getStoreId()}')";

        }       

        

        if($options){           

            if(isset($options['store_id'])){

                if(is_array($options['store_id']) && count($options['store_id']) > 0){

                    $storeIds = implode(',', $options['store_id']);

                    $store = " AND find_in_set(store_id,'{$storeIds}')";

                }else{

                    if(trim($options['store_id'])!= ''){$store = " AND find_in_set(store_id,'{$options['store_id']}')";}                     

                }           

            }       



            if(isset($options['status']) && is_array($options['status']) && count($options['status']) > 0){

                $idsStatus = implode(',', $options['status']);

                $status = " AND find_in_set(status,'$idsStatus')";

            }  

            

            if(is_null($store) 

                && is_null($status)){$limit = " LIMIT 500";}           

        }else{

            $limit = " LIMIT 500";

        }       

        

        $query = "SELECT *,"

                . "DATE_FORMAT(date,'%m/%d/%Y %h:%i %p')as formated_date,"   

                . "fxGetStoreName(store_id)as store_name,"

                . "SUM((final_cash) + debit_card + credit_card + `check` + stamp + withdrawal )as total_sales,"

                . "SUM((final_cash) + debit_card + credit_card + `check` + stamp )as total_close,"

                . "fxGetStatusName(status,'SalesRecord')as status_name "

                . "FROM $this->table "

                . "WHERE 1 =1 "

                . "$store "

                . "$status "

                . "$date "

                . "GROUP BY id ORDER BY id DESC $limit ";



        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        

        return null;

    }

    

    public function getListStatus(){

        $query = "SELECT * FROM status_code WHERE operation = 'SalesRecord'";

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

    

    public function crearTablaDetallesForUser(){

        $login = new Login();

        

        $query = "CREATE TABLE IF NOT EXISTS sales_record_expenses_details_".$login->getId()." 

                 (  `id` int(11) NOT NULL AUTO_INCREMENT,

                    `token_form` char(50) NOT NULL,

                    `id_detalle` int(11) NULL,

                    `id_sales_record` int(11) NULL,                    

                    `id_category_expense` int(11) NOT NULL,

                    `comments` varchar(255) NULL,

                    `amount` double NULL,

                    PRIMARY KEY (`id`)

                 )ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        

       $result = $this->query($query);

    }

    

    public function getSalesRecordExpenseDetalles($token_form){

        $login = new Login();

        $query = "SELECT s.*,

                CASE WHEN comments = '' AND amount = 0 THEN NULL ELSE amount END as amount,

                p.description as category_expense_name

                FROM sales_record_expenses_details_".$login->getId()." s, product_categories p 

                WHERE s.id_category_expense = p.id  

                AND token_form = '$token_form'";

        $result = $this->query($query);

        

        if($result){

            $result = $this->resultToArray($result);

            return $result;

        }

        

        return null;

    }

    

    public function getSalesRecordExpenseDetallesSaved($id){

        $query = "SELECT d.*,

                    fxGetCategoryDescription(id_category_expense)as category_expense_name

                    FROM sales_record_expenses_details d

                    WHERE id_sales_record = '$id' 

                    ORDER BY id";

        $result = $this->query($query);

        

        if($result){

            $result = $this->resultToArray($result);

            return $result;

        }

        

        return null;

    }

    

    public function setSalesRecordExpenseDetallesById($idStoreRequest,$tokenForm){

        $repo = new SalesRecordExpesnsesDetailsTemp();

        return $repo->setSalesRecordExpenseDetallesById($idStoreRequest, $tokenForm);

    }

    

    public function removeAllowEdit($idReceiving){

        parent::update($idReceiving, array('allow_edit'=>'0'), $this->table);

    }

    

    public function getSalesByDateRange($start,$end,$group = null,$orderBy = null){       

        if(is_null($group)){$group = " GROUP BY store_id";}else{$group = " GROUP BY $group";}

        if(is_null($orderBy)){$orderBy = " ORDER BY total_sales DESC";}else{$orderBy = " ORDER BY $orderBy";}

        

        $date = $this->createFilterFecha(array('startDate'=>$start,'endDate'=>$end),'DATE(date)');

        $query = "SELECT "

                . "DATE_FORMAT(date,'%m/%d/%Y %h:%i %p')as formated_date,"   

                . "fxGetStoreName(store_id)as store_name, "

                . "SUM((final_cash) + debit_card + credit_card + stamp + `check` + withdrawal)as total_sales "

                . "FROM $this->table "

                . "WHERE status = '1' $date $group $orderBy ";

        

        $result = $this->query($query);

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        return null;

    }
    //UG ADD Query Get Trafico
    public function getTraficoByStoreAndDateRange($start,$end,$group = null,$orderBy = null){       

        if(is_null($group)){$group = " GROUP BY idSucursal";}else{$group = " GROUP BY $group";}
       

       // if(is_null($orderBy)){$orderBy = " ORDER BY total_sales DESC";}else{$orderBy = " ORDER BY $orderBy";}        

        $date = $this->createFilterFecha(array('startDate'=>$start,'endDate'=>$end),'DATE(fecha)');

        $query = "SELECT "

                . "DATE_FORMAT(fecha,'%m/%d/%Y %h:%i %p')as formated_date,"   

                . "fxGetStoreName(idSucursal)as store_name, "

                // . "SUM((final_cash) + debit_card + credit_card + stamp + `check` + withdrawal)as total_sales "
                . "HOUR(_ventas.creado_fecha) _hora,COUNT(_ventas.id) _trafico "

                . "FROM ventas AS _ventas "

                . "JOIN stores as _store ON _ventas.idSucursal = _store.id "

                . "WHERE _ventas.status <> 1 $date $group ";        
        
        $result = $this->query($query);
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        return null;
    }   
    //Se obtienen las mermas por sucursal
    public function getMermasByStoreAndDateRange($start,$end,$group = null,$orderBy = null){
        $date = null;
        $store_id = null;
        $limit = null;  
        
        if(is_null($group)){$group = " GROUP BY idSucursal";}else{$group = " GROUP BY $group";}
        
        $date = $this->createFilterFecha(array('startDate'=>$start,'endDate'=>$end),'DATE(creado_fecha)');
         

        $query = "SELECT *,"
        . "date_format(creado_fecha,'%c/%d/%Y')as formated_date,"

        . "fxGetStoreName(idSucursal)as store_name,SUM(quantity) _mermas "
        
        . "FROM waste "

        . "WHERE 1 = 1 $date $group ";
   
       // var_dump($query);exit;
        $result = $this->query($query);
       
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        return null;
    } 
    public function getSalesByStoreIdByDateRange($start,$end,$group = null,$orderBy = null){       

        $login = new Login();

        $store_id = $login->getStoreId();

        if(is_null($group)){$group = " GROUP BY DATE(date)";}else{$group = " GROUP BY $group";}

        if(is_null($orderBy)){$orderBy = " ORDER BY DATE(date) DESC";}else{$orderBy = " ORDER BY $orderBy";}

        

        $date = $this->createFilterFecha(array('startDate'=>$start,'endDate'=>$end),'DATE(date)');

        $query = "SELECT "

                . "DATE_FORMAT(date,'%m/%d/%Y')as date," 

                . "DAYNAME(date)as dayname,"

                . "fxGetStoreName(store_id)as store_name, "

                . "SUM((final_cash) + debit_card + credit_card + stamp + `check` + withdrawal )as total_sales "

                . "FROM $this->table "

                . "WHERE 1 = 1 AND store_id = '$store_id' $date $group $orderBy ";

        

        $result = $this->query($query);

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        return null;

    }

    

    /*Se obtienen las venta desde tbl venta que se llena desde los POS*/

    public function getSalesByDateRangeFromSalesPOS($start,$end,$group = null,$orderBy = null){       

        if(is_null($group)){$group = " GROUP BY idSucursal";}else{$group = " GROUP BY $group";}

        if(is_null($orderBy)){$orderBy = " ORDER BY total_sales DESC";}else{$orderBy = " ORDER BY $orderBy";}

        

        $date = $this->createFilterFecha(array('startDate'=>$start,'endDate'=>$end),'fecha');

        $query = "SELECT "

                . "DATE_FORMAT(fecha,'%m/%d/%Y')as formated_date,"   

                . "fxGetStoreName(idSucursal)as store_name, "

                . "SUM(total_venta)as total_sales "

                . "FROM ventas "

                . "WHERE status = '2' $date $group $orderBy ";

        //echo $query;exit;

        $result = $this->query($query);

        if($result->num_rows > 0){

            return $this->resultToArray($result);

        }

        return null;

    }

    

    public function createFilterFecha($options,$campoFecha = null ){

        if(!isset($options['startDate']) && !isset($options['endDate'])){return null;}        

        $startDate = $options['startDate'];

        $endDate = $options['endDate'];

        $fecha = null;

        $tools = new Tools();

        if($startDate!=null){

            $startDate = $tools->setFormatDateToDB($startDate);

            if($endDate!=null){

                $endDate = $tools->setFormatDateToDB($endDate);

                $fecha .=" AND $campoFecha BETWEEN '{$startDate}' AND '{$endDate}' ";

            }else{

                $fecha .=" AND $campoFecha BETWEEN '{$startDate}' AND '{$startDate}' ";

            }

        }elseif($endDate!=null){

            $fecha .=" AND $campoFecha BETWEEN '{$endDate}' AND '{$endDate}' ";

        }

        

        return $fecha;

    }

    

}