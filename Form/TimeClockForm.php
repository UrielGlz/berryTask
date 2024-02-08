<?php
/**
 * Description of NivelForm
 *
 * @author carlos
 */

class TimeClockForm extends Form {

    public function __construct() {
        $this->setActionForm('TimeClock.php');
        $this->setName('time_clock');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'date',
            //'label'=>'Fecha',
            //'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'id_user',
            'label'=>'Usuario',
            'multiOptions' => $this->getListUsuarios(),
            'required'=> true,
        ));       
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'check_in',
            'label'=>'Hora inicio',
            'required'=> true
        ));                
        $this->addElement(array(
            'type' => 'text',
            'name' => 'check_out',
            'label'=>'Hora fin',
            'required'=> false
        ));
        
        $this->addElement(array(
            'type' => 'button',
            'name' => 'send',
            'value'=> $this->_getTranslation('Terminar'),
            'optionals'=>array(
                'onclick'=>"submit()"),
            'class'=>'btn btn-primary m-t-1 m-b-1'
        ));
        
        $this->addElement(array(
            'type' => 'button',
            'name' => 'cancelar',
            'value'=> $this->_getTranslation('Cancelar'),
            'optionals'=>array(
                'onclick'=>"document.location = '".ROOT_HOST."/Controller/TimeClock.php'"),
            'class'=>'btn btn-danger m-t-1 m-b-1'
        ));
    }
    
    public function getListUsuarios(){
        $repository = new UserRepository();
        $list = $repository->getListSelectUsers();
         
         $array = array(''=>''); #Para poder aplicar "placeholder"  en select2 en vista
        foreach($list as $key => $value){
            $array[$key] = $value;
        }
        return $array;
    }
    
    public function populate($data){
        $tools = new Tools();
        if(isset($data['date']) && $tools->isValidaDateYYYMMDD($data['date'])){
            $data['date'] = $tools->setFormatDateToForm($data['date']);
        }
        
        if(isset($data['check_in']) && substr_count($data['check_in'], '-') > 0){
           $data['check_in'] = $tools->setFormatDateTimeToForm($data['check_in']);
        }
        if(isset($data['check_in_lunch']) && substr_count($data['check_in_lunch'], '-') > 0){
           $data['check_in_lunch'] = $tools->setFormatDateTimeToForm($data['check_in_lunch']);
        }
        if(isset($data['check_out_lunch']) && substr_count($data['check_out_lunch'], '-') > 0){
           $data['check_out_lunch'] = $tools->setFormatDateTimeToForm($data['check_out_lunch']);
        }
        if(isset($data['check_out']) && substr_count($data['check_out'], '-') > 0){
           $data['check_out'] = $tools->setFormatDateTimeToForm($data['check_out']);
        }
        parent::populate($data);
    }
}