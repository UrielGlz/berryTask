<?php

/**

 * Description of Login

 *

 * @author carlos

 */

class Login extends LoginRepository {

    private $user = array();

    private $isLogged = null;

    

    public function __construct($options = null) {

        if(null === $options){

            if(isset($_SESSION['user'])){ 

                $this->user = $_SESSION['user'];

                $this->isLogged = true;

            }

        }else{

            $this->isLoginCorrect($options['user'], $_POST['password']);

        }

    } 

       

    public function isLoginCorrect($user, $password) {

        $data = parent::isLoginCorrect($user, $password);

        

        if($data){

            $this->setUser($data);

            $this->isLogged = true;

            return true;

        }

         return null;

    }    

    

    public function setUser($data){        

        $this->user = $data;

        $this->setDefaultController();

        $_SESSION['user'] = $this->user;

        

        $translator = new Translator();

        $translator->setLenguage($this->getIdioma());

        
        //UG Comente est parte porque no aplica el store_id 
        /*if(strpos($data['store_id'], ',') === false){

            $this->setStoreId($data['store_id']);

        } */
        /*else{ 

            $this->user['store_id'] = null;

            $this->user['store_name'] = null;

        }*/        

        

        $_SESSION['user'] = $this->user;

    }

    

    private function setDefaultController(){

        switch($this->getRole()){

            default:

                    $this->user['defaultController'] = 'Home.php';

                break;

        }

    }

    

    public function getDefaultController(){

        return $this->user['defaultController'];

    }

    

    public function getId(){

        return $this->user['id'];

    }

    

    public function getUser(){

        return $this->user['user'];

    }

    
    public function getCompleteName(){

        return $this->user['name']." ".$this->user['last_name'];

    }

    

    public function getRole(){

        return $this->user['role'];

    }    

    

    public function isLogged(){

        return $this->isLogged;

    }

    

    public function setStoreId($storeId){

        $this->user['store_id'] = $storeId;

        $storeRepo = new StoreRepository();

        $storeData = $storeRepo->getById($storeId);

        

        $this->setStoreName($storeData['name']);

    }
    //UG obtener todos los proyectos a los que esta asignado el usuario
    public function SetProjectsByUser($data){
        $user = $data;
        
        $project = new ProjectRepository();

        $projectData = $project->GetProjectsByUser($user);

        return $projectData;    
    }
    

    public function setStoreName($storeName){

        $this->user['store_name'] = $storeName;

    }    

        

    public function getStoreId(){

        return $this->user['store_id'];

    }   

    

    public function getStoreName(){

        return $this->user['store_name'];

    }

    

    public function getAreaBakeryProduction(){

        return $this->user['area_bakery_production_id'];

    }  

    

    public function setCurrentController($controller){

        $this->user['controller'] = $controller;

    }

    

    public function getCurrentController(){

        return  $this->user['controller'];

    }

    

    public function getIdioma(){

        return $_SESSION['user']['lenguage'];

    }

    

    public function getStoreArray(){

        return explode(',', $this->getStoreId());

    }

}