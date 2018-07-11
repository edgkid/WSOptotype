<?php

require_once 'DataBaseClass/PgDataBase.php';

require_once 'DomainClass/ParameterForCard.php';
require_once 'DomainClass/PrintSize.php';
require_once 'DomainClass/ImageSize.php';
require_once 'DomainClass/Canvas.php';
require_once 'DomainClass/CardConstructor.php';


class TestDB extends PgDataBase{
    
    function __construct() {
        parent::__construct();
    }
    
    function processDataMedicalTest(array $obj){
        
        $response = "";
        
        switch ($obj[0]->action){
            
            case '0':
                $this->save($obj);
                break;
            
            case '1':
                echo "Consultar datos de interacción";
                break;
            
            case '2':
                echo "Actualizar datos de interacción";
                break;
            
            case '3':
                echo 'eliminar datos de interacion';
                break;
            
            case '4':
                $response = $this->newTest($obj);
                break;
        }
        
        return $response;
        
    }
    
    public function save (array $obj){
        
        $query="";
        $idPatient = "";
        $idTest = "";
        
        foreach ($obj as $value){
         
            $idPatient = $this->getFkAppointmentForTest($value->idPatient);
            $query = "INSERT INTO TEST (testCode,eye,fk_idappointment) VALUES (";
            $query = $query."'".$value->testCode."','".$value->eye."',".$idPatient.")";
            
            $result = pg_query($query);
                if($result){
                    
                    $this->sendCommit();
                    $idTest = $this->getIdTestOptotype();
                    echo 'exito al guardar Test '.$value->testCode." ".$idTest."\n";
                    $this->saveOptotpeTest($value->idOptotype, $idTest);
                }
                else 
                    echo 'fallo al guardar Test'.$value->testCode."\n";
        }
    }
    
    private function saveOptotpeTest($idOptotype, $idTest){
        
        $query = "INSERT INTO OPTOTYPE_TEST (status, fk_idoptotype, fk_idtest) VALUES (";
        $query = $query."'N',".$idOptotype.",".$idTest.")";
        
        $result = pg_query($query);
                if($result)
                    echo 'exito al guardar optotipo'.$idOptotype."\n";
                else 
                    echo 'fallo al guardar optotipo'.$idOptotype."\n";
       
    }
    
    private function getIdTestOptotype (){
        
        $value= "";
        $query="SELECT MAX(idTest) FROM TEST";
        
        $result = pg_query($query);
        
        if ($row = pg_fetch_row($result)) {
           
            $value = $row[0]; 
        }
        
        if ($value == null || $value == 0)
            $value = 1;
   
        return $value;
    }
    
    private function sendCommit (){
        
        $query = "COMMIT; ";
        $result = pg_query($query);
        if($result)
            echo 'commit\n';
        
    }
    
    private function getFkAppointmentForTest($idPatient){
           
        $value = 0;
        
        $query = " SELECT idAppointment ".
                 " FROM Medical_Appointment ma ".
                 " WHERE ma.fk_idPatient = ".$idPatient.
                 "      AND ma.appointmentdate = to_date(".
                 "         (SELECT COALESCE((EXTRACT(day FROM  current_timestamp))".  
                 "          || '/'|| (EXTRACT(month FROM  current_timestamp))|| '/'||". 
                 "          (EXTRACT(year FROM  current_timestamp)))".
                 "           FROM current_timestamp), 'dd/mm/yyyy')";
        
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        if ($row = pg_fetch_row($result)) {
           
            $value = $row[0]; 
        }
            
        return $value;
    }
    
    private function saveNewTest($testCode){
        
        $directory="src/OptometricCard";
        $path = ""; 
        $count = 0;
        
        $files = opendir($directory);
        
        while ($file = readdir($files)){
    
            if ($count >1){
                $path = $directory."/".$file;
                $name = explode(".", $file);
                $bytesFile = file_get_contents($path);
                $bytesFile = pg_escape_bytea($bytesFile);
                if ($testCode == $name[0])
                {
                    $query = "INSERT INTO SUMMARY_TEST (summarycode, imagetest) VALUES ("."'";
                    $query = $query.$testCode."','".$bytesFile."'); commit;";
                    
                    $result = pg_query($query);
                    break;
                }
            }
            $count ++;
        }
        
    }
    
    private function saveOptotypeByNewTest($testCode){
        
       $query = "";
       $optometricTest = $this->getNewTest($testCode);
       $optotypes = $this->getOptotypes($testCode);
       
       foreach ($optotypes as $value){

            $query = $query." INSERT INTO TEST_BY_SUMMARY (fk_idSummary, fk_idOptotypeTest) VALUES (";
            $query = $query.$optometricTest.",".$value."); ";
       }
       
       $query = $query ." commit;";
       
       $result = pg_query($query);
        
    }
    
    private function getNewTest($testCode){
        
        $value = "";
        $query =    "   SELECT idSummary".
                    "   FROM Summary_Test".
                    "   WHERE summaryCode = '".$testCode."'";
        
        $result = pg_query($query) or die('La consulta en getNewTest fallo: ' . pg_last_error());
       
        if ($row = pg_fetch_row($result)) {
           
            $value = $row[0]; 
        }
            
        return $value;
        
    }
    
    private function getOptotypes ($testCode){
        
        $data = array();
        $count = 0;
        $query =    " SELECT idOptotypetest".
                    " FROM Test, optotype_test, optotype". 
                    " WHERE testCode = '".$testCode."'".
                    "       AND fk_idTest = idTest".
                    "       AND fk_idOptotype = idOptotype";
        
        $result = pg_query($query) or die('La consulta en getOptotypes fallo: ' . pg_last_error());

        while ($row = pg_fetch_row($result)) {
           
            $data[$count] = $row[0];
            $count ++;
        }
            
        return $data;
    }
    
    function getSummaryTestByCode($testCode){
        
        $data = array();
        $dataTest = array();
        $directory = "src/rowsBase";
        $path = ""; 
        $count = 0;
        $row = 1;
        
        $query ="   SELECT idsummary, summaryCode, imagetest".
                "   FROM summary_test".
                "   WHERE summaryCode = '".$testCode."'";
        
        $patient = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

        while ($line = pg_fetch_array($patient, null, PGSQL_ASSOC)) {
            $data []= array('idSummary'=>$line['idsummary'],'summaryCode'=>$line['summarycode'],'imageTest'=> base64_encode(pg_unescape_bytea($line['imagetest'])));
        }
        
        $files = opendir($directory);
        while ($file = readdir($files)){
    
            if ($count >1){
                
                $path = $directory."/".$file;
                $name = explode(".", $file);
                $bytesFile = file_get_contents($path);
                $data []= array('idSummary'=>0,'summaryCode'=>$testCode."_".$row,'imageTest'=> base64_encode($bytesFile));
                $row++;
                
            }
            $count ++;
            
        }
        
        $dataTest[0] = $data[0];
        $dataTest[1] = $data[1];
        $dataTest[2] = $data[4];
        $dataTest[3] = $data[5];
        $dataTest[4] = $data[6];
        $dataTest[5] = $data[7];
        $dataTest[6] = $data[8];
        $dataTest[7] = $data[9];
        $dataTest[8] = $data[10];
        $dataTest[9] = $data[11];
        $dataTest[10] = $data[2];
        $dataTest[11] = $data[3];
        
        return $dataTest;
      
    }
    
    private function newTest($objArr){
        
        $avMin = 0;
        $avGrade = 0;
        $avRadians = 0;
        $e = 0;
        $h = 0;
        $position = 0;
        $nextElement = 0;
        $hPixel = 0;
        $x = 0;
        $y = 0;
        $xPixel = 0;
        $yPixel = 0;
        
        $avList = array();
        $avPixels = array();
        $avPrints = array();
        $avElements = array();
        
        $parameters = new ParameterForCard($objArr[0]->distance);
        $printSize = new PrintSize();
        $imageSize = new ImageSize($parameters->getCalculatePPP());
        $avList = $parameters->getAvList();

        $sizeArray = count($avList);
        
        //// defino el tamañod elos elementos para cada renglon
        while ($position <= $sizeArray ){
            
            if($position == $sizeArray){
              $avMin = $printSize->getAvMinute($avList[$position - 1]);  
            }else{
               $avMin = $printSize->getAvMinute($avList[$position]); 
            }
            
            $avRadians = $printSize->getAvRadian($avGrade);
            $e = $printSize->getSizeMinDetailmm($avRadians, $parameters->getDistance());
            $avGrade = $printSize->getAvGrade($avMin);
            $h = $printSize->getSizeElement($e);
            $h = $printSize->getSizeElementCm($h);
            $hPixel = $imageSize->getSizeInPixel($h);

            $avPrints[$nextElement] = $h;
            $avPixels[$nextElement] = $hPixel;

            $position ++;
            $nextElement++;
         }  
         
         ///// Defino alto y ancho del lienzo que sera la carta
        $y = $printSize->getCanvasHight($avPrints);
        $x = $printSize->getCanvasWith($avPrints);
        $yPixel = $imageSize->getSizeInPixel($y);
        $xPixel = $imageSize->getSizeInPixel($x) + 100;
        
        ///// voy a crear el lienzo base para la carta

        //// aqui debo obtener un testCode
        $testCode = $this->getNameNewTest($objArr[0]->patientId);
        $canvas = new Canvas($xPixel, $yPixel, $avPixels, $testCode);
        $canvas->newCanvasImage();
        $canvas->rowsForCanvas();
        
        ///Aqui debo obtener los elementos de interacción, habilito el atributo y lo paso por constructor
        $avElements = $this->getElementsInteraction($objArr[0]->patientId, $testCode);
        
        $cardConstructor = new CardConstructor($avPixels, $objArr[0]->distance, $testCode, $avElements);
        $cardConstructor->fillCanvasRows();
        //$cardConstructor->fillOptometricCard();
        
        if (!$this->getSumaryCode($testCode)){
            $this->saveNewTest($testCode);
            $this->saveOptotypeByNewTest($testCode);
        }

        $response = $this->getSummaryTestByCode($testCode);
        // DElete elementos en la carpetas optometric y rows
     
        $this->deleteImageElementOnServer("src/OptometricCard");
        $this->deleteImageElementOnServer("src/rowsBase");
        
        return $response;
    }
    
    
    private function getSumaryCode ($testCode){
        
        $value = false;
        $query = "  Select summaryCode". 
                    "   from Summary_Test".
                    "   where summaryCode = '".$testCode."'";  
        
        $db = new PgDataBase();
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        if ($row = pg_fetch_row($result)) {
            $testCode = $row[0];
            $value = true;
        }
        
        return $value;        
    }
    
    
    function  getNameNewTest ($patientId){
        
        $date = getdate();
        $testCode = "";
        $today = "'".$date['mday']."/".$date['mon']."/".$date['year']."'";

        $query = "  SELECT DISTINCT(te.testCode) ". 
                    " FROM Patient pa, Medical_appointment ma, Test te, Optotype_Test ot, Optotype op ".
                    " WHERE pa.idPatient = ma.fk_idPatient ".
                        " AND ma.idAppointment = te.fk_idAppointment ".
                        " AND te.idTest = ot.fk_idTest ".
                        " AND ot.fk_idOptotype = op.idOptotype ".
                        " AND pa.idPatient = ".$patientId;
                        " AND ma.appointmentdate = to_date(".$today.",'dd/mm/yyyy')";  
        
        $db = new PgDataBase();
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        if ($row = pg_fetch_row($result)) {
            $testCode = $row[0];
        }
        
        return $testCode;
     
    }
    
    function getElementsInteraction ($patientId, $testCode){
        
        $elements = array();
        $date = getdate();
        $position = 0;
        $today = "'".$date['mday']."/".$date['mon']."/".$date['year']."'";
        
        $query = "  SELECT op.optotypeCode". 
                    " FROM Patient pa, Medical_appointment ma, Test te, Optotype_Test ot, Optotype op ".
                    " WHERE pa.idPatient = ma.fk_idPatient ".
                        " AND ma.idAppointment = te.fk_idAppointment ".
                        " AND te.idTest = ot.fk_idTest ".
                        " AND ot.fk_idOptotype = op.idOptotype ".
                        " AND pa.idPatient = ".$patientId.
                        " AND ma.appointmentdate = to_date(".$today.",'dd/mm/yyyy')".
                        " AND te.testCode LIKE '%".$testCode."%'"; 
       
        $db = new PgDataBase();
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
        while ($row = pg_fetch_row($result)) {
            $elements [$position] = $row [0];
            $position ++;
         }   
        
        return $elements;
         
    }
    
    
    function deleteImageElementOnServer ($directory){
       
        $count = 0;
        $files = opendir($directory);
        
        while ($file = readdir($files)){
    
            if ($count >1){
                
                $path = $directory ."/".$file;
                unlink($path);
                
            }
            $count ++;      
        }
        
    }

}