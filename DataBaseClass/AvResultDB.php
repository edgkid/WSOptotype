<?php
require_once 'DataBaseClass/PgDataBase.php';
/**
 * Description of AvResultDB
 *
 * @author Edgar
 */
class AvResultDB extends PgDataBase {
    
    function __construct() {
        parent::__construct();
    }
    
    public function getResultsForToday (){
        
        $data = array();
        $query = "  SELECT distinct (pat.idPatient) as idPatient,avr.idAvResult as idAvResult, mea.appointmentDate as lastDate,". 
                 "                   avr.eyeRight as AvRight, avr.eyeLeft as AvLeft, dia.description as Description,". 
                 "           sub.center as Center, sub.sustain as Sustain, sub.maintain as Maintain".
                 "  FROM Patient pat, Medical_Appointment mea, TEST tes,". 
                 "             Optotype_TEST opt, TEST_BY_SUMMARY tbs, SUMMARY_TEST sut, AV_RESULT avr,". 
                 "         DIAGNOSTIC_RESULT dia, SUBJECTIVE_TEST sub".
                 "  WHERE pat.idPatient = mea.fk_idPatient".
                 "           AND mea.idAppointment = tes.fk_idAppointment".
                 "       AND tes.idTest = opt.fk_idTest".
                 "       AND opt.idOptotypeTest = tbs.fk_idOptotypeTest".
                 "           AND sut.idSummary = tbs.fk_idSummary".
                 "       AND sut.idSummary = avr.fk_idSummary".
                 "       AND avr.idAvResult = dia.fk_idAvResult".
                 "       AND sub.idSubjective = dia.fk_idSubjective".
                 "       AND pat.idPatient IN(	SELECT pa.idPatient".
                 "                               FROM Patient pa, Medical_Appointment mea".
                 "                               WHERE pa.idPatient = mea.fk_idPatient".
                 "                                              AND pa.idPatient = pat.idPatient".
                 "                                      AND to_char(mea.appointmentdate, 'dd/mm/yyyy') != to_char((Current_Timestamp :: date), 'dd/mm/yyyy')".
                 "                               ORDER BY pat.idPatient DESC".
                 "                                   LIMIT 1)";
        
        $avResult = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($line = pg_fetch_array($avResult, null, PGSQL_ASSOC)) {
            //$data[] = $line;
            $data []= array('idPatient'=>$line['idpatient'],'idAvResult'=>$line['idavresult'],'lastDate'=>$line['lastdate'],'avRight'=>$line['avright'],'avLeft'=>$line['avleft'],'description'=>$line['description'],'center'=>$line['center'],'sustain'=>$line['sustain'],'maintain'=>$line['maintain']);
        }
        
        return $data;
          
    }
    
}
