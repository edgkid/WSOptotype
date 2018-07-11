<?php

require_once 'DataBaseClass/OpticalDefectDB.php';
/**
 * Description of OpticalDefectApi
 *
 * @author Edgar
 */
class OpticalDefectApi {
    
    Public function API(){
        
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            $this->getOpticalDefect();
            break;     
        case 'POST'://inserta
            echo'post';
            break;                
        case 'PUT'://actualiza
            echo 'put';
            break;      
        case 'DELETE'://elimina
            echo'delete';
            break;
        default://metodo NO soportado
            echo 'METODO NO SOPORTADO';
            break;
        }
    }
    
    function getOpticalDefect(){
       if ($_GET['action'] == 'signalDefect'){
           $db = new OpticalDefectDB();
           $response = $db->getOpticalDefect();
           echo json_encode($response,JSON_PRETTY_PRINT);
       }else{
           $this->response(400);
       }  
    }
    
    function response($code=200, $status="", $message="") {
       http_response_code($code);
       if( !empty($status) && !empty($message) ){
           $response = array("status" => $status ,"message"=>$message);  
           echo json_encode($response,JSON_PRETTY_PRINT);    
       }   
    }  
    
}
