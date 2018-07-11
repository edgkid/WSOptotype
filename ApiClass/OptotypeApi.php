<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'DataBaseClass/OptotypeDb.php';
/**
 * Description of OptotypeApi
 *
 * @author Edgar
 */
class OptotypeApi {
    //put your code here
    
    Public function API(){
        
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            $this->getOptotypes();
            break;     
        case 'POST'://inserta
            //$this->getOptotypesAnswer();
            break;                
        case 'PUT'://actualiza
            $this->putOptotypes();
            break;      
        case 'DELETE'://elimina
            echo'delete';
            break;
        default://metodo NO soportado
            echo 'METODO NO SOPORTADO';
            break;
        }
    }
    
     /**
    * función que segun el valor de "action" e "id":
    *  - mostrara una array con todos los registros de personas
    *  - mostrara un solo registro 
    *  - mostrara un array vacio
    */
    function getOptotypes(){
       if ($_GET['action'] == 'optotypes'){
           $db = new OptotypeDB();
           $response = $db->getOptotypes();
           echo json_encode($response,JSON_PRETTY_PRINT);
       }else{
           $this->response(400);
       }  
    }
    
    /*function getOptotypesAnswer(){
        
        if ($_GET['action'] == 'optotypes'){
               $db = new OptotypeDB();
               $response = $db->getOptotypeAnswer();
               echo json_encode($response,JSON_PRETTY_PRINT);
       }else{
           $this->response(400);
       }  
        
        
    }*/
    
    
    function putOptotypes(){
        
        if ($_GET['action'] == 'updateOptotypes'){
            $db = new OptotypeDB();
            $response = $db->putOptotypesImage();
       }else{
           $this->response(400);
       }
       
    }
    
     /**
    * Respuesta al cliente
    * @param int $code Codigo de respuesta HTTP
    * @param String $status indica el estado de la respuesta puede ser "success" o "error"
    * @param String $message Descripcion de lo ocurrido
    */
    function response($code=200, $status="", $message="") {
       http_response_code($code);
       if( !empty($status) && !empty($message) ){
           $response = array("status" => $status ,"message"=>$message);  
           echo json_encode($response,JSON_PRETTY_PRINT);    
       }   
    }  
    
}
