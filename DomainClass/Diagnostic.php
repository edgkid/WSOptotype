<?php
/**
 * Description of Diagnostic
 *
 * @author Edgar
 */
class Diagnostic {
    
    private $idPatient;
    private $idSignalDefect;
    private $idSubjectiveTest;
    private $idObjectiveTest;
    private $idAntecedent;
    private $idOtherTest;
    
    function __construct() {
        $this->idPatient = 0;
        $this->idSignalDefect = 0;
        $this->idSubjectiveTest = 0;
        $this->idObjectiveTest = 0;
        $this->idAntecedent = 0;
        $this->idOtherTest = 0;
    }

    
    function getIdPatient() {
        return $this->idPatient;
    }

    function setIdPatient($idPatient) {
        $this->idPatient = $idPatient;
    }

        
    function getIdSignalDefect() {
        return $this->idSignalDefect;
    }

    function getIdSubjectiveTest() {
        return $this->idSubjectiveTest;
    }

    function getIdObjectiveTest() {
        return $this->idObjectiveTest;
    }

    function getIdAntecedent() {
        return $this->idAntecedent;
    }

    function getIdAvResult() {
        return $this->idAvResult;
    }

    function setIdSignalDefect($idSignalDefect) {
        $this->idSignalDefect = $idSignalDefect;
    }

    function setIdSubjectiveTest($idSubjectiveTest) {
        $this->idSubjectiveTest = $idSubjectiveTest;
    }

    function setIdObjectiveTest($idObjectiveTest) {
        $this->idObjectiveTest = $idObjectiveTest;
    }

    function setIdAntecedent($idAntecedent) {
        $this->idAntecedent = $idAntecedent;
    }

    function setIdAvResult($idAvResult) {
        $this->idAvResult = $idAvResult;
    }
    
    function getIdOtherTest() {
        return $this->idOtherTest;
    }

    function setIdOtherTest($idOtherTest) {
        $this->idOtherTest = $idOtherTest;
    }




}
