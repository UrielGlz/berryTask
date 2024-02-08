<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class TimeClockRepository extends EntityRepository {

    private $table = 'timeclock';
    public $flashmessenger = null;        
     private $options = array(
        'id_user'=>null,
        'date'=>null,
        'check_in'=>null,
        'check_out'=>null
    );
    
    public function __construct() {
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
    
    public function _getTranslation($text){
        return $this->flashmessenger->_getTranslation($text);
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
        $tools = new Tools();
        $userRepo = new UserRepository();
        $userData = $userRepo->getById($data['id_user']);
        $data['store_id'] = $userData['store_id'];
        
        if(trim($data['check_in']) == ''){unset($data['check_in']);
        }else{$data['check_in'] = $tools->setFormatDateTimeToDB($data['check_in']);}
        
       if(trim($data['check_out']) == ''){
            unset($data['check_out']);
            $data['next_operation'] = 'check_out';
        }else{
            $data['check_out'] = $tools->setFormatDateTimeToDB($data['check_out']);
            $data['next_operation'] = 'check_in';
        }       
        
        /*Obtenes fecha de check_in;*/
        $data['date'] = substr($data['check_in'], 0, 10);
        
        return parent::save($data, $this->table);
    }
    
    public function delete($id, $table = null) {        
        return parent::delete($id, $this->table);
    }

    public function update($id, $data, $table = null) {   
         $tools = new Tools();
        
        if(trim($data['check_in']) == ''){unset($data['check_in']);
        }else{$data['check_in'] = $tools->setFormatDateTimeToDB($data['check_in']);}
        
       if(trim($data['check_out']) == ''){
            $data['check_out'] = '_NULL';
            $data['next_operation'] = 'check_out';
        }else{
            $data['check_out'] = $tools->setFormatDateTimeToDB($data['check_out']);
            $data['next_operation'] = 'check_in';
        }       
        
        /*Obtenes fecha de check_in;*/
        $data['date'] = substr($data['check_in'], 0, 10);
        
        unset($data['id_user']);
        return parent::update($id, $data, $this->table);
    }

    public function getById($id, $table = null,$selectAux = null) {
        $selectAux = " date_format(check_in,'%m/%d/%Y %h:%i %p')as check_in,";
        $selectAux .= " date_format(check_out,'%m/%d/%Y %h:%i %p')as check_out ";
        return parent::getById($id, $this->table,$selectAux);
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        return parent::isUsedInRecord($id, array('productos' => 'tamano'));
    }

    public function getListaSelectTimeClock() {
        $select = "SELECT * FROM $this->table ";
        $result = $this->query($select);

        if ($result) {
            $array = array();
            while ($row = $result->fetch_assoc()) {
                $array[$row['id']] = $row['id_user'];
            }
            return $array;
        }
        return null;
    }
    
     public function getListaTimeClock($options = null) {        
        $user  = null;
        $date = null;
        $limit = null;        

        if($options != null){         
            $date = $this->createFilterFecha($options['start_date'], $options['end_date'], 'date');
            if (isset($options['user']) && $options['user'] != null) {
                $user = implode(',', $options['user']);                 
                $user = " AND find_in_set(id_user,'$user') "; 
            }
            
        }else{
          $limit = " LIMIT 500 ";          
        }        
        
        $store_id = null;        
        $login = new Login();
        if($login->getRole() != '1'){
            $store_id = "AND (";
            $storeIdArray = explode(',', $login->getStoreId());
            foreach($storeIdArray as $key => $storeId ){
                $store_id .= " find_in_set($storeId,store_id) OR ";
            }            
            
            $store_id = trim($store_id, " OR ");
            $store_id .= ") ";
        }        
 
        $select = "SELECT 
                    id,
                    date_format(date,'%c/%d/%Y')as formated_date,
                    fxGetStoreName(store_id)as sucursalName,
                    date_format(check_in,'%r')as check_in,
                    date_format(check_out,'%r')as check_out,
                    fxGetUserName(id_user)as userName,
                    timediff(check_out,check_in)as total_work,
                    ((time_to_sec(timediff(check_out,check_in)))/60)/60 as total
                    from $this->table "
                . "WHERE 1 = 1 $date "
                . "$user
                   $store_id
                   ORDER BY date DESC,check_in DESC $limit";
        
        $result = $this->query($select);

        if ($result) {
           return $this->resultToArray($result);
        }
        return null;
    }
    
    public function getLastPunchTimeClockByUser($idUser = null,$limit = 5) {
        $select = "SELECT 
                    id,
                    date_format(date,'%c/%d/%Y')as date,
                    date_format(check_in,'%c/%d/%Y %r')as check_in,
                    date_format(check_out,'%c/%d/%Y %r')as check_out,
                    fxGetUserName(id_user)as userName,
                    timediff(check_out,check_in)as total_work,
                    ((time_to_sec(timediff(check_out,check_in)))/60)/60 as total
                    from $this->table WHERE id_user = '$idUser' ORDER BY check_in DESC LIMIT $limit ";
        $result = $this->query($select);

        if ($result) {
           return $this->resultToArray($result);
        }
        return null;
    }
    
    public function setPunchTimeClockByNIPUser($options){
        $nipUser = $options['nip_user'];
        
        $settings = new SettingsRepository();
        $time_lapse_between_punchtime = $settings->_get('time_lapse_between_punchtime');
        
        $repoUser = new UserRepository();
        $dataUser = $repoUser->getUserByNIP($nipUser);
        $storeId = $dataUser['store_id'];
        
        if($dataUser == null){
            $this->flashmessenger->addMessage(array('danger'=>"NIP de empleado {$nipUser} no existe o esta Inactivo en el sistema."));
            return null;
        }
        
        $idUser = $dataUser['id'];
        
        $fecha = date('Y-m-d');
        $hora = date('G:i:s'); 
        
        $query = "SELECT * FROM $this->table where id_user = '$idUser' ORDER BY `check_in` DESC LIMIT 1";
        $result = $this->query($query);        
        
        $entityRepository = new EntityRepository();
        $nextOperation = null;
        if($result->num_rows > 0){
            $result = $result->fetch_object();
            $nextOperation = $result->next_operation;
            
            if($nextOperation === 'check_out' && $result->date === date('Y-m-d')){ //Se registra salida solo si es el mismo dia que su ultima checada
                /*VALIDAR DIFERENCIA DE TIEMPO ENTRE ULTIMA CHEECADA*/
                $query = "SELECT ((time_to_sec(timediff(NOW(),'{$result->check_in}')))/60) as minutes";
                $validate = $this->query($query);

                $validate = $validate->fetch_object();
                if($validate->minutes < $time_lapse_between_punchtime){
                    $this->flashmessenger->addMessage(array('danger'=>"Debes esperar almenos $time_lapse_between_punchtime minutos despues de tu ultima checada."));
                    return null;
                }
                /* END VALIDAR DIFERENCIA DE TIEMPO ENTRE ULTIMA CHEECADA*/
                
                $data = array(
                    'check_out'=>$fecha." ".$hora,
                    'next_operation'=>'check_in'
                );
                
                if($entityRepository->update($result->id, $data, $this->table)){
                    $this->flashmessenger->addMessage(array('success'=>"{$dataUser['name']} {$dataUser['last_name']} su <strong>Salida</strong> se registro con exito."));
                    return array(
                        'lastPunchTimeClock'=>$this->getLastPunchTimeClockByUser($idUser),
                        'userName'=>$dataUser['name']." ".$dataUser['last_name']
                    );
                }              
            }          
        }        
        
        /* Sino exite registro o nextOption = 'check_in', se crea un registro nuevo*/  
        
        /*VALIDAR DIFERENCIA DE TIEMPO ENTRE ULTIMA CHEECADA*/
        if($nextOperation === 'check_in'){
            $query = "SELECT ((time_to_sec(timediff(NOW(),'{$result->check_out}')))/60) as minutes";
            $result = $this->query($query);
            
            $result = $result->fetch_object();
            if($result->minutes < $time_lapse_between_punchtime){
                $this->flashmessenger->addMessage(array('danger'=>"Debes esperar almenos $time_lapse_between_punchtime minutos despues de tu ultima checada."));
                return null;
            }
        }
        /*END VALIDAR DIFERENCIA DE TIEMPO ENTRE ULTIMA CHEECADA*/
        
        /*AGREGAR NUEVO REGISTRO CON CHECK_IN*/
        $data = array(
            'id_user'=>$idUser,
            'date'=>$fecha,
            'check_in'=>$fecha." ".$hora,
            'store_id'=>$storeId,
            'next_operation'=>'check_out'
        );

        if($entityRepository->save($data,$this->table)){
            $this->flashmessenger->addMessage(array('success'=>"{$dataUser['name']} {$dataUser['last_name']} su <strong>Entrada</strong> se registro con exito."));
            return array(
                'lastPunchTimeClock'=>$this->getLastPunchTimeClockByUser($idUser),
                'userName'=>$dataUser['name']." ".$dataUser['last_name']
            );     
        }
        
        return null;
    }
    
    public function createFilterFecha($startDate =null,$endDate =null,$campoFecha = null ){
        if(is_null($startDate) && is_null($endDate)){return null;}        
        $fecha = null;
        $tools = new Tools();
        if($startDate!=null){
            $startDate = $tools->setFormatDateToDB($startDate);
            if($endDate!=null){
                $endDate = $tools->setFormatDateToDB($endDate);
                $fecha .=" AND DATE($campoFecha) BETWEEN '{$startDate}' AND '{$endDate}' ";
            }else{
                $fecha .=" AND DATE($campoFecha) BETWEEN '{$startDate}' AND '{$startDate}' ";
            }
        }elseif($endDate!=null){
            $fecha .=" AND DATE($campoFecha) BETWEEN '{$endDate}' AND '{$endDate}' ";
        }
        
        return $fecha;
    }
}
