<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class TimeClockAjax extends TimeClockRepository {
    
    public $flashmessenger = null;
    
    public function __construct() {
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
    
    public function getResponse($request, $options) {
        return $this->$request($options);
    }
    
    public function _getTranslation($text){
        $translator = new Translator();
        return $translator->_getTranslation($text);
    }
    
    public function getTranslation($options){
        $msj = $options['msj'];
        
        return array(
            'response'=>true,
            'translation'=>$this->_getTranslation($msj)
                );
    }
    
    public function setPunchTimeClock($options){
        $rs = parent::setPunchTimeClockByNIPUser($options);
        
        if($rs == null){
            return array(
                'response'=>true,
                'message'=>$this->flashmessenger->getMessageString()
            );
        }else{
            $listPunchstimeclock = '';
            if(count($rs['lastPunchTimeClock'])>0){
                foreach($rs['lastPunchTimeClock'] as $punch){
                    $listPunchstimeclock .= 
                            "<tr>"
                                . "<td class='text-center'>{$punch['check_in']}</td>"
                                . "<td class='text-center'>{$punch['check_out']}</td>"
                                . "<td class='text-center'>{$punch['total_work']}</td>"
                            ."</tr>";
                }
            }
            
            return array(
                'response'=>true,
                'punchstimeclock'=>$listPunchstimeclock,
                'employeName'=>$rs['userName'],
                'message'=>$this->flashmessenger->getMessageString()
            );
        }
    }
    
    public function getDataToEdit($options){
        $colegioRepo = new TimeClockRepository();
        $data = $colegioRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        $tools = new Tools();
        if(isset($data['check_in']) && substr_count($data['check_in'], '-') > 0){$data['check_in'] = $tools->setFormatDateTimeToForm($data['check_in']);}
        if(isset($data['check_out']) && substr_count($data['check_out'], '-') > 0){$data['check_out'] = $tools->setFormatDateTimeToForm($data['check_out']);}
        
        return array(
            'response'=>true,
            'timeClockData'=>$data
        );
    }
    
    public function deleteTimeClock($options){
        $colegioRepo = new TimeClockRepository();
        
        if($colegioRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'El registro se elimino satisfactoriamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
}