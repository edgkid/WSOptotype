<?php

require_once 'DataBaseClass/TestDB.php';
//require_once 'OptometricTest.php';

class TestApi{
    
    Public function API(){
        
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            //$this->optometricTest();
            echo 'get';
            break;     
        case 'POST'://inserta
            //$this->saveTest();
            $this->RequestTest();
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
    
    public function RequestTest(){
        
        if ($_GET['action'] == 'test'){
            
            $obj = json_decode( file_get_contents('php://input') );   
            $objArr = (array)$obj;
            
            if (!empty($objArr)){
                $db = new TestDB();
                $response = $db->processDataMedicalTest($obj);
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
    
    /**
     * 
     */
    /*function optometricTest(){
        
        if ($_GET['action'] == 'test'){
           
           $obj = json_decode( file_get_contents('php://input') );   
           $objArr = (array)$obj;
           $this->newTest($objArr, "L");
       }else{
           $this->response(400);
       }
        
    }
    
    function newTest($objArr, $eye){
        
        $pixelArray = array();
        
        $optometricCard = new OptometricTest('prueba');
        $optometricCard->setDistance($objArr['distance']);
        $optometricCard->setTestCode($eye);
        $pixelArray = $optometricCard->findInteractionData($objArr['patientId']);
        $optometricCard->resizeImage($optometricCard->getHigh(),$optometricCard->getWidth(),$optometricCard->getTestCode());
        $optometricCard->newOptometricCard($optometricCard->getInteraction(),$optometricCard->getTestCode(), $optometricCard->getWidth(), $optometricCard->getHigh(), $pixelArray);
        
    }*/
    
    
}
