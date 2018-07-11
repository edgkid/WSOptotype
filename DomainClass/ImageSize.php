<?php
/**
 * Description of ImageSize
 *
 * @author Edgar
 */
class ImageSize {
    
    private $ppi;
    private $cmOnInch;
    
    function __construct($ppi) {
        $this->ppi = $ppi;
        $this->cmOnInch = 2.54;
    }
    
    function getSizeInPixelForElements($value){
        
        return ($value * $this->ppi)/ $this->cmOnInch;
        
    }
    
    function getSizeInPixel ($value){
        
        return ($value * $this->ppi)/$this->cmOnInch;
    }
    
}
