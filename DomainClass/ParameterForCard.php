<?php
/**
 * Description of ParameterForCard
 *
 * @author Edgar
 */
class ParameterForCard {
    
    private $referencePPP = 72;
    private $calculatePPP = 110;
    private $avList = array('1','0.8','0.63','0.5','0.4','0.33','0.25','0.2','0.17','0.13','0.1');
    private $distance;
    
    function __construct($distance) {
        $this->distance = $distance;
    }
    
    function getReferencePPP() {
        return $this->referencePPP;
    }

    function getCalculatePPP() {
        return $this->calculatePPP;
    }

    function getAvList() {
        return $this->avList;
    }

    function getDistance() {
        return $this->distance;
    }

    function setReferencePPP($referencePPP) {
        $this->referencePPP = $referencePPP;
    }

    function setCalculatePPP($calculatePPP) {
        $this->calculatePPP = $calculatePPP;
    }

    function setAvList($avList) {
        $this->avList = $avList;
    }

    function setDistance($distance) {
        $this->distance = $distance;
    }
    
}
