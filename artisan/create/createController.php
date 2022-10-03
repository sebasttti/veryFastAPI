<?php

$arguments = getopt("c:");

if (!isset($arguments['c'])) {
    echo "Por favor introduce un parametro para el controlador \n";
    die();
}

$controllerName = $arguments['c'];

$controllerName = ucfirst($controllerName);

// Se crea el archivco

$fileName = dirname(dirname(__DIR__)).'/controllers/'.$controllerName.'.php';

$nFile = fopen($fileName, "w") ;

// Se inserta el template dentro del archivo

$content = "<?php
class $controllerName extends Controller{

    public function __construct()
    {
        
    }
   
    public function index(){
        echo \"Controlador $controllerName llamado correctamente\";
    }
}
?>
";


fwrite($nFile,$content);

//Se cierra el archivo
fclose($nFile);

echo "\n";
echo "Se creo la clase $controllerName en la locación $fileName \n";
echo "\n\n";
?>