<?php

//require_once 'PgDataBase.php';
require_once 'DomainClass/Diagnostic.php';
/**
 * Description of DiagnosticDB
 *
 * @author Edgar
 */
class DiagnosticDB extends PgDataBase {
    
    
    public function proccessDataDiagnostic(array $obj){
        
        //$responce = "listo para procesar datos";
        
        switch ($obj[0]->action){
            
            case '0':
                //echo "salvar datos";
                $this->saveDataDiagnostic($obj);
                break;
            
            case '1':
                //echo "Consultar datos de interacción";
                $response = $this->readDiagnostic($obj);
                break;
            
            case '2':
                echo "Actualizar datos de interacción";
                break;
            
            case '3':
                echo 'eliminar datos de interacion';
                break;
  
        }
        
        return $response;
    }
    
    
    private function readDiagnostic(array $obj){
        
        $data = array();
        $query = "( SELECT avr.eyeRight AS eyeRight , avr.eyeLeft AS eyeLeft, sut.Center AS center, sut.Sustain AS sustain, ". 
                    "    sut.Maintain AS maintain, dir.typeTest as test, to_char(mea.appointmentDate, ". 
                    "    'dd/mm/yyyy') AS appointmentDate, pat.sex AS sex ". 
                    " FROM Patient pat, Medical_Appointment mea,Diagnostic_result dir, ". 
                        " Subjective_Test sut, Av_Result avr ".
                    " WHERE pat.idPatient = mea.fk_idPatient ".
                    "    AND mea.idAppointment = dir.fk_Appointment ".
                    "    AND sut.idSubjective = dir.fk_idSubjective ". 
                    "    AND avr.idAvResult = dir.fk_idAvResult ".
                    "    AND dir.typeTest = 'Test Estandar' ".
                    "    AND pat.idPatient = ".$obj[0]->idPatient.") ".
                " UNION ".
                "( SELECT avr.eyeRight AS eyeRight , avr.eyeLeft AS eyeLeft, sut.Center AS center, sut.Sustain AS sustain, ". 
                    "    sut.Maintain AS maintain, dir.typeTest as test, to_char(mea.appointmentDate, ".
                    "    'dd/mm/yyyy') AS appointmentDate, pat.sex AS sex ".
                    " FROM Patient pat, Medical_Appointment mea,Diagnostic_result dir, ". 
                    "    Subjective_Test sut, Av_Result avr ".
                    " WHERE pat.idPatient = mea.fk_idPatient ".
                    "    AND mea.idAppointment = dir.fk_Appointment ".
                    "    AND sut.idSubjective = dir.fk_idSubjective ".
                    "    AND avr.idAvResult = dir.fk_idAvResult ".
                    "    AND dir.typeTest = 'Test Personalizado' ".
                    "    AND pat.idPatient = ".$obj[0]->idPatient.")";
        
        $dataAppointment = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($line = pg_fetch_array($dataAppointment, null, PGSQL_ASSOC)) {
            $data []= array('eyeRight'=>$line['eyeright'],'eyeleft'=>$line['eyeleft'],'center'=>$line['center'],'sustain'=>$line['sustain'],'maintain'=>$line['maintain'], 'appointmentdate'=>$line['appointmentdate'], 'gender'=>$line['sex'], 'typeTest'=> $line['test']);
         }   
            
     
        return $data; 
        
    }
    
    
    private function saveDataDiagnostic(array $obj){

        $diagnostic = new Diagnostic();
        $diagnostic->setIdPatient($obj[0]->idPatient);
        
        if ($obj[0]->signalDefect != ""){
            $this->saveDataDiagnosticSignalRegister($diagnostic, $obj);
            $this->saveDataDiagnosticSignalPatient($diagnostic, $obj);
        }
        
        if ($obj[0]->antecedentMon != ""){
           $this->saveDataDiagnosticAntecedentRegister($diagnostic, $obj); 
           $this->saveDataDiagnosticAntecedentRoll($diagnostic, $obj, $obj[0]->antacedentDad, 'M');
        }
        
        if ($obj[0]->antacedentDad){
          $this->saveDataDiagnosticAntecedentRegister($diagnostic, $obj);
          $this->saveDataDiagnosticAntecedentRoll($diagnostic, $obj, $obj[0]->antecedentMon, 'F');
        }        
        
        $this->saveDataDiagnosticSubjectiveTest($diagnostic, $obj);
        $this->saveDataDiagnosticObjectiveTets($diagnostic, $obj);
        $this->saveDataDiagnosticResult($diagnostic, $obj);
        $this->saveDataDiagnosticOnRepository($diagnostic, $obj);
    }
    
    private function getAId ($query, $tableNanme, $whereClausule){
        $id = 0;
        
        $query = $query.$tableNanme.$whereClausule;
        
        $result = pg_query($query);
        
        if ($row = pg_fetch_row($result)) {
           
            $id = $row[0]; 
        }
        
        return $id;
        
    }
    
    private function getSomeId ($query, $tableNanme, $whereClausule){
        
        $someId = array();
        $position = 0;
        $query = $query.$tableNanme.$whereClausule;
        
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($row = pg_fetch_row($result)) {
           
            $someId[$position] = $row[0];
            $position ++;
        }
        
        return $someId;
        
    }
    
    private function saveDataDiagnosticSignalRegister (Diagnostic $diagnostic, array $obj){
        
        $fk = (int) $obj[0]->idPatient;
        $idTable = "idSignalRegister";
        $whereClausule = " ";
        $query = " INSERT INTO Signal_BY_REGISTER (fk_idPatient) VALUES (".$fk.");";            
        $query = $query." commit;";
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
        if ($result){
            $query = " Select max (".$idTable.") from ";
            $tableName = "Signal_BY_REGISTER";
            $diagnostic->setIdSignalDefect($this->getAId($query, $tableName, $whereClausule));
        }
            
    }
    
    private function saveDataDiagnosticSignalPatient (Diagnostic $diagnostic, array $obj){
        
        $someId = array();
        $arraySignal = split(',', $obj[0]->signalDefect);
        $tableName = " SIGNAL_DEFECT sd ";
        $query = " SELECT sd.idSignal FROM ";
        $whereClausule = " WHERE";
        
        for ($position = 0; $position < count($arraySignal); $position ++){
         
            $whereClausule = $whereClausule." sd.name like ('%".$arraySignal[$position]."%') ";
            $whereClausule = $whereClausule." OR ";
        }
        
        $whereClausule = substr($whereClausule, 0, -5).";";
        
        $someId = $this->getSomeId($query, $tableName, $whereClausule);

        $query = "";
        for ($position = 0; $position < count($someId); $position ++){
            
            $query = " INSERT INTO SIGNAL_DEFECT_PATIENT (fk_idSignal, fk_idregisterSignal) VALUES (";
            $query = $query.$someId[$position].",".$diagnostic->getIdSignalDefect()."); ";
            $query = $query." commit;";
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        }   
    }
    
    private function saveDataDiagnosticAntecedentRegister(Diagnostic $diagnostic, array $obj){
        
        $fk = (int) $obj[0]->idPatient;
        $idTable = "idByRegister";
        $whereClausule = " ";
        $query = " INSERT INTO ANTECENDENT_BY_REGISTER (fk_idPatient) VALUES (".$fk.");";            
        $query = $query." commit;";
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
        if ($result){
            $query = " Select max (".$idTable.") from ";
            $tableName = " ANTECENDENT_BY_REGISTER ";
            $diagnostic->setIdAntecedent($this->getAId($query, $tableName, $whereClausule));
        }
        
    }
    
    private function saveDataDiagnosticAntecedentRoll (Diagnostic $diagnostic, array $obj, $antecedent, $roll){
        
        $someId = array();
        $arrayAntecedent = split(',', $antecedent);
        $tableName = " EYE_DEFECT ed ";
        $query = " SELECT ed.idDefect FROM ";
        $whereClausule = " WHERE";
        
        for ($position = 0; $position < count($arrayAntecedent); $position ++){
         
            $whereClausule = $whereClausule." ed.name like ('%".$arrayAntecedent[$position]."%') ";
            $whereClausule = $whereClausule." OR ";
        }
        
        $whereClausule = substr($whereClausule, 0, -5).";";
        
        $someId = $this->getSomeId($query, $tableName, $whereClausule);

        for ($position = 0; $position < count($someId); $position ++){
            
            $query = " INSERT INTO ANTECENDENT_ROLL (roll, fk_idEyeDefect, fk_idByRegister) VALUES (";
            $query = $query."'".$roll."',".$someId[$position].",".$diagnostic->getIdAntecedent()."); ";
            $query = $query." commit;";           
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
        }
        
    }
    
    private function saveDataDiagnosticSubjectiveTest (Diagnostic $diagnostic, array $obj){
        
        $idTable = "idSubjective";
        $whereClausule = " ";
        $query = " INSERT INTO SUBJECTIVE_TEST (center, SUSTAIN, MAINTAIN) VALUES ('".$obj[0]->center."',";
        $query = $query."'".$obj[0]->sustain."','".$obj[0]->maintain."'); ";
        $query = $query." commit;";

        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
        if ($result){
            $query = " Select max (".$idTable.") from ";
            $tableName = " Subjective_test ";
            $diagnostic->setIdSubjectiveTest($this->getAId($query, $tableName, $whereClausule));
        }  
        
    }
    
    private function saveDataDiagnosticObjectiveTets (Diagnostic $diagnostic, array $obj){
        
        $idTable = "idAvResult";
        $whereClausule = " ";
        $query = "INSERT INTO AV_RESULT (eyeRight,eyeLeft) VALUES (";
        $query = $query.$obj[0]->avRigth.",".$obj[0]->avLeft."); commit;";
        
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
        if ($result){
            $query = " Select max (".$idTable.") from ";
            $tableName = " AV_RESULT ";
            $diagnostic->setIdObjectiveTest($this->getAId($query, $tableName, $whereClausule));
        }  
        
    }
    
    private function saveDataDiagnosticResult (Diagnostic $diagnostic, array $obj){
        
        $appointment = "(   SELECT idAppointment 
                            FROM Medical_Appointment
                            WHERE to_char(appointmentDate, 'dd/mm/yyyy') = to_char((Current_Timestamp :: date), 'dd/mm/yyyy')
                                    AND fk_idPatient = ".$diagnostic->getIdPatient()." )";
        
        if ($diagnostic->getIdAntecedent() == 0){
            $diagnostic->setIdAntecedent('null');
        }
        if ($diagnostic->getIdSignalDefect() == 0){
            $diagnostic->setIdSignalDefect('null');
        }
        
        $query = "INSERT INTO DIAGNOSTIC_RESULT (typeTest,colaboration,";
        $query = $query."fk_idSubjective,fk_idAvResult,fk_antecendent,fk_Signal,fk_Appointment) VALUES (";
        $query = $query."'".$obj[0]->typeTest."','".$obj[0]->colaboratedGrade."',";
        $query = $query.$diagnostic->getIdSubjectiveTest().",".$diagnostic->getIdObjectiveTest().",";
        $query = $query.$diagnostic->getIdAntecedent().",".$diagnostic->getIdSignalDefect().",".$appointment."); commit;";
        
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
    }
    
    private function saveDataDiagnosticOnRepository (Diagnostic $diagnostic, array $obj){
        
        $arrayMon = array();
        $arrayDad = array();
        $arraySIgnal = array();
        $arrayAntecedent = array();
        
        if($obj[0]->antecedentMon != ""){
           $arrayMon = split(',',$obj[0]->antecedentMon); 
        }
        if ($obj[0]->antacedentDad != ""){
            $arrayDad = split(',', $obj[0]->antacedentDad);
        }
        if($obj[0]->signalDefect != ""){
           $arraySIgnal = split(',', $obj[0]->signalDefect); 
        }
        
        $arrayAntecedent = array_merge($arrayMon, $arrayDad);
        $arrayAntecedent = array_unique($arrayAntecedent);
        $arraySIgnal = array_unique($arraySIgnal);

        if (count($arrayAntecedent) > 0){
            if(count($arraySIgnal) > 0){
                for($x = 0; $x <count($arrayAntecedent); $x++){
                    for($y = 0; $y <count($arraySIgnal); $y++){
                        $this->insertInRepository($diagnostic, $obj,$arraySIgnal[$y],$arrayAntecedent[$x]);
                    }
                }
            }else{
                for ($x = 0; $x <count($arrayAntecedent); $x++){
                    $this->insertInRepository($diagnostic, $obj,null,$arrayAntecedent[$x]);
                }
            }
        }elseif (count($arraySIgnal) > 0){
            for($y = 0; $y <count($arraySIgnal); $y++){
                $this->insertInRepository($diagnostic, $obj,$arraySIgnal[$y],null);
            }
        }else{
            $this->insertInRepository($diagnostic, $obj,null,null);
        }
    }
    
    private function insertInRepository(Diagnostic $diagnostic, array $obj, $signal, $antecedent){
        
        $antValue = 'null';
        $sigValue = 'null';
        $today = date("d")."/".date("m")."/".date("Y");
        
        if ($signal != null){
            $idTable = ' idSignal ';
            $tableName = ' SIGNAL_DEFECT ';
            $whereClausule = '';
            $query = " SELECT MAX(".$idTable.") FROM ";
            $sigValue = $this->getAId($query, $tableName, $whereClausule);
        }
        if ($antecedent != null){
            $idTable = 'idDefect';
            $tableName = 'EYE_DEFECT';
            $whereClausule = ''; 
            $query = " SELECT MAX(".$idTable.") FROM ";
            $antValue = $this->getAId($query, $tableName, $whereClausule);
        }
        
        $query = "INSERT INTO REPOSITORY_DIAGNOSTIC (repositoryYears, repositorySex, respositoryCenter,";
        $query = $query."repositorySustain, repositorymaintain, repositoryAvRigth, repositoryAvLeft,";
        $query = $query."repositoryColaborated, repositoryTypeTest, repositoryDate, fk_defect, fk_signalDefect) ";
        $query = $query."VALUES (".$obj[0]->yearsOld.",'".$obj[0]->gender."','".$obj[0]->center."','";
        $query = $query.$obj[0]->sustain."','".$obj[0]->maintain."','".$obj[0]->avRigth."','".$obj[0]->avLeft."','";
        $query = $query.$obj[0]->colaboratedGrade."','".$obj[0]->typeTest."','".$today."',".$antValue.",";
        $query = $query.$sigValue."); commit;";
        
        $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
       
        
    }
    
    
}
