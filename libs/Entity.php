<?php

class Entity{
    
    protected $db;
    /**
     * El constructor asigna la variable $db como una instancia de
     * la base de Datos
     */
    public function __construct()
    {
        $this->db = \Database::getInstance();
        $this->declareAttributes();
    }

    /**
     * Polyfill de la funcion de php7.3 array_key_first
     *
     * @param array $array
     * @return String
     */
    function array_key_first($array)
    {
        return key(array_slice($array, 0, 1));
    }

    /**
     * Esta es una función inteligente que crea la tabla en caso de que no exista
     */

    public function createTable(){
        $table = $this->getTableName();

        $attributesToLoad = $this->attributes;
        
        $values = "(";

            $i = 0;

            foreach ($attributesToLoad as $key => $attribute) {

                    $attributeToInsert = $attribute['attribute'].' '.$attribute['type'];

                    if ($i < (count($attributesToLoad)-1)) {
                        $values .= $attributeToInsert.", ";
                    
                    }else{
                        $values .= $attributeToInsert;
                     
                    }
                

                $i++;
            }
            
        $values .= ")";

        $sql = "CREATE TABLE IF NOT EXISTS $table $values";

        $result = $this->db->Execute($sql);

        return $result;
    } 

    /**
     * Convierte el contenido del arreglo $this->attributes en atributos independientes;
     * 
     * @return void
     */

    private function declareAttributes(){
        if (count($this->attributes) > 0 ) {
            foreach ($this->attributes as $key => $attribute) {
                $attributeName = $attribute['attribute'];

                $this->{$attributeName} = null;
            }
        }
    }

    /**
     * Obtiene los atributos de la clase hijo
     *
     * Nota: Los atributos deben ser instanciados como Protected o Public
     * Para que puedan ser tomados por la clase padre Mode
     * 
     * @return Array
     */
    public function getAttributes($param = 'restrict_null'){
        $attributes = get_object_vars($this);
        unset($attributes['db']);
        unset($attributes['attributes']);

        if($param !== 'allow_null'){
            foreach ($attributes as $key => $value) {
                if ($value === NULL) {
                    unset($attributes[$key]);
                }
            }
        }

        return $attributes;
    }

    /**
     * Obtiene el nombre de la tabla por operar a partir del nombre
     * del Modelo
     *
     * @return string
     */
    protected function getTableName(){
        $table = get_class($this);
        $table = str_replace('Entity','',$table);
        $table = lcfirst($table);
        return $table;
    }

    /**
     * Devuelve el primer Atributo, asociado al id de la tabla
     * @version >5.3
     * @return Array
     */
    protected function getFirstAttribute(){

        $attributes = $this->getAttributes('allow_null');

        $firstKey = $this->array_key_first($attributes);

        $firstValue = $attributes[$firstKey];

        $arrayReturn = array($firstKey => $firstValue);

        return $arrayReturn;
    }

     /**
     * Actualiza los componentes de una tabla al valor que los tiene
     * actualmente
     * @return bool
     */
    public function update(){
        $table = $this->getTableName();

        $attributes = $this->getAttributes();

        $sql = "UPDATE $table SET ";

        $i = 0;

        foreach ($attributes as $key => $value) {
            if ($i > 0) {
                if ($i < (count($attributes)-1)) {
                    $sql .= "$key = '".$value."', ";
                }else{
                    $sql .= "$key = '".$value."' ";
                }
            }

            $i++;
        }
        
        $firstAttribute = $this->getFirstAttribute();
                
        $firstKey = $this->array_key_first($firstAttribute);

        $firstValue = $firstAttribute[$firstKey];

        $sql = $sql."WHERE $firstKey = '".$firstValue."'";

        return $this->db->Execute($sql);        
    }

    /**
     * Inserta una nueva tupla en una tabla
     * 
     * @return void
     */

    public function insert(){

        $table = $this->getTableName();

        $attributes = $this->getAttributes();
        
        $keys = "(";

        $values = "(";

            $i = 0;

            foreach ($attributes as $key => $value) {

                    if ($i < (count($attributes)-1)) {
                        $keys .= $key.", ";
                        $values .= "'".$value."', ";
                    }else{
                        $keys .= $key;
                        $values .= "'".$value."'";
                    }
                

                $i++;
            }
            
        $keys .= ")";

        $values .= ")";

        $sql = "INSERT INTO $table $keys VALUES $values";
        
        $result = $this->db->Execute($sql);

        return $result;

    }

    /**
     * Llena todos los atributos de un modelo a partir de una consulta
     *
     * @param [string | number] $id
     * @return Array
     */
    public function getById(){

        $table = $this->getTableName();
        
        $firstAttribute = $this->getFirstAttribute();

        $firstKey = $this->array_key_first($firstAttribute);

        $firstValue = $firstAttribute[$firstKey];

        $sql = "SELECT * FROM $table WHERE $firstKey = $firstValue";
       
        $result = $this->db->fetchUnique($sql);
        
        if ($result) {
            //A partir del resultado se asignan los atributos;
            foreach ($result as $key => $value) {
                $this->$key = $value;
            }

            return $result;
        }else{
            return false;
        }
        
    }

    /**
     * Obtiene una tupla por columna,
     * No devuelve ningun resultado, sino que asigna el resultado obtenido
     * a los atributos del modelo
     * 
     * @return void
     */
    public function getByColumnName($column,$where=null){

        $table = $this->getTableName();
        
        $where = $where ? "and $where" : "";

        $value = $this->$column;

        /**
         * @since 2022-04-30
         * Si se requiere traer unicamente los registros con estado activo,
         * se debe agregar en el parametro where, así, el orm no estará limitado
         * a ningun tipo de registro
         */

        $sql = "SELECT * FROM $table WHERE 1
                 AND $column = '".$value."'
                 $where";

        $result = $this->db->fetchUnique($sql);
  
        if ($result) {
            //A partir del resultado se asignan los atributos;
            foreach ($result as $key => $value) {
                $this->$key = $value;
            }

            return true;

        }else{
            
           return false;
        }

        
    }
    /**
     * Devuelve todos los registros de una tabla
     * el parametro $where permite colocar una condicion de la consulta
     * @return array | boolean
     */

    public function getList($where = null){

        $thisClass = get_class($this);

        $table = $this->getTableName();

        $attributes = $this->getAttributes('allow_null');
                
        $firstAttribute = $this->getFirstAttribute();

        $firstKey = $this->array_key_first($firstAttribute);
         
        $aux = 1;
        $attributesString = "";

        foreach ($attributes as $key => $value) {
            if ($aux == count($attributes)) {
                $attributesString .= "t.$key";
            }else{
                $attributesString .= "t.$key, ";
            }
            $aux++;
        }
                
        $where = $where !== null ? $where : '1';
       

        $sql = "SELECT $attributesString from $table t where $where
                order by t.".$firstKey;
                
    
        $result = $this->db->fetchAll($sql);
    

        $resultToReturn = array();

        if ($result) {
            
            foreach ($result as $eachKey => $eachResult) {
                $auxResponseObject = new $thisClass();
                
                
                $auxResponseObject->fillParams($eachResult);


                $resultToReturn[] = $auxResponseObject;
            }
        }

        return $resultToReturn;
    }
    
    /**
     * Como los parametros de las entidades son privados, se requiere un metodo que los
     * llene cuando los objetos son instanciados en el metodo getList
     */

    private function fillParams($fillingArray){
        foreach ($fillingArray as $fillingKey => $fillingValue) {
            $this->$fillingKey = $fillingValue;
        }
    }

    /**
     * Devuelve el ultimo id
     * 
     * @return string
     */

    public function getLastId(){

        $firstAttribute = $this->getFirstAttribute();

        $firstKey = $this->array_key_first($firstAttribute);


        $table = $this->getTableName();

        $sql = "select $firstKey as lastId from $table order by $firstKey desc";

        $result = $this->db->fetchUnique($sql);

        return $result['lastId'];
    }

    /**
     * La siguiente funcion asigna los atributos comunicados por metodo GET o POST
     * al listado de atributos del modelo
     * 
     * @return void
     */

    public function setAttributes(){
        $attributes = $this->getAttributes('allow_null');

        $bodyData = json_decode(file_get_contents("php://input"),true);

        /**
         * @deprecated Esta validación debe ser corregida en versiones posteriores del proyecto
         */

        if ($bodyData && $_SERVER['REQUEST_METHOD'] != 'POST') {
            responseRequest(false,"El metodo de envio debe ser POST");
            die();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $bodyData) {
            foreach ($attributes as $attr => $value) {
                if (isset($bodyData["$attr"])) {
                    $this->$attr = $bodyData["$attr"];
                }
            }
        }else{
            foreach ($attributes as $attr => $value) {
                if (isset($_REQUEST["$attr"])) {
                    $this->$attr = $_REQUEST["$attr"];
                }
            }
        }

        
    }

}

?>