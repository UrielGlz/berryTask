<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class AjaxModalForms extends EntityRepository {
    
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
    
    public function getStringForm($options){
        $stringForm = "Error al tratar de obtener formulario.";
        switch ($options['name']){
            case 'customer':
                $form = new CustomerForm();
                $stringForm = $form->getModalFormString();
                break;
        }
        
        return array(
            'response'=>true,
            'stringForm'=>utf8_encode($stringForm)
        );
    }
    
    public function saveCustomer($options){
        $currentSelected = array();
        if(isset($options['customerSelected'])){
            if(!is_array($options['customerSelected'])){
                $currentSelected[] = $options['customerSelected'];
            }else{
                $currentSelected = $options['customerSelected'];
            }           
        }
        
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }

        $form = new CustomerForm();   
        $form->populate($data);
        if(!$form->isValid($data)){            
            return array(
                'response'=>false,
                'message'=>$this->flashmessenger->getMessageString(),
                'formString'=>$form->getModalFormString()
              );
        }
        
        $entity = new CustomerEntity();
        $entity->setOptions($data);        
        
        if($data['action']=='insert'){
            $result = $entity->save($entity->getOptions());
            $currentSelected[] = $entity->getInsertId();
            
        }elseif($data['action']=='edit'){
            $result = $entity->update($options['idCustomer'],$entity->getOptions());
        }        

        if($result){
            $this->flashmessenger->addMessage(array('success'=>'Excelente!! El cliente se registro exitosamente.'));
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'Opss. Algo salio mal al intetar registrar el cliente.'));
        }        
       
        return array(
            'response'=>true,
            'message'=>$this->flashmessenger->getMessageString(),
            'listCustomers'=>utf8_encode($this->getListClientes($currentSelected))
        );
    }  
    
    public function getListClientes($selectedCustomer = null){
        $entity = new CustomerRepository();
        $listClientes= $entity->getListSelectClientes();
        
        $list = "<option value=''>".$this->_getTranslation('Seleccionar una opcion...')."</option>";
        foreach($listClientes as $key => $value){
            $selected = "";
            //if($key == $selectedCustomer){$selected = "selected";}
            if(in_array($key, $selectedCustomer)){$selected = "selected";}
            $list .= "<option value='$key' $selected >$value</option>";
        }
        return $list;
    }
    
    public function getCustomerInfo($options){
        $customerId = $options['id'];
        
        $repo = new CustomerRepository();
        $data = $repo->getById($customerId);
   
        return array(
            'response'=>true,
            'customerInfo'=>$data
        );
    }
     
}