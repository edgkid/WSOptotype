<?php

require_once 'DataBaseClass/PgDataBase.php';

class AppointmentDB extends PgDataBase{
    
    function __construct() {
        parent::__construct();
    }
    
    function actionAppointment (array $obj){
        
        $value = false;
        
        switch ($obj[0]->action){
            
            case '0':
                $this->save($obj);
                break;
            
            case '1':
                $this->update($obj);
                break;
            
            case '2':
                $value = $this->delete($obj);
                break;
            
        }
        
        return $value;
        
    }
    
    function delete(array $obj){
        
        $value = false;
        $query = "DELETE FROM Medical_Appointment WHERE fk_idPatient = ".$obj[0]->idPatient;
        $query = $query." AND status = '".$obj[0]->status."'";
        
        $result = pg_query($query);
        
        if ($result)
            $value = true;
        
        return $value;
    }
    
    function update (array $obj){
        
        $value = false;
        
        $query = "UPDATE Medical_Appointment SET appointmentdate = '".$obj[0]->appointmentDate."'";
        $query = $query." WHERE fk_idPatient = ".$obj[0]->idPatient;
        $query = $query." AND status = '".$obj[0]->status."'";
        
        $result = pg_query($query);
        
        if ($result)
            $value = true;
        
        return $value;
        
    }
    
    function save (array $obj){
        
        $value = false;
        
        $query = "INSERT INTO Medical_Appointment (idappointment,appointmentDate, status, fk_IdPatient)";
        $query = $query." VALUES (".$this->getNewId().",'".$obj[0]->appointmentDate."','".$obj[0]->status."',";
        $query = $query.$obj[0]->idPatient."); commit;";
        
        echo $query;
        
        $result = pg_query($query);
        
        if ($result)
            $value = true;
        
        return $value;
      
    }
    
    function getNewId (){
        
        $query ="Select (Max(idAppointment) + 1) from Medical_Appointment";
        $result = pg_query($query);
        
        if ($row = pg_fetch_row($result)) {
            $value = $row[0]; 
        }
        return $value;
    }
    
}
