<?php

class Database{
    private $host;
    private $user;
    private $password;
    private $db_name;
  
    private $dbh;
    private $stmt;
    private $error;

    /**
     * La variable $instance almacenara la instancia de la
     * clase Database, acorde con el patron Singleton
     */
    private static $instance = null;

    /**
     * el metodo getInstance adapta la clase al patron
     * Singleton
     * @return class
     */
    public static function getInstance(){
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
  
    private function __construct(){

        //Configuracion de parametros

        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->password = DB_PASSWORD;
        $this->db_name = DB_NAME;
  
        //configurar conexion
        $dsn = "mysql:host=$this->host;dbname=$this->db_name";
        $options = [
          \PDO::ATTR_PERSISTENT => true,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
  
        try {
            $this->dbh = new \PDO($dsn,$this->user,$this->password,$options);
            $this->dbh->exec('set names utf8');
  
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;die;
        }
  
    }
  
    //preparar consulta
    public function query($sql){
        $this->stmt=$this->dbh->prepare($sql);
    }
  
    //unir valores
    public function bind($parametro,$valor,$tipo=null){
  
        if (is_null($tipo)) {
           switch (true) {
               case is_int($valor):
                  $tipo=\PDO::PARAM_INT;
               break;
               case is_bool($valor):
                  $tipo=\PDO::PARAM_BOOL;
               break;
               case is_null($valor):
                  $tipo=\PDO::PARAM_NULL;
               break;
               default:
                  $tipo=\PDO::PARAM_STR;
               break;
           }
        }
  
        $this->stmt->bindValue($parametro,$valor,$tipo);
  
    }
  
    //ejecutar instruccion
    public function execute($sql){
       $this->query($sql);
       return $this->stmt->execute();
    }
  
    //obtener los registros de la consulta
    public function fetchAll($sql){
        $this->execute($sql);
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);   
    }
  
    //obtener los registro de la consulta
    public function fetchUnique($sql){
        $this->execute($sql);     
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
        
    }
  
    //obtener cantidad de registros
    public function rowCount($sql){
        $this->execute($sql);
        return (int)$this->stmt->rowCount();
    }
  }
  

?> 
