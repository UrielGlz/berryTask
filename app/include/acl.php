<?php
    $login = new Login();
    if($login->isLogged()){        
        $login->setCurrentController($controller);
        if($controller == 'index'){
            header('Location: /Controller/Home.php');
            exit;
        }else{
            $acl = new Acl($login->getId());        
            if(!$acl->isAllowed($controller,$action)){
                header('Location: /Controller/Home.php');
                exit;
            }
           
        }
       
    }else{        
        if($controller != 'index'){
            header('Location: /');
            exit;
        } 
    }
