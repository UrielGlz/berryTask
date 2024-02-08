<?php
class FileManagement  {
    private $operations = array(
        'invoice'=>array(
            'allowedExtensions'=>array('pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','tiff','bmp'),
            'maxFileSizeAllowed'=>1000000,
            'pathToSave'=>PATH_INVOICE_ATTACHMENT,
            'alt'=>'/app/resources/docs/invoice_attachments/'),
        'customer_payment'=>array(
            'allowedExtensions'=>array('pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','tiff','bmp'),
            'maxFileSizeAllowed'=>1000000,
            'pathToSave'=>PATH_CUSTOMER_PAYMENTS_ATTACHMENT,
            'alt'=>'/app/resources/docs/customer_payments_attachments/'),
        'purchase'=>array(
            'allowedExtensions'=>array('pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','tiff','bmp'),
            'maxFileSizeAllowed'=>1000000,
            'pathToSave'=>PATH_PURCHASE_ATTACHMENT,
            'alt'=>'/app/resources/docs/purchase_attachments/'),
            
        
        'task'=>array(
            'allowedExtensions'=>array('pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','tiff','bmp'),
            'maxFileSizeAllowed'=>1000000,
            'pathToSave'=>PATH_TASK_ATTACHMENT,
            'alt'=>'/app/resources/docs/task_attachments/'),
        'deposit'=>array(
            'allowedExtensions'=>array('pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','tiff','bmp'),
            'maxFileSizeAllowed'=>1000000,
            'pathToSave'=>PATH_DEPOSIT_ATTACHMENT,
            'alt'=>'/app/resources/docs/deposit_attachments/'),
        
      
    );
    
    public function saveFile($files,$prefix,$operation){ 
        $file = new UploadFile();
        $options_files = $this->operations[$operation];
        
        $file->setAllowedExtensions($options_files['allowedExtensions']);
        $file->setMaxFileSizeAllowed($options_files['maxFileSizeAllowed']);
        $file->setTempFolder($options_files['pathToSave']);

        $rsUpload = $file->uploadMultipleFile($files, $prefix);
        if(!$rsUpload){
            //$flashmessenger = new FlashMessenger();
            //$flashmessenger->addMessage(array('danger'=>$file->getMessageError()));  
            return null;
        }        
        return true;
    }
    
    public function getStringListFilesByOperationAndPrefix($operation,$prefix){
        $options_files = $this->operations[$operation];
        $dir = $options_files['pathToSave'];
        $alt = $options_files['alt'];
      
        if(file_exists($dir)){ 
            $string =  "<ul style='list-style-type:none; padding-left:10px'>";                

            chdir($dir); /*Cambiamos al directori para leer archivos*/
            $prefijo = $prefix."+___+"; 
            $files = glob($prefijo."*"); /*Se obtienen archivos que comienzen con el patron $prefijo*/

           foreach($files as $pathFile){
                $file = explode(".", $pathFile);
                $name = $file[0];        
                $extension = $file[1];

                $name = explode("+___+", $name);
                $string .= "<li class='{$name[2]}' style='padding:5px;'>
                        <a class='btn btn-xs btn-danger _deleteFile' data-uuid='{$name[2]}' data-filedelete='".$alt.$pathFile."'>
                            <i id='trash' class='fa fa-trash'></i>
                        </a>                         
                        <span class='text-right' style='cursor:pointer' onclick='javascript: void window.open(\"$alt$pathFile\",\"$name[2]\",\"width=700,height=500,status=1,scrollbars=1,resizable=1\")')>{$name[1]}.{$extension}</span>                                                      
                      </li>";              
            }
            $string .= "</ul>";
            return $string;
        }         
    }
    
    public function getAttachemntsFilesByOperationAndPrefix($operation,$prefix,$defaultAttachments){
        $options_files = $this->operations[$operation];
        $dir = $options_files['pathToSave'];
        $alt = $options_files['alt'];
      
        if(file_exists($dir)){ 
            $string =  "<ul style='list-style-type:none; padding-left:10px'>";         
            foreach($defaultAttachments as $attach){
                $checked = 'checked';
                $readOnly = "onclick='javascript: return false;'";
                if(key_exists('notChecked', $attach)){$checked = ''; $readOnly = '';}
                $string .= "<li style='padding:5px;'>
                            <input type='checkbox' name='attachmentsFiles[]' class='_attachementForEmail' value='".$attach['filePath']."' $checked $readOnly>                        
                            <span class='text-right' style='cursor:pointer' onclick='javascript: void window.open(\"{$attach['filePathForLink']}\",\"{$attach['fileName']}\",\"width=700,height=500,status=1,scrollbars=1,resizable=1\")')>{$attach['fileName']}</span>                                                      
                          </li>";  
            }

            chdir($dir); /*Cambiamos al directori para leer archivos*/
            $prefijo = $prefix."+___+"; 
            $files = glob($prefijo."*"); /*Se obtienen archivos que comienzen con el patron $prefijo*/
            
            if(count($files) > 0){
                foreach($files as $pathFile){
                    $file = explode(".", $pathFile);
                    $name = $file[0];        
                    $extension = $file[1];

                    $name = explode("+___+", $name);
                    $string .= "<li style='padding:5px;'>
                            <input type='checkbox' name='attachmentsFiles[]' class='_attachementForEmail' value='".ROOT.$alt.$pathFile."'>                        
                            <span class='text-right' style='cursor:pointer' onclick='javascript: void window.open(\"$alt$pathFile\",\"$name[2]\",\"width=700,height=500,status=1,scrollbars=1,resizable=1\")')>{$name[1]}.{$extension}</span>                                                      
                          </li>";              
                }
                $string .= "</ul>";
              
            }
            return $string;
        }  
        
        return null;
    }
    
}