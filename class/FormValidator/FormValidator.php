<?php
/**
 * Description of PFAAjax
 *
 * @author carlos
 */
class FormValidator {
    
    private $flashmessenger = null;
    
    public function __construct() {
        $this->flashmessenger = new FlashMessenger();
    }
    
    public function _getTranslation($text){
        return $this->flashmessenger->_getTranslation($text);
    }
    
    public function Validate($validator, $element) {
        return $this->$validator($element);
    }
    
    public function telefono($element){
        $campo = $element['name'];
        $valor = $element['value'];
        
        if(!is_numeric($valor)){
            $this->flashmessenger->addMessage(array('danger'=>"El {$campo} ingresado no es valido."));
            return null;
        }
        
        return true;        
    }
    
    public function email($element){
        $email = $element['value'];

        $result = (false !== filter_var($email, FILTER_VALIDATE_EMAIL));

        if($result){
          $domain = explode('@', $email);
          $result = checkdnsrr($domain[1], 'MX');
        }
        
        if(!$result){
            $this->flashmessenger->addMessage(array('danger'=>'El correo electonico ingresado no es valido.'));
            return null;
        }
        
        return true;
    }
    
    public function curp($element){
        $val = $element['value'];
        $campo = $element['label']; 
        
        $curp = new CURP();
        
        if($curp->validar_curp($val)){
            return true;
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'El campo <b>'.$campo.'</b> no es valido.'));
            return null; 
        }
    }
    
    public function rfc($element){
        $val = $element['value'];
        $campo = $element['label']; 
        $val = trim(strtoupper($val));
        
        $rfc = new RFC();
        
        if($rfc->validar_rfc($val)){
            return true;
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'Oops !! el campo <b>'.$campo.'</b> no es valido.'));
            return null; 
        }
    }
    
    public function codigoPostal($element){
        $val = $element['value'];
        $campo = $element['label'];
        
        if(strlen($val) != '5'){
            $this->flashmessenger->addMessage(array('danger'=>'Oops !! el campo <b>'.$campo.'</b> debe estar formado por 5 caracteres numericos.'));
            return null; 
        }
        
        $entity = new EntityRepository();        
        if(!$entity->esValidoCodigoPostal($val)){
            $this->flashmessenger->addMessage(array('danger'=>'Oops !! el Codigo postal <b>'.$val.'</b> no es valido.'));
            return null; 
        }
        
        return true;
    }
    
    public function double($element){
        $val = $element['value'];
        $campo = $element['label'];
        
        $pattern = '/^-?[0-9]+([,\.][0-9]*)?$/';
        if((!is_bool($val) && (is_float($val))) || preg_match($pattern, trim($val))){
            return true;
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'Oops !! el campo <b>'.$campo.'</b> debe ser numerico (opcionalmente con decimales.)'));
            return null; 
        }
    }
    
    public function date($element){  
        $val = $element['value'];
        $campo = $element['label'];
        
        if($this->isValidDate($val, $format = 'mm/dd/yyyy')){
            return true;
        }else{
           $this->flashmessenger->addMessage(array(
               'danger'=>$this->_getTranslation('El campo').'<b> '.$this->_getTranslation($campo).' </b>'.$this->_getTranslation('debe tener el siguiente formato mm/dd/aaaa')));
            return null; 
        }
    }
    
    public function time($element){
        $val = $element['value'];
        $campo = $element['label'];        
        
        $pattern = '/(2[0-3]|[01][0-9]):([0-5][0-9])/';
        if(preg_match($pattern, trim($val))){
            return true;
        }else{
           $this->flashmessenger->addMessage(array(
               'danger'=>$this->_getTranslation('El campo').'<b> '.$this->_getTranslation($campo).' </b>'.$this->_getTranslation('debe tener el siguiente formato hh/mm/ss')));
            return null; 
        }
    }
    
    public function datetime($element){        
        $data = explode(' ', $element['value']);
        $rs = $this->date(array(
            'value'=>$data[0],
            'label'=>$element['label']
        ));
        
        if($rs){
            $time = explode(":",$data[1]);
            $hour = $time[0];
            $min = $time[1];
            $sec = "00";
            if(isset($time[2])){$sec = $time[2];}   
            
            $rs = $this->time(array(
                    'value'=>$hour.":".$min.":".$sec,
                    'label'=>$element['label']
                ));
            if($rs){
                return true;
            }
        }
        
        return $rs;
    }
    
    public function isValidDate($value, $format = 'dd/mm/yyyy'){
        if(strlen($value) >= 6 && strlen($format) == 10){
            // find separator. Remove all other characters from $format
            $separator_only = str_replace(array('m','d','y'),'', $format);
            $separator = $separator_only[0]; // separator is first character
            $separatorOrigen = $separator;
           
            if($separator && strlen($separator_only) == 2){ 
                // make regex 
                $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
                $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
                $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
                $regexp = str_replace($separator, "\\" . $separator, $regexp);
              
                if($regexp != $value && preg_match('/'.$regexp.'\z/', $value)){
                    // check date
                    
                    $formatArray = explode($separator, $format);
                    foreach($formatArray as $key => $rango){
                        switch(substr($rango, 0,1)){
                            case 'd':
                                    $array['d'] = $key;
                                break;
                            case 'm':
                                    $array['m'] = $key;
                                break;
                            case 'y':
                                    $array['y'] = $key;
                                break;
                        }
                    }
                    
                    $arr=explode($separator,$value);
                    $month = $arr[$array['m']];
                    $day = $arr[$array['d']];
                    $year = $arr[$array['y']];
                    
                    if(@checkdate($month, $day, $year))
                        return $value;
                }
            }
        }
        return false;
    } 
}