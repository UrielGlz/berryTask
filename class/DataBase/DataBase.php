<?php

class DataBase extends mysqli {

    private $mysqlcon;
    static private $instance;
    
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $hostname = DB_HOSTNAME;
    private $dbname = DB_NAME;
    
    #La utilizo solo para regresar nombre de DB cuando uso la funcion getDataBaseName();
    static $dbname_aux = DB_NAME;    
    
    public static $a_especial_chars = array(
        'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ã' => 'a', 'Ä' => 'a', 'Å' => 'a',
        'Æ' => 'a', 'Ç' => 'c', 'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e',
        'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'Ð' => 'd', 'ñ' => 'n',
        'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o', 'Ø' => 'o',
        'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ý' => 'y', 'Þ' => 'b',
        'ß' => 's', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
        'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'd',
        'Ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y',
        'þ' => 'b', 'ÿ' => 'y', '\'' => '', '%' => '', '"' => ''
    );

    const WARNING = 1;
    const STOP_CRITICAL = 2;

    public static function getInstance($db = null) {
        if (self::$instance == null) {
            self::$instance = new self();
        } 
        return self::$instance;
    }
    
    public static function getDataBaseName(){
        return self::$dbname_aux;
    }
    
    static function getInsertId(){
        return self::$instance->insert_id;
    }

    private function __construct($db = null) {
        if($db!==null){
             $this->dbname = $db;
             $this->dbname_aux = $db;
        }
        $mysqli = parent::__construct($this->hostname, $this->username, $this->password, $this->dbname);
        if (mysqli_connect_errno()) {
            throw new DataBase_Exception('Connect failed: ' . mysqli_connect_error(), self::STOP_CRITICAL);
            return false;
        }
        $this->set_charset("utf8");
        return $this;
    }
    
    public function execute($query) {
        $r = $this->query($query);
        if ($r) {
          return $r;
        } else {
          $error = "Al intentar la consulta, se produjo: <i>$this->errno</i>";
          $error = "Al intentar <b>$query</b> se produjo: <i>$this->error</i>";
          throw new DataBase_Exception($error, self::STOP_CRITICAL);
          return false;
        }
  } 
}

class DataBase_Exception extends Exception {

    public function getErrorMessage() {
        return '<span class="error_database">' . $this->getMessage() . '</span>';
    }

}