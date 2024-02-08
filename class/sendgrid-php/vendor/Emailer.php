<?php
require 'autoload.php';
putenv("SENDGRID_API_KEY=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");

class Emailer extends \SendGrid\Mail\Mail{
    private $error = null;
    private $fromTitle = null;
    private $to = null;
    
    public function sendEmail($data) {       
       
 
        $this->to = $data['to'];
        $this->setAddAddress($data['to']);
        if(isset($data['cc']) && trim($data['cc'])!=''){$this->setAddCC($data['cc']);}  
        $this->_setSubject($data['subject']);       
        $this->setMsgHTML($data['message']);
        
        if(isset($data['attachment'])){
            if(is_array($data['attachment'])){
                foreach($data['attachment'] as $attachment){  
                    $path_parts = pathinfo($attachment);
                    $extension = $path_parts['extension'];
                    $filename =  $path_parts['filename'];
                    $this->AddAttachment(base64_encode(file_get_contents($attachment)),"application/$extension",$filename,"attachment");   
                }
            }else{
                $path_parts = pathinfo($attachment);
                $extension = $path_parts['extension'];
                $filename =  $path_parts['filename'];
                $this->AddAttachment(base64_encode(file_get_contents($attachment)),"application/$extension",$filename,"attachment");   
            }            
        }
        
        $flashmessenger = new FlashMessenger();
        if($this->error == null){
            if($this->_send()){              
                //Mensaje eliminado por tarea asignada: Eliminar Mensaje de correo enviado correctamente.  
              //  $flashmessenger->addMessage(array('success'=>'Correo electronico enviado correctamente.'));                                
                return true;
            }else{
                $flashmessenger->addMessage(array('danger'=>'Algo salio mal al intentar enviar el correo electronico.'));
                return null;
            }
        }       
    }
    
    public function _setSubject($subject){
        if($subject == ''){$subject = SET_FROM_TITLE;}
        $this->setSubject($subject);
    }
    
    public function setAddAddress($to){        
        $flashmessenger = new FlashMessenger();
        $noEsMail = '';
        if(strpos($to, ',')){
            $destinatarios = explode(',', $to);
            foreach ($destinatarios as $destino){
                echo $destino."</br>";
                if(filter_var(trim($destino), FILTER_VALIDATE_EMAIL)){
                    $this->addTo($destino); 
                }else{
                    $noEsMail .= "<li>".$destino."</li>";
                }
            }
          
        }else{
            if(filter_var($to, FILTER_VALIDATE_EMAIL)){
                $this->addTo($to);
            }else{
                $noEsMail .= $to;
            } 
        }
        if($noEsMail != ''){
            $this->error = true;
            $flashmessenger->addMessage(array('danger'=>'Correo invalido: '.trim($noEsMail,',')));
        }
    }
    
    public function setAddCC($cc){        
        $flashmessenger = new FlashMessenger();
        $noEsMail = '';
        if(strpos($cc, ',')){
            $destinatarios = explode(',', $cc);
            foreach ($destinatarios as $destino){
                if(filter_var(trim($destino), FILTER_VALIDATE_EMAIL)){
                    $this->addCc($destino); 
                }else{
                    $noEsMail .= "<li>".$destino."</li>";
                }
            }
          
        }else{
            if(filter_var($cc, FILTER_VALIDATE_EMAIL)){
                $this->addCc($cc);
            }else{
                $noEsMail .= $cc;
            } 
        }
        if($noEsMail != ''){
            $this->error = true;
            $flashmessenger->addMessage(array('danger'=>'Correo invalido: '.trim($noEsMail,',')));
        }
    }
    
    public function setMsgHTML($msg){
        //$translator = new Translator();
        //if($msg ==''){$msg = $translator->_getTrranslation('Revisar documento adjunto porfavor.');}
        if($msg ==''){$msg = '';}
        $this->addContent("text/html",$msg);
    }
    
    public function getFromTitle(){
        return $this->fromTitle;
    }
    
     public function _send(){ 
        // $company = new CompanyRepository();
        // $company = $company->getById(1);
      
        $this->setFrom("noreply@mymg.app",'Berry Task');// noreply@mymg.app
        
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($this);     
            $status_code = $response->statusCode();
            $status_error = array(
                '400'=>'Bad request',
                '401'=>'Requires authentication',
                '403'=>'From address doesnt match Verified Sender Identity.',
                '406'=>'Missing Accept header. ',
                '429'=>'Too many requests/Rate limit exceeded',
                '500'=>'Internal server error'
            );
             
             if(key_exists($status_code, $status_error)){
                $login = new Login();
                $logRepo = new LogSystemRepository();            
                $logRepo->save(array(
                    'controller'=>'Emailing',
                    'reference'=>$this->to,
                    'type'=>'Mensaje',
                    'process'=>'Enviando correo',
                    'message'=>"Codigo: $status_code",
                    'extra_info'=>$status_error[$status_code],
                    'creado_por'=>$login->getId(),
                    'creado_fecha'=>date('Y-m-d H:i:s')
                ));
                 return null;
             }            
            
            return true;
        } catch (Exception $e) {  
            $login = new Login();
            $logRepo = new LogSystemRepository();            
            $logRepo->save(array(
                'controller'=>'Emailing',
                'reference'=>$this->to,
                'type'=>'Mensaje',
                'process'=>'Enviando correo',
                'message'=>'Caught exception: '. $e->getMessage(),
                'creado_por'=>$login->getId(),
                'creado_fecha'=>date('Y-m-d H:i:s')
            ));
            return null;
        }
       
    }   
}
