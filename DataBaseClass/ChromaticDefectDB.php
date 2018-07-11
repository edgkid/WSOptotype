<?php
require_once 'DataBaseClass/PgDataBase.php';
/**
 * Description of ChromaticDefectDB
 *
 * @author Edgar
 */
class ChromaticDefectDB extends PgDataBase {
    
    public function getChromaticDefect (){
        
        $data = array();
        $query = " SELECT idDefect, name FROM EYE_DEFECT";
        $users = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($line = pg_fetch_array($users, null, PGSQL_ASSOC)) {
            $data []= array('idDefect'=>$line['iddefect'],'name'=>$line['name']);
         }   

        return $data; 
    }
    
    
}
