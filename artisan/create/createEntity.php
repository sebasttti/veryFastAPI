<?php

$arguments = getopt("e:");

if (!isset($arguments['e'])) {
    echo "Por favor introduce un parametro para la entidad \n";
    die();
}

$entityName = $arguments['e'];

$entityName = ucfirst($entityName);

// Se crea el archivco

$fileName = dirname(dirname(__DIR__)).'/entities/'.$entityName.'.php';

$nFile = fopen($fileName, "w") ;

// Se inserta el template dentro del archivo

$content = "<?php
class $entityName extends Entity{
   
    protected \$attributes = [
        [
            'attribute'=>'id',
            'type'=>ID_INSERT_TABLE
        ],
        [
            'attribute'=>'',
            'type'=>''
        ],
    ];
}
?>
";


fwrite($nFile,$content);

//Se cierra el archivo
fclose($nFile);

echo "\n";
echo "Se creo la clase $entityName en la locación $fileName";
echo "\n\n";
?>