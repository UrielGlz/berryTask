<?php
class Emailer extends PHPMailer{
    private $error = null;
    private $msgSuccess = null;
    private $fromTitle = null;
    
    public function sendEmail($data) {
        if(isset($data['from_title'])){ $this->setFromTitle($data['from_title']);}                 
        $this->setAddAddress($data['to']);
        if(isset($data['cc']) && trim($data['cc'])!=''){$this->setAddCC($data['cc']);}  
        if(SEND_BCC_WHEN_SEND_INVOICE){
            $this->AddBCC(SET_MANAGER_MAIL);
        }
        $this->setSubject($data['subject']);       
        $this->setMsgHTML($data['message']);
        $this->AddAttachment($data['attachment']);
        if(isset($data['msg-success'])){$this->setMsgSuccess($data['msg-success']);}
        $this->AltBody = 'Para ver este mensaje usa un lector que soporte HTML.';
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
    
    public function setSubject($subject){
        if($subject == ''){$subject = SET_FROM_TITLE;}
        $this->Subject = $subject;
    }
    
    public function setFromTitle($title){
        if($title == null  || trim($title) == ''){
            $empresa = new CompanyEntity();
            $empresa = $empresa->getById(1);
            $this->fromTitle = $empresa['nombre'];
        }else{                    
            $this->fromTitle = $title;
        }

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
                    $this->AddAddress($destino); 
                }else{
                    $noEsMail .= "<li>".$destino."</li>";
                }
            }
          
        }else{
            if(filter_var($to, FILTER_VALIDATE_EMAIL)){
                $this->AddAddress($to);
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
                    $this->AddCC($destino); 
                }else{
                    $noEsMail .= "<li>".$destino."</li>";
                }
            }
          
        }else{
            if(filter_var($cc, FILTER_VALIDATE_EMAIL)){
                $this->AddCC($cc);
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
        $this->msgHTML($msg);
    }
    
    public function getFromTitle(){
        return $this->fromTitle;
    }
    
     public function setConfig(){
        $company = new EmpresaRepository();
        $company = $company->getById(1);
        
        $this->IsSMTP();
        $this->SMTPAuth = true;        
        $this->SMTPSecure ='ssl';
        $this->Host = "mail.lunis.mx";
        #$this->Port = 587;
        $this->Port = 465;
        $this->Username = "noreply@mgsoftwaresolutions.com";
        $this->Password = "M1vc300184@";
        $this->SetFrom(SET_FROM_MAIL,$company['nombre']);
    }   
}
