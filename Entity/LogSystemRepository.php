<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class LogSystemRepository extends EntityRepository {

    private $table = 'log_system';
    
    public function save(array $data, $table = null) {
        return parent::save($data, $this->table);
    }
    
    public function getLisLog($options = null){        
    }
}