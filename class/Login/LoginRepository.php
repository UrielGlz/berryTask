<?php
/**
 * Description of LoginRepository
 *
 * @author carlos
 */
class LoginRepository extends DataBase {
    public function isLoginCorrect($user,$password){
        $select = "SELECT * FROM users WHERE user = '$user' AND password = MD5('$password') LIMIT 1";
        $result = $this->getInstance()->execute($select);
        
        if($result->num_rows > 0){
           $result = $result->fetch_assoc();
           
           if($result['status']=='2'){
               $flashmessenger = new FlashMessenger();
               $flashmessenger->addMessage(array('danger'=>'Usuario inactivo para ingreso a sistema.'));
               return null;
           }           
           
           return $result;
        }else{
            $flashmessenger = new FlashMessenger();
            $flashmessenger->addMessage(array('danger'=>'Usuario o contraseña incorrecta.'));
            return null;
        }
    }
}
