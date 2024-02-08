<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class PaymentTermsAjax extends PaymentTermsRepository {
    
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
    
    public function getDataToEdit($options){
        $qualityRepo = new PaymentTermsRepository();
        $data = $qualityRepo->getById($options['id']);
        $data['action'] = 'edit';
        
        return array(
            'response'=>true,
            'paymentTermsData'=>$data
        );
    }
    
    public function deletePaymentTerms($options){
        $qualityRepo = new PaymentTermsRepository();
        
        if($qualityRepo->delete($options['id'])){
            $this->flashmessenger->addMessage(array('success'=>'Termino de pago se elimino exitosamente.'));                
        }
        
        return array(
            'response'=>true
        );
    }
    
    public function setDueDate($options) {
       $date = $options['date'];
       $payment_terms_id = $options['payment_terms_id'];
       
       $tools = new Tools();
       $date = $tools->setFormatDateToDB($date);
       
       $paymentTermsData = $this->getById($payment_terms_id);
       
       $dueDate = $this->getDueDate($date, $paymentTermsData['days']);       
       $dueDate = $tools->setFormatDateToForm($dueDate);
       
       return array(
            'response'=>true,
            'due_date'=>$dueDate
        );
    }
}