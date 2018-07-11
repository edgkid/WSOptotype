<?php
/**
 * Description of PrintSize
 *
 * @author Edgar
 */
class PrintSize {
    
    private $cincoH = 5;
    private $cm = 10;
    private $mm = 1000;
    private $pi = 3.14;
    
    function getAvMinute ($value){
        
        return 1/$value;
    }
    
    function getAvGrade ($value){
        
        return $value * (1/60);
    }
    
    function getAvRadian ($value){
        
        return $value * ($this->pi/180);
    }
    
    function getSizeMinDetailmm ($value, $distance){
        
        return $value * ($this->mm * $distance);
    }
    
    function getSizeElement($value){
        
        return $value * $this->cincoH;
    }
    
    function getSizeElementCm ($value){
        
        return $value / $this->cm;
    }
    
    function getCanvasHight ($listSizePrint){
       
        $position = 0;
        $value = 0;
        $y = 24;
        $sizeArray = count($listSizePrint);

        while ($position < $sizeArray ){
        
            $value = $value + $listSizePrint[$position];
            $position ++;
        }
        
       
      return $value + $y; 
    }
    
    function getCanvasWith ($listSizePrint){
        
        $x = 18;
        $position = 7;
        $value = ($listSizePrint[$position] * $position)+ $x ; 
        
        return $value;
    }
    
}
