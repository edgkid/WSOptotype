<?php
require_once 'DataBaseClass/PgDataBase.php';
/**
 * Description of OpticalDefectDB
 *
 * @author Edgar
 */
class OpticalDefectDB extends PgDataBase{
    
    public function getOpticalDefect (){
        
        $data = array();
        $query = " SELECT idSignal, name FROM SIGNAL_DEFECT ";
        $users = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($line = pg_fetch_array($users, null, PGSQL_ASSOC)) {
            $data []= array('idSignal'=>$line['idsignal'],'name'=>$line['name']);
         }   

        return $data; 
    }
    
}
