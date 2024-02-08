<?php



/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */



class UserRepository extends EntityRepository {



    private $table = 'users';

    public $flashmessenger = null;

    private $images = null;

    private $options_image = array(

        'allowedExtensions'=>array('image/jpg','image/jpeg','image/png','image/gif','jpg','jpeg','png','gif'),

        'maxFileSizeAllowed'=>16384

    );

    

    private $options = array(

        'user'=>null,

        'password'=>null,

        'nip'=>null,

        'master_key'=>null,

        'name'=>null,

        'last_name'=>null,

        'phone'=>null,        

        'email'=>null,

        'role'=>null,       

       // 'area_bakery_production_id'=>null,

       // 'store_id'=>null,

        'status'=>null,

        'address'=>null,

        //'ssn'=>null,

        'employe_number'=>null,

        'initials'=>null,

        'color'=>null,

    );

    

     public function __construct() {

        if(!$this->flashmessenger instanceof FlashMessenger){

            $this->flashmessenger = new FlashMessenger();

        }

    }

    

     public function _getTranslation($text){

        return $this->flashmessenger->_getTranslation($text);

    }

    

    public function setOptions($data){

      foreach ($this->options as $option => $value){

          if(isset($data[$option])){

            $this->options[$option] = $data[$option];

          }

      }

    }

  

    public function getOptions(){

        return $this->options;

    }

    

     public function getUsuario(){

        return $this->options['user'];

    }

    

    public function getContrasena(){

        return $this->options['password'];

    }

    

    public function getNombre(){

        return $this->options['name'];

    }

    

    public function setImage($images){

        $this->images = $images;

    }

    

    public function getTable(){

        return $this->table;

    }

    

    public function showPhoto($userId){

        $image = $this->getById($userId);

        if(!$image || $image['photo'] == '' || is_null($image['photo'])){return null;}

        

        $string = '<div class="col-md-6" style="margin-left:25%">'

                . '<img src="data:'.$image['photo_type'].';base64,'.base64_encode( $image['photo'] ).'" style="width:100%"/>'

                . '<div class="col-lg-12 col-md-12 col-xs-12 text-right" style="padding-right:0px;margin-top:5px">'

                    . '<a class="btn btn-xs btn-danger" data-id="'.$image['id'].'" onclick="deletePhoto(this)"><i class="fa fa-trash"></i> Eliminar</a>'

                . '</div>';

            

        return $string."</div>";

    }



    public function save(array $data, $table = null) {

        $tools = new Tools();

        // $data['alta_payroll'] = $tools->setFormatDateToDB($data['alta_payroll']);

        // $data['baja_payroll'] = $tools->setFormatDateToDB($data['baja_payroll']);

        

        $data['password'] = MD5($data['password']);

        // if(is_array($data['store_id']) && count($data['store_id']) > 0){

        //     $data['store_id'] = trim(implode(',', $data['store_id']),',');

        // }        

        

        if($data['role'] == '1' || $data['role'] == '2' || $data['role'] == '6'){

            $data['master_key'] = $data['nip'];

        }else{

            $data['master_key'] = '';

        }

        

      //  if(!isset($data['area_bakery_production_id']) || is_null($data['area_bakery_production_id'])){$data['area_bakery_production_id'] = 0;}

        

        $rs = parent::save($data, $this->table);

        

        if($rs){

            $userId = parent::getInsertId();        

            parent::update($userId, array('employe_number'=>$userId), $this->table);

            $this->saveImage($userId);

        }           

        

        return $rs;

        

    }

    

    public function delete($id, $table = null) {

        return parent::delete($id, $this->table);

    }



    public function update($id, $data, $table = null) {

        $tools = new Tools();

        // $data['alta_payroll'] = $tools->setFormatDateToDB($data['alta_payroll']);

        // $data['baja_payroll'] = $tools->setFormatDateToDB($data['baja_payroll']);

        

        $currentData = $this->getById($id);

        if($data['password']!='' && $data['password']!=null){

            $data['password'] = MD5($data['password']);

        }else{

            unset($data['password']);

        }

        

        /*Si no es Mostrador*/

       if($data['role'] == '1' || $data['role'] == '2' || $data['role'] == '6'){ 

            /*Si al momento de hacer update de cambio de rol*/

            if($currentData['role'] != $data['role']){

                if(trim($data['nip'])=='' || $data['nip'] == null){

                    $data['master_key'] = $currentData['nip'];

                }else{

                    $data['master_key'] = $data['nip'];

                }              

            }

        }else{

            $data['master_key'] = '';

        }

        

        if(trim($data['nip']=='') || $data['nip']==null){ unset($data['nip']);}

        

        // if(is_array($data['store_id']) && count($data['store_id']) > 0){

        //     $data['store_id'] = trim(implode(',', $data['store_id']),',');

        // }       

        

        //if(!isset($data['area_bakery_production_id']) || is_null($data['area_bakery_production_id'])){$data['area_bakery_production_id'] = 0;}

        

        foreach($data as $campo => $value){

            if(is_null($data[$campo])){unset($data[$campo]);}

        }

        

        $this->saveImage($id);

        

        return parent::update($id, $data, $this->table);

    }



    public function getById($id, $table = null,$selectAux = null) {

        $query = "SELECT * "

               // . "DATE_FORMAT(alta_payroll,'%m/%d/%Y')as formated_alta_payroll,"

               // . "DATE_FORMAT(baja_payroll,'%m/%d/%Y')as formated_baja_payroll "

                . "FROM $this->table WHERE id = '$id'";

        
        
        $result = $this->query($query);

        if($result->num_rows > 0){

            return $this->resultToArray($result)[0];

        }

        return null;

    }



    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {

       return parent::isUsedInRecord($id, array(
        //UG  AQUI van todas las validaciones donde se use un empleado antes de ser eliminado
        //    'sales_record' => 'creado_por',

        //    'special_orders'=>'creado_por',

        //    'store_request'=>'creado_por',

        //    'transfers'=>'creado_por',

        //    'outputs'=>'creado_por',

        //    'pagos'=>'creado_por',

        //    'physical_inventory'=>'creado_por',

        //    'purchases'=>'creado_por',

        //    'receivings'=>'creado_por',

        //    'receiving_store_requests'=>'creado_por',

        //    'returns'=>'creado_por',

        //    'shipment_store_requests'=>'creado_por'

           ));

    }



    public function getListRoles() {

        $select = "SELECT id,role FROM acl_roles";

        $result = $this->query($select);



        if ($result) {

            $array = array();

            while ($row = $result->fetch_assoc()) {

                $array[$row['id']] = $row['role'];

            }

            return $array;

        }

        return null;

    }

    

    public function getUsersByIds($ids){

        $select = "SELECT *,CONCAT(name,' ',last_name)as completeName FROM $this->table WHERE id IN($ids)";

        $result = $this->query($select);

        

        if($result){

            return $this->resultToArray($result);

        }

        

        return null;

    }

    

    public function getListSelectUsers() {

        $select = "SELECT * FROM $this->table ";

        $result = $this->query($select);



        if ($result) {

            $array = array();

            while ($row = $result->fetch_assoc()) {

                $array[$row['id']] = $row['user'];

            }

            return $array;

        }

        return null;

    }

    

     public function getListUsers($options) {

        $store_id = null;

        $system_variable = null;

        

        $login = new Login();

      
        

        $user_name = null;

        $role = null;

        $store = null;

        $status = " AND status = '1' ";

        $limit = null;

        

        if($options){

            if(trim($options['user']) !== ''){$user_name = " AND find_in_set(user,'{$options['user']}')";}



            if(isset($options['role'])){

                if(is_array($options['role']) && count($options['role']) > 0){

                    $roleIds = implode(',', $options['role']);

                    $role = " AND find_in_set(role,'{$roleIds}')";

                }else{

                    if(trim($options['role'])!= ''){$role = " AND find_in_set(role,'{$options['role']}')";}                     

                }           

            }            



            if(isset($options['status']) && is_array($options['status']) && count($options['status']) > 0){

                $idsStatus = implode(',', $options['status']);

                $status = " AND find_in_set(status,'$idsStatus')";

            }  

        
            if(is_null($user_name) 

                    && is_null($role) 

                    && is_null($status)){$limit = " LIMIT 300";}

        }else{

            $limit = " LIMIT 300";

        }       

        $select = "SELECT *,"

                . "CONCAT(name,' ',last_name)as complete_name,"

                . "fxGetStatusName(status,'User') as status_name, "

                . "fxGetRoleName(role) as role_name "

                . "FROM $this->table "

                . "WHERE system_variable != 1  $system_variable $user_name $role  $status";

        
      
        $result = $this->query($select);



        if ($result) {

           return $this->resultToArray($result);

        }

        return null;

    }

    

    public function getListStatus(){

        $query = "SELECT * FROM status_code WHERE operation = 'User'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $array = array();

            foreach($result as $status){

                $array[$status['code']] = $status['description'];

            }

            return $array;

        }

        return null;

    }

    

    public function storeStringToArray($users){

        $arrayUser = array();

        $users = explode(',', $users);

        foreach($users as $key => $user){

            $arrayUser[$user] = $user;

        }

        return $arrayUser;

    }

    

    public function getUserByNIP($nip){

        $query = "SELECT * FROM $this->table where nip = '$nip' AND status = '1' LIMIT 1 ";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            $result = $this->resultToArray($result);

            return $result[0];

        }

        return null;

    }

    

    public function existUserName($username,$id){

        $query = "SELECT id FROM $this->table WHERE user = '$username' AND id != '{$id}'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return true;

        }

        return null;

    }

    

     public function existNIP($nip,$id){

        $query = "SELECT id FROM $this->table WHERE nip = '$nip' AND id != '{$id}'";

        $result = $this->query($query);

        

        if($result->num_rows > 0){

            return true;

        }

        return null;

    }

    

    public function resizeImage($image,$type){      

        $maxWidth = 480;

        

        switch($type){

            case 'image/jpeg':

            case 'image/jpg':

                $originImage = imagecreatefromjpeg($image);

                break;

            

            case 'image/png': 

                $originImage = imagecreatefrompng($image);

                break;

                

            case 'image/gif': 

                $originImage = imagecreatefromgif($image);

                break;

        }

        

        $originWidth = imagesx($originImage);

        $originHeight = imagesy($originImage);

        

        

        if($originWidth > $originHeight){

            #Es una imagen horizontal    

            $newWidth = $maxWidth;

            $newHeight = $maxWidth * $originHeight/$originWidth;

        }else{

            #Es una imagen vertical

            $newHeight = $maxWidth;

            $newWidth = $maxWidth * $originWidth/$originHeight;

        }

        

        $newImage = imagecreatetruecolor($newWidth, $newHeight); #tamano de nueva imagen

        imagecopyresized($newImage, $originImage, 0, 0, 0, 0, $newWidth, $newHeight, $originWidth, $originHeight);



        switch($type){

            case 'image/jpeg':

            case 'image/jpg':

                imagejpeg($newImage, $image);

                break;

            

            case 'image/png': 

                imagepng($newImage, $image);

                break;

                

            case 'image/gif': 

                imagegif($newImage, $image);

                break;

        }       

    }

    

    public function saveImage($user_id){      

        try {

            $upload = new UploadFile();     

            if($this->images['name'][0] != ''){



                for($i=0; $i <= count($this->images['name'])-1; $i++){

                    if (in_array($this->images['type'][$i], $this->options_image['allowedExtensions']) && $this->images['size'][$i] <= $this->options_image['maxFileSizeAllowed'] * 1024){                        



                        // Tipo de archivo

                        $tipo = $this->images['type'][$i]; 

                        $size = $this->images['size'][$i];                             



                        $file = array(

                            'name' => "Image_$i.".$upload->getExtension($this->images['name'][$i]),

                            'tmp_name' => $this->images['tmp_name'][$i],

                            'size' =>  $this->images['size'][$i],

                            'type' => $this->images['type'][$i]

                        );



                        $upload->uploadFile($file);

                        $uploadedFile = $upload->getUploadedFile();

                        $uploadedFile = end($uploadedFile);

                        $this->resizeImage($uploadedFile, $tipo);                               



                        // Leemos contenido y escapamos caracteres especiales

                        $data = file_get_contents($uploadedFile);

                        $data = addslashes($data);



                        $array = array(

                            'photo'=>$data,

                            'photo_type'=>$tipo,

                            'photo_size'=>$size

                        );



                        /*No uso parent::update, porque marca error al guardar modificaciones, por el campo blob de photo*/

                        //parent::update($user_id,$array, 'users');    

                        parent::query("UPDATE users SET photo = '{$data}', photo_type = '{$tipo}', photo_size = '{$size}' WHERE id = $user_id");



                    }else{

                        $this->flashmessenger->addMessage(array(

                        'danger'=>$this->_getTranslation("Formato de archivo no permitido o excede el tamaño limite de {$this->options_image['maxFileSizeAllowed']} Kbytes.")));

                        return null;

                    }

                }

            }

            return true;

        } catch (Exception $exc) {

            echo $this->flashmessenger->addMessage(array('danger'=>$exc->getMessage()));

            return null;

        }       

    }

    

    public function _deletePhoto($id){

        return parent::query("UPDATE users SET photo = null, photo_type = null, photo_size = null WHERE id = $id");

    }

}