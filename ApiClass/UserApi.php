<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'DataBaseClass/UserDb.php';
/**
 * Description of UserApi
 *
 * @author Edgar
 */
class UserApi {
    
    Public function API(){
        
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            $this->getUsers();
            break;     
        case 'POST'://inserta
            echo'post';
            break;                
        case 'PUT'://actualiza
            echo'put';
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
    function getUsers(){
       if ($_GET['action'] == 'users'){
           $db = new UserDB();
           if(isset($_GET['id'])){
               $response = $db->getUserById($_GET['id']);
               echo json_encode($response,JSON_PRETTY_PRINT);
           }elseif (isset($_GET['userName']) && isset($_GET['userPassword'])) {
                $response = $db->getUserByLogin($_GET['userName'],$_GET['userPassword']);
                echo json_encode($response,JSON_PRETTY_PRINT);
            }else{
               $response = $db->getUsers();
               echo json_encode($response,JSON_PRETTY_PRINT);
           }
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
