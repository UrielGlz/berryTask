<?php
/**
 * Description of AclRepository
 *
 * @author carlos
 */
class AclRepository extends DataBase{
    //put your code here
    
    public function getUserById($id){
        $select = "SELECT * FROM users WHERE id = '$id'";
        $result = $this->getInstance()->execute($select);
        
        if($result){
            $set = $this->resultToArray($result);
            return $set[0];
        }
    }
    
    public function getIdResourcesFromRole($idRole){
        $select = "SELECT resources FROM acl_roles WHERE id = '$idRole'";
        $result = $this->getInstance()->execute($select);
        
        if($result){
            $row = $result->fetch_object();
            return $row->resources; //string exemple: 1,2,12
        }
        
        return null;
    }
    
    public function getResourcesById($idResources){
        if($idResources == 0){return 0;}
        
        $select = "SELECT * FROM acl_resources WHERE id IN($idResources)";
        $result = $this->getInstance()->execute($select);
        
        if($result){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function setResources($idRole){
        $idResources = $this->getIdResourcesFromRole($idRole);
        $resources = $this->getResourcesById($idResources);
        
        return $resources;
    }
    
    public function resultToArray($result){
        for ($set = array(); $row = $result->fetch_assoc(); $set[] = $row);
        return $set;
    }
}