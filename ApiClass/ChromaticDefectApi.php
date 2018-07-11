<?php
require_once 'DataBaseClass/ChromaticDefectDB.php';
/**
 * Description of ChromaticDefect
 *
 * @author Edgar
 */
class ChromaticDefectApi {
    
    Public function API(){
        
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            $this->getDefect();
            break;     
        case 'POST'://inserta
            echo 'post';
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
    
    function getDefect(){
       if ($_GET['action'] == 'antecedent'){
           $db = new ChromaticDefectDB();
           $response = $db->getChromaticDefect();
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
