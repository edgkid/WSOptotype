<?php

/**
 * Description of AvStandar
 *
 * @author edgar
 */
class AvStandar {
    
    private $avStandarOneYears = Array("20/200", "20/160", "20/120","20/100", "20/80","20/60", "20/50","20/40", "20/32", "20/25", "20/20");
    private $avStandarTowYears = Array("20/80","20/60", "20/50", "20/40","20/32", "20/25", "20/20");
    private $avStandarthreeYears = Array("20/50", "20/40", "20/32", "20/25", "20/20");
    private $avStandarFourYears = Array("20/40", "20/32", "20/25", "20/20");
    private $avStandarFiveYears = Array("20/32", "20/25", "20/20");
    private $avStandarSixToMoreYears = Array("20/20");
    
    function getAvStandarOneYears() {
        return $this->avStandarOneYears;
    }

    function getAvStandarTowYears() {
        return $this->avStandarTowYears;
    }

    function getAvStandarthreeYears() {
        return $this->avStandarthreeYears;
    }

    function getAvStandarFourYears() {
        return $this->avStandarFourYears;
    }

    function getAvStandarFiveYears() {
        return $this->avStandarFiveYears;
    }

    function getAvStandarSixToMoreYears() {
        return $this->avStandarSixToMoreYears;
    }

    function setAvStandarOneYears($avStandarOneYears) {
        $this->avStandarOneYears = $avStandarOneYears;
    }

    function setAvStandarTowYears($avStandarTowYears) {
        $this->avStandarTowYears = $avStandarTowYears;
    }

    function setAvStandarthreeYears($avStandarthreeYears) {
        $this->avStandarthreeYears = $avStandarthreeYears;
    }

    function setAvStandarFourYears($avStandarFourYears) {
        $this->avStandarFourYears = $avStandarFourYears;
    }

    function setAvStandarFiveYears($avStandarFiveYears) {
        $this->avStandarFiveYears = $avStandarFiveYears;
    }

    function setAvStandarSixToMoreYears($avStandarSixToMoreYears) {
        $this->avStandarSixToMoreYears = $avStandarSixToMoreYears;
    }

    public function getDiagnosticStandarAvTest(Array $data){
       
        $messageOd = "";
        $messageOi = "";
        $message = "Mensaje por definir";
        
        $year = $data[0]['repositoryYears'];
        $avOd = $data[0]['eyeRight'];
        $avOi = $data[0]['eyeleft'];
        
        $messageOd = $this->avResult($year, $avOd);
        $messageOi = $this->avResult($year, $avOi);
        
        $message = "La Av(OD): ".$messageOd.". La Av(OI): ".$messageOi;
        
        return $message;
        
    }
    
    public function getDiagnosticStandarChromaticTest(Array $data){
        
        $message = " No se aplico prueba de percepción cromatica";
        
        return $message; 
        
    }
    
    public function getDiagnosticStandarMacularBalance(Array $data){
        
        $message = "No se realizo test de balance macular";

        if ($data[0]['ortoforia'] === "S" || $data[0]['ortotropia'] === "S" ){
            $message = "Posible balance macular adecuado";
        }
        
        if ($data[0]['foria'] == "S" || $data[0]['endoforia'] == "S" || $data[0]['exoforia'] == "S"){
            $message = "Evalue la posible existencia de estrabimos";
        }
        
        if ($data[0]['dvd'] == "S" || $data[0]['caElevada'] == "S"){
            $message = "Evalue la posible existencia de estrabimos";
        }
        
        return $message;
        
    }
    
    public function getMessageSubjectiveTest(Array $data){
        
        $message = "";
        
        if ($data[0]['center'] != "N/A" || $data[0]['sustain'] != "N/A" || $data[0]['maintain'] != "N/A"){
            
            $message = "Fue aplicado una prueba subjetiva de estimulación visual";
        }
        
        if ($data[0]['center'] == "N/A" || $data[0]['sustain'] == "N/A" || $data[0]['maintain'] == "N/A"){
            
            $message = "No Se aplico prueba subjetiva de estimulación visual";
        }
        
        return $message;
    }
    
    public function getMessageTonometricTest(Array $data){
        
        $message = "";
        
        if ($data[0]['tonometriaOi'] != 'N/A' || $data[0]['tonometriaOd'] != 'N/A'){
            
            $message = "Se aplico test de tonometria";
        }
        
        if ($data[0]['tonometriaOi'] == 'N/A' || $data[0]['tonometriaOd'] == 'N/A'){
            
            $message = "No se aplico test de tonometria";
        }
        
        return $message;   
    }
    
    private function findAvResult(array $array, $av){
        
        $value = false;
        
        for ($x=0; $x < sizeof($array); $x++){
            if ($array[$x]== $av){
                $value = true;
                break;
            }
        }
        
        return $value;     
    }
    
    private function avResult($year, $av){
        
        $message = "";
        $aux = $year;
        
         while ($year != 0){
            
            if ($year == "6"){
               if ($this->findAvResult($this->avStandarSixToMoreYears, $av)){
                    break;
               }
            }
            
            if ($year == "5"){
                if ($this->findAvResult($this->avStandarFiveYears, $av)){
                    break;
                }
            }
            
            if ($year == "4"){
                if ($this->findAvResult($this->avStandarFourYears, $av)){
                    break;
                }
            }
            
            if ($year == "3"){
                if ($this->findAvResult($this->avStandarthreeYears, $av)){
                    break;
                }
            }
            
            if ($year == "2"){
                if ($this->findAvResult($this->avStandarTowYears, $av)){
                    break;
                }
            }
            
            if ($year == "1"){
                if ($this->findAvResult($this->avStandarOneYears, $av)){
                    break;
                }
            }
            $year --;
        }

        if ($year == $aux){
            $message = "Presenta un desarrollo visual normal y adecuado en relación a la edad del paciente"; 
        }
        
        if ($year < $aux){
            $message = "Presenta un desarrollo visual mediana o completamente bajo en relación a la edad del paciente";
        }
        
        if ($year > $aux){
            $message = "Presenta un desarrollo visual normal o superior en relación a la edad del paciente";
        }
        
        return $message;
       
    }
   
}
