<?php

require_once 'DataBaseClass/AppointmentDB.php';

class AppointmentApi {
    
    Public function API(){
        
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            echo 'get';
            break;     
        case 'POST'://inserta
            //$this->deleteAppointment();
            $this->requestAppointment();
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
    
    function requestAppointment (){
        if ($_GET['action'] == 'appointment'){
            
            $obj = json_decode( file_get_contents('php://input') );   
            $objArr = (array)$obj;
            
            if (!empty($objArr)){
                $appointmentDb = new AppointmentDB();
                if ($appointmentDb->actionAppointment($obj))
                    $this->response (200);
            }
        }else{
           $this->response(400);
        } 
        
    } 
    
    /*public function deleteAppointment(){
        
        if ($_GET['action'] == 'appointment'){
            
            $obj = json_decode( file_get_contents('php://input') );   
            $objArr = (array)$obj;
            
            if (!empty($objArr)){
                $appointmentDb = new AppointmentDB();
                if ($appointmentDb->delete($objArr))
                    $this->response (200);
            }
        }else{
           $this->response(400);
        } 
    }*/  
    
    function response($code=200, $status="", $message="") {
       http_response_code($code);
       if( !empty($status) && !empty($message) ){
           $response = array("status" => $status ,"message"=>$message);  
           echo json_encode($response,JSON_PRETTY_PRINT);    
       }   
    } 
}
