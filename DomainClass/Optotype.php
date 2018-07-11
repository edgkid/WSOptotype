<?php

class Optotype {
   
    private $optotypeCode;
    private $optotypeName;
    private $image;
    
    function __construct($optotypeCode, $optotypeName, $image) {
        $this->optotypeCode = $optotypeCode;
        $this->optotypeName = $optotypeName;
        $this->image = $image;
    }
    
    function getOptotypeCode() {
        return $this->optotypeCode;
    }

    function getOptotypeName() {
        return $this->optotypeName;
    }

    function getImage() {
        return $this->image;
    }

    function setOptotypeCode($optotypeCode) {
        $this->optotypeCode = $optotypeCode;
    }

    function setOptotypeName($optotypeName) {
        $this->optotypeName = $optotypeName;
    }

    function setImage($image) {
        $this->image = $image;
    }
    
}
