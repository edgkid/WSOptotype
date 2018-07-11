<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'DataBaseClass/PgDataBase.php';
require_once 'DataBaseClass/AppointmentDB.php';
/**
 * Description of PatientDB
 *
 * @author Edgar
 */
class PatientDB extends PgDataBase {
    
    function __construct() {
        parent::__construct();
    }
    
    public function actionPatient (array $obj){
        
         $value = array();
        
        switch ($obj[0]->action){
            
            case '0':
                $this->savePatient($obj);
                break;
            
            case '1':
                echo "update";
                break;
            
            case '2':
                echo "delete";
                break;
            
            case '3':
                $value = $this->getSomePatient($obj);
                break;
        }
        
        return $value;
       
    }
    
    /**
     * This function find the Patients for today
     * @return type
     */
    public function getPatientsForToday (){
        $data = array();
        $query = "  SELECT pat.idPatient as idPatient, pat.firstName as firstName, ".
               "           pat.middleName as middleName, pat.lastName as lastName,".
               "           pat.maidenName as maidenName , pat.sex as gender, pat.birthday as birthday, ".
               "    (extract(year from  current_timestamp) - extract(year from pat.birthDay)) as yearsOld,".
               "    pat.photo as image,". 
               "    pat.fk_idUser as fkUSer,".
               "    mea.appointmentdate as nextAppointmentDate".
               "  FROM Patient pat, Medical_Appointment mea".
               "  WHERE pat.idPatient = mea.fk_idPatient".
               "        AND to_char(mea.appointmentdate, 'dd/mm/yyyy')= to_char((Current_Timestamp :: date), 'dd/mm/yyyy')";
        
        $patient = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($line = pg_fetch_array($patient, null, PGSQL_ASSOC)) {
            //$data[] = $line;
            $data []= array('idPatient'=>$line['idpatient'],'firstName'=>$line['firstname'],'middleName'=>$line['middlename'],'lastName'=>$line['lastname'],'maidenName'=>$line['maidenname'],'gender'=>$line['gender'],'birthday'=>$line['birthday'],'yearsOld'=>$line['yearsold'],'image'=> base64_encode(pg_unescape_bytea($line['image'])),'fkUSer'=>$line['fkuser'],'nextAppointmentDate'=>$line['nextappointmentdate']);
        }
        
        return $data;
    }
    
    public function getSomePatient(array $obj){
        
        $data = array();
        $query = "  SELECT pa.idPatient as idPatient, pa.firstName as firstName, pa.lastName as lastName,". 
                "       pa.middleName as middleName, pa.maidenName as maidenName,".
                "        (extract(year from  current_timestamp) - extract(year from pa.birthDay)) as yearsOld,".
                "        pa.photo as image".
                "    FROM Patient pa, Medical_Appointment ma".
                "    WHERE pa.idPatient = ma.fk_idPatient".
                "        AND (Lower(pa.firstName) LIKE Lower('%".$obj[0]->patient."%')".
                "            OR Lower(pa.LastName) LIKE Lower('%".$obj[0]->patient."%')".
                "            OR Lower(pa.MiddleName) LIKE Lower('%".$obj[0]->patient."%')".
                "            OR Lower(pa.MaidenName) LIKE Lower('%".$obj[0]->patient."%'))".
                "        AND ma.appointmentdate= (SELECT max (ma.appointmentdate)". 
                "                                FROM Medical_Appointment ma, Patient pa".
                "                                WHERE pa.idPatient = ma.fk_idPatient".
                "                			AND  (Lower(pa.firstName) LIKE Lower('%".$obj[0]->patient."%')".
                "                                            OR Lower(pa.LastName) LIKE Lower('%".$obj[0]->patient."%')".
                "                                            OR Lower(pa.MiddleName) LIKE Lower('%".$obj[0]->patient."%')".
                "                                            OR Lower(pa.MaidenName) LIKE Lower('%".$obj[0]->patient."%')))";
        
        $patient = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($line = pg_fetch_array($patient, null, PGSQL_ASSOC)) {
            $data []= array('idPatient'=>$line['idpatient'],'firstName'=>$line['firstname'],'middleName'=>$line['middlename'],'lastName'=>$line['lastname'],'maidenName'=>$line['maidenname'],'yearsOld'=>$line['yearsold'],'image'=> base64_encode(pg_unescape_bytea($line['image'])));
        }
        
        return $data;   
    }
    
    
    public function putPatientsImage (){
        
        $directory="src/kids";
        $path = ""; 
        $count = 0;
        
        
        $files = opendir($directory);
        
        while ($file = readdir($files)){
    
            if ($count >1){
                $path = $directory."/".$file;
                $name = explode(".", $file);
                $bytesFile = file_get_contents($path);
                $bytesFile = pg_escape_bytea($bytesFile);
                //echo $path."</br>";
                $query = " UPDATE patient SET photo = '".$bytesFile.
                         "' WHERE Concat(firstname,middlename,lastname,maidenname) = '".
                          $name[0]."'";
                $result = pg_query($query);
                if($result)
                    echo 'exito al actualizar'.$name[0]."</br>";
                else 
                    echo 'fallo al actualizar'.$name[0]."</br>";
            }
            $count ++;
        }
        
    } 
    
    public function savePatient(array $obj){
        
        if ($obj[0]->photo != null || $obj[0]->photo != ""){
            
            $directory="kids/";
            $fileName =$obj[0]->firstName.$obj[0]->secondName.$obj[0]->firstLastName.$obj[0]->secondLastName.".png";
            $baseDecode = base64_decode($obj[0]->photo);
            file_put_contents($fileName, $baseDecode);
            $path = $directory.$fileName;
            $bytesFile = file_put_contents($path, $baseDecode);

            $bytePhoto = file_get_contents($path);
            $bytePhoto = pg_escape_bytea($bytesFile);
            
            $bytePhoto = "'".$bytePhoto."'";
            
        }else{
            $bytePhoto = "null";
        }
        
        $idPatient = $this->getNewId();
        $query = "INSERT INTO patient (idPatient, firstName, middleName, lastName, maidenName, sex, birthday, photo, fk_idUser)".
                " VALUES (".
                $idPatient.",".
                "'".$obj[0]->firstName."',".
                "'".$obj[0]->secondName."',".
                "'".$obj[0]->firstLastName."',".
                "'".$obj[0]->secondLastName."',".
                "'".$obj[0]->gender."',".
                "'".$obj[0]->birthday."',".
                $bytePhoto.",".
                $obj[0]->fk_user.
                ")";

        $result = pg_query($query);
        
        if($result){
            if ($obj[0]->action == "0"){
                $appoitnmet = new AppointmentDB();
                $idAppointment = $appoitnmet->getNewId();
                $query = "INSERT INTO Medical_Appointment (idappointment,appointmentDate, status, fk_IdPatient)";
                $query = $query." VALUES (".$idAppointment.",'".$obj[0]->nextAppointment."','N',";
                $query = $query.$idPatient."); commit;";
                
                $result = pg_query($query);
            }
        }
           
       
    }
    
    function getNewId (){
        
        $query ="Select (Max(idPatient) + 1) from Patient";
        $result = pg_query($query);
        
        if ($row = pg_fetch_row($result)) {
            $value = $row[0]; 
        }
        return $value;
    }
    
}
