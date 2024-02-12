<?php

/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */



/**

 * Description of uploadPHP

 *

 * @author carlos

 */

class UploadFile {

    private $objFile;

    private $strFilePrefix;

    private $allowedExtensions = array(

        'jpg',

        'jpeg',

        'png'

    );

    private $maxFileSizeAllowed = 10000000;

    private $tempFolder = PATH_TEMP_DOCS;

    private $strFileLocation;

    private $iUploadStatus;

    private $error = null;

    private $uploadedFiles = array();



    const FILE_NOT_SPECIFIED = 0, UPLOADING_ERROR = 1, FILE_UPLOADED = 2;

    const NOT_ALLOWED_EXTENSION = 3, MAX_FILE_SIZE_ERROR = 4;



   public function __construct() {

       $this->setAllowedExtensions($this->allowedExtensions);

       $this->setMaxFileSizeAllowed($this->maxFileSizeAllowed);

       $this->setTempFolder($this->tempFolder);

   }

   

   public function uploadFile($fFile, $blnUseFilePrefix = null,$setFileName = null) {

        $this->strFilePrefix = ($blnUseFilePrefix) ? substr(md5(uniqid(rand())), 0, 6) : '';

        $this->objFile = $fFile;

       

        if ($this->objFile['name'] != '') {           

            if ($this->isAllowedExtension()) { 

                if($setFileName){$this->objFile['name'] = $setFileName.".".$this->getExtension($this->objFile['name']);}

                if ($this->getFileSize() > $this->getMaxFileSizeAllowed()) {

                    $this->iUploadStatus = self::MAX_FILE_SIZE_ERROR;

                    return false;

                } else {

                    if (copy($this->objFile['tmp_name'], $this->getFileLocation())) {

                        $this->iUploadStatus = self::FILE_UPLOADED;

                        $this->setUploadedFile($this->getFileLocation());

                        return true;

                    } else {

                        $this->iUploadStatus = self::UPLOADING_ERROR;

                        return false;

                    }

                }

            } else {

                $this->iUploadStatus = self::NOT_ALLOWED_EXTENSION;

                return false;

            }

        } else {

            $this->iUploadStatus = self::FILE_NOT_SPECIFIED;

            return false;

        }

    }

    

    public function setUploadedFile($file){

        $this->uploadedFiles[] = $file;

    }

    

    public function getUploadedFile(){

        return $this->uploadedFiles;

    }

   

    public function uploadMultipleFile($files,$pref1) {     

        $flashmessenger = new FlashMessenger();

        

        if(is_array($files['name'])){

            $count = count($files['name']);

            for ($i = 0; $i <= $count-1; $i++) {

                $pref2 = uniqid();

                $originalName = $files['name'][$i];

                $filename = explode(".", $files['name'][$i]);

               

                $file = array(

                    'name' => $pref1."+___+".$filename[0]."+___+".$pref2.".".$this->getExtension($files['name'][$i]),

                    'tmp_name' => $files['tmp_name'][$i],

                    'size' => $files['size'][$i],

                    'type' => $files['type'][$i]

                );

                

                if(!$this->uploadFile($file,null)){

                    $flashmessenger->addMessage(array('danger'=>$originalName."=>".$this->getMessageError()));

                    $this->error = true;

                }

            }            

        }

        else{

            $pref2 = uniqid();

            //$pref2 = '3010smp'; //UG LO elimina para mantener le id unico 09.02.24

            $originalName = $files['name'];

            $filename = explode(".", $files['name']);

            $file = array(

                    'name' => $pref1."+___+".$filename[0]."+___+".$pref2.".".$this->getExtension($files['name']),

                    'tmp_name' => $files['tmp_name'],

                    'size' => $files['size'],

                    'type' => $files['type']

                );



                if(!$this->uploadFile($file,null)){

                    $flashmessenger->addMessage(array('danger'=>$originalName." => ".$this->getMessageError()));

                    $this->error = true;

                }

            

        } 

        

        if($this->error){

            //vaciar directorio con files subidos

            return null;

        }

        

        $flashmessenger->addMessage(array('success'=>'All files was imported successfully.'));

        return true;

    }

    

    public function removeUploadedFile() {

        unlink($this->strFileLocation);

        return $this;

    }



    public function getUploadStatusCode() {

        return $this->iUploadStatus;

    }



    public function setAllowedExtensions(array $allowedExtensions) {

        $this->allowedExtensions = $allowedExtensions;

        return $this;

    }



    public function setMaxFileSizeAllowed($maxFileSizeAllowed) {

        $this->maxFileSizeAllowed = $maxFileSizeAllowed;

        return $this;

    }



    public function getMaxFileSizeAllowed() {

        return $this->maxFileSizeAllowed;

    }



    public function getFileSize() {

        return $this->objFile['size'];

    }



    private function isAllowedExtension() {

        return (in_array($this->getExtension($this->objFile['name']), $this->allowedExtensions));

    }

    

    public function getExtension($file){

        return pathinfo($file, PATHINFO_EXTENSION);

    }

    

    public function deleteTempFile() {

        unlink($this->getFileLocation());

        return true;

    }

    

    public function getFileLocation(){

        if(!is_dir($this->tempFolder)){

            mkdir($this->tempFolder,0777,true);

        }

        return $this->tempFolder . $this->strFilePrefix . $this->objFile['name'];

    }



    public function setTempFolder($strPath) {

        $this->tempFolder = $strPath;



        return $this;

    }



    public function getFullFileLocation() {

        return $this->strFileLocation;

    }



    public function getTempFolder() {

        return $this->tempFolder;

    }

    

    public function statusCodeIs($theConstant) {

        switch ($theConstant) {

            case 'FILE_UPLOADED':

                return self::FILE_UPLOADED;

                break;

            case 'FILE_NOT_SPECIFIED':

                return self::FILE_NOT_SPECIFIED;

                break;

            case 'MAX_FILE_SIZE_ERROR':

                return self::MAX_FILE_SIZE_ERROR;

                break;

            case 'NOT_ALLOWED_EXTENSION':

                return self::NOT_ALLOWED_EXTENSION;

                break;

            case 'UPLOADING_ERROR':

                return self::UPLOADING_ERROR;

                break;

            default:

                return -1;

        }

    }



    public function getMessageError() {

        switch ($this->getUploadStatusCode()) {

            case $this->statusCodeIs('FILE_UPLOADED'):

                //we won't use this status here, it's only to check if the file was uploaded

                break;

            case $this->statusCodeIs('FILE_NOT_SPECIFIED'):

                return 'File not specified. Please, select the file you want to upload.';

                break;

            case $this->statusCodeIs('MAX_FILE_SIZE_ERROR'):

                return 'Max. file size allowed is ' . $this->getMaxFileSizeAllowed();

                break;

            case $this->statusCodeIs('NOT_ALLOWED_EXTENSION'):

                return 'The file extension is not valid.';

                break;

            case $this->statusCodeIs('UPLOADING_ERROR'):

                return 'Uploading error. Contact your admin to check the uploading folder permissions.';

                break;

            default:

                return "There's something wrong with the uploading process.";

        }

    }



}