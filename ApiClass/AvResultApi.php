<?php

require_once 'DataBaseClass/AvResultDB.php';
/**
 * Description of AvResultApi
 *
 * @author Edgar
 */
class AvResultApi {
    
    Public function API(){
        
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            $this->getAvResults();
            break;     
        case 'POST'://inserta
            echo 'POST';
            break;                
        case 'PUT'://actualiza
            echo 'put';
            break;      
        case 'DELETE'://elimina
            echo 'delete';
            break;
        default://metodo NO soportado
            echo 'METODO NO SOPORTADO';
            break;
        }
    }
    
    function getAvResults (){
       
        if ($_GET['action'] == 'avResult'){
           $db = new AvResultDB();
           if(isset($_GET['id'])){
               echo json_encode($response,JSON_PRETTY_PRINT);
            }else{
               $response = $db->getResultsForToday();
               echo json_encode($response,JSON_PRETTY_PRINT);
           }
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
