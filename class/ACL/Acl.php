<?php
/**
 * Description of Acl
 *
 * @author carlos
 */
class Acl extends AclRepository {
    private $resourcesId  = array();
    private $resourcesName = array();
    private $user = array(
        'id'=>null,
        'usuario'=>null,
        'nombre'=>null,
        'role'=>null,
        'unidad'=>null,
    );
    
    private $userId = null;
    private $resourcesForAll = array(
        'home'=>'HomeController',
        'ajax-ajax'=>'ajax-ajax',
        'index'=>'index',
        ''=>'-'
    );
    
    public function __construct($userId = null) {
        if(null === $userId){
            $userId = $SESSION['usuario']['id'];
        }
        $this->userId = $userId;
        $this->createACL();
    }
    
    public function createACL(){
        $dataUser = $this->getUserById($this->userId);
        $this->setUser($dataUser);
        $this->setResources($this->user['role']);
    }
    
    public function setResources($idRole) {
        $resources = parent::setResources($idRole);
        $resourcesTemp = array();
        $resourcesName = array();
        
        foreach($resources as $resource){
            if(trim($resource['action'])!=''){
                $resourcesTemp[$resource['id']] = $resource['controller']."-".$resource['action'];
                
                $rnameTemp = strtolower($resource['controller']."-".$resource['action']);
                $resourcesName[$rnameTemp] = $resource['controller']."-".$resource['action'];
            }else{
                $resourcesTemp[$resource['id']] = $resource['controller'];
                $rnameTemp = strtolower($resource['controller']);
                $resourcesName[$rnameTemp] = $resource['controller'];
            }
        }
        $this->resources = $resourcesTemp;
        $this->resourcesName = $resourcesName;        
    }
    
    public function setUser($data){
        $this->user = $data;
    }
    
    public function isAllowed($controller,$action){
        $resource = $this->mergeResource($controller, $action);
        $isAllowed = $this->isResourcesForAll($resource);
       
        if($isAllowed){return true;}
         
        $isAllowed = $this->isAssignedResource($resource);
       
        return $isAllowed;
        
    }
    
    public function isResourcesForAll($resource){
        if(isset($this->resourcesForAll[$resource])){
            return true;
        }else{
            return null;
        }
    }
    
    public function mergeResource($controller,$action){
        if($action != '' or null != $action){
           $resource = strtolower($controller)."-".strtolower($action);
        }else{
           $resource = strtolower($controller);
        }
        
        return $resource;
    }
    
    public function isAssignedResource($resource){
        if(isset($this->resourcesName[$resource])){
            return true;
        }else{
            return null;
        }
    }    
}