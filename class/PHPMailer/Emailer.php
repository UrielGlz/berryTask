<?php
require 'autoload.php';
putenv("SENDGRID_API_KEY=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");

class Emailer extends \SendGrid\Mail\Mail{
    private $error = null;
    private $msgSuccess = null;
    private $fromTitle = null;
    
    public function sendEmail($data) {     
        // echo "<pre>";var_dump($data);echo "</pre>";exit;
  
        $this->setAddAddress($data['to']);
        if(isset($data['cc']) && trim($data['cc'])!=''){$this->setAddCC($data['cc']);}  
        if(SEND_BCC_WHEN_SEND_INVOICE){
            $this->AddBCC(SET_MANAGER_MAIL);
        }
        $this->_setSubject1($data['subject']);       
        $this->setMsgHTML($data['message']);
       
        if(isset($data['attachment'])){
            if(is_array($data['attachment'])){
                foreach($data['attachment'] as $attachment){
                    $this->AddAttachment($attachment);   
                }
            }else{
                $this->AddAttachment($attachment);   
            }            
        }

        if(isset($data['msg-success'])){$this->setMsgSuccess($data['msg-success']);}
        //$this->AltBody = 'Para ver este mensaje usa un lector que soporte HTML.';
        $this->setConfig();
        
        $flashmessenger = new FlashMessenger();
        if($this->error == null){
            if($this->Send()){
                if(trim($this->msgSuccess)!='' && $this->msgSuccess != null){
                    $flashmessenger->addMessage(array('success'=>$this->msgSuccess));
                }                
                return true;
            }else{
                $flashmessenger->addMessage(array('danger'=>"$this->ErrorInfo"));
                return null;
            }
        }       
    }
    
    public function _setSubject1($subject){
        if($subject == ''){$subject = SET_FROM_TITLE;}
        $this->setSubject2($subject);
    }
    
    public function setMsgSuccess($msg){
        $this->msgSuccess = $msg;
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
        if($msg ==''){$msg = "Revisar archivo adjunto porfavor.";}
        $this->addContent("text/html",$msg);
    }
    
    public function getFromTitle(){
        return $this->fromTitle;
    }
    
     public function setConfig(){
        $company = new CompanyRepository();
        $company = $company->getById(1);
        
        $this->setFrom("noreply@mymg.app",$company['name']);
        
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
        
        /*
        $this->IsSMTP();
        $this->SMTPAuth = true;        
        $this->SMTPSecure ='ssl';
        $this->Host = "cfdi.live";
        //$this->Port = 587;
        $this->Port = 465;
        $this->Username = "noresponder@cfdi.live";
        $this->Password = "Lun1s0905+*13";
        $this->SetFrom(SET_FROM_MAIL,$company['name']);*/
    }   
}
