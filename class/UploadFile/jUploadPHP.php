<?php

/*
 * @class name: jUploadPHP
 * @author: cali rojas <juancarlosr@msn.com>
 * @web site: www.lewebmonster.com
 * @description: simple class for file uploading
 * @date: june 29, 2012
 * @last update: <date> <who>
 * 
 */

class jUploadPHP {

    private $tempFolder, $strFilePrefix, $strFileLocation, $objFile;
    private $allowedExtensions = array(), $iUploadStatus, $maxFileSizeAllowed;

    const FILE_NOT_SPECIFIED = 0, UPLOADING_ERROR = 1, FILE_UPLOADED = 2;
    const NOT_ALLOWED_EXTENSION = 3, MAX_FILE_SIZE_ERROR = 4;

    public function __construct() {
        
    }

    public function removeUploadedFile() {
        unlink($this->strFileLocation);
        return $this;
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
        return (in_array(pathinfo($this->objFile['name'], PATHINFO_EXTENSION), $this->allowedExtensions));
    }

    public function uploadFile($fFile, $blnUseFilePrefix = true) {
        $this->strFilePrefix = ($blnUseFilePrefix) ? substr(md5(uniqid(rand())), 0, 6) : '';
        $this->objFile = $fFile;
        
        if ($this->objFile['name'] != '') {
            if ($this->isAllowedExtension()) {
                if ($this->getFileSize() > $this->getMaxFileSizeAllowed()) {
                    $this->iUploadStatus = self::MAX_FILE_SIZE_ERROR;
                    return false;
                } else {
                    if (copy($this->objFile['tmp_name'], $this->getFileLocation())) {
                        $this->iUploadStatus = self::FILE_UPLOADED;
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
    
    public function getFileLocation(){
        return $this->tempFolder . $this->strFilePrefix . $this->objFile['name'];
    }

    public function setTempFolder($strPath) {
        $this->tempFolder = $strPath;

        return $this;
    }

    public function getFullFileLocation() {
        return $this->strFileLocation;
    }

    private function getTempFolder() {
        return $this->tempFolder;
    }
}