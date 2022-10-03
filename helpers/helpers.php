<?php

function textHeaders(){
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
    header("Access-Control-Allow-Origin: *");    
}

function printJSON($array){
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
    header("Access-Control-Allow-Origin: *");    
    header('Content-type:application/json; charset=utf-8');
    echo json_encode($array, JSON_UNESCAPED_UNICODE);
}

/**
 * Esta es la funcion que va a convertir la respuesta a la api en un objeto JSON
 * 1. Otorga los encabezados para que pueda ser consultada desde fuente externas
 * 2. responde un status de afirmativo o negativo a la solicitud [request]
 * 3. Agrega la data o el mensaje que se requiera comunicar a la solicitud
 * 
 * @return json
 */

function responseRequest($res,$message = null){

    if ($res) {
      $aux = ['status'=>'success'];
    }else{
      $aux = ['status'=>'failure'];
    }

    if ($message !== null) {
      $aux['message'] = $message;
    }

    printJSON($aux);
}

function JSONmessage($string){
  $auxArray = array();
  $auxArray['message'] = $string;
  printJSON($auxArray);
}

function show_controller_error($section, $controller){
  return "El controlador $controller de la seccion $section pudo ser encontrado";
}

function show_model_error($section,$model){
  return "El modelo $model de la seccion $section no pudo ser encontrado";
}

function message($obj){
  return array('message'=>$obj);
}

function validateKey(){
  if (isset($_REQUEST['APP_KEY']) && $_REQUEST['APP_KEY'] == $_ENV['APP_KEY']) {
  }else{
    die('La llave no coincide');
  }
}

function holaMundo(){
  echo "hola mundo";
}

function getCurrentDate(){
  $date = date('Y-m-d H:i:s');
  return $date;
}

?>