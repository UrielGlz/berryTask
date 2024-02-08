<?php
/**
 * Description of Ajax
 *
 * @author carlos
 */
class Ajax extends EntityRepository {
    
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
    
    public function setColegioActualNombre($options){
        $colegioRepo = new ColegioRepository();
        $dataColegio = $colegioRepo->getById($options['colegio_id']);
        
        $login = new Login();
        $login->setColegioActivo($dataColegio['id']);
        $login->setColegioActivoNombre($dataColegio['nombre']);
        
        return array('response'=>true);
    }
    
    public function confirmAction($options){
        $query = "SELECT * FROM users WHERE master_key = '{$options['masterKey']}' LIMIT 1";
        $result = $this->query($query);
        
        $data = null;
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            $data = $result[0];            
        }
        
        if($data){
            $operacion = $options['operation'];
            $permisos = array(
                'edit_salesRecord'=>array('1','2','6'), #admin, supervisor
                'special_decorated'=>array('1','2','6'), #admin, supervisor
                'edit_special_order'=>array('1','2','6'),
                'delete_special_order_detail'=>array('1','2','6'),
                'delete_special_order'=>array('1','2','6'),
                'editarReceivingStoreRequest'=>array('1','2','6'), #admin, supervisor
            );
            
            $permiso = $permisos[$operacion];
            if(in_array($data['role'], $permiso)){
                return array(
                    'response'=>true
                );
            }else{
                $this->flashmessenger->addMessage(array('danger'=>'No tienes provilegios para confirmar esta operacion.'));
                return array(
                    'response'=>false,
                    'msg'=>$this->flashmessenger->getMessageString());
            } 
            
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'Clave incorrecta.'));
            return array(
                    'response'=>false,
                    'msg'=>$this->flashmessenger->getMessageString());
        }         
    }
    
    public function setIdioma($options){
        $translator = new Translator();
        $translator->setLenguage($options['idioma']);
        
        $login = new Login();
        $entityRepo = new EntityRepository();
        $entityRepo->update($login->getId(), array('idioma'=>$options['idioma']), 'users');
        
        return array('response'=>true);
    }
    
    public function approveWorkedHours($options){
        if($options['value'] == "true"){$approved = 'Si';}else{$approved = 'No';} /*TEngo que validad asi como esta, con comillas*/

        $query = "UPDATE timeclock SET approved  = '{$approved}' WHERE id = '{$options['id']}'";
        $this->query($query);
        
        return array(
            'response'=>true
        );        
    }
    
     public function deleteFile(array $file){
        $fileDelete = $file['fileDelete'];
        if (unlink(ROOT.$fileDelete)) {
            $response = true;
            $this->flashmessenger->addMessage(array('success'=>'Documento eliminado correctamente.'));
        }
        else{
            $response = null;
            $this->flashmessenger->addMessage(array('danger'=>'Eror al tratat de eliminar documento.'));
        }

        return array(
            'response' => true,
            'msj'=>$this->flashmessenger->getMessageString(),
        );
    }
}