<?php

class Patient {
    
    private $firstName;
    private $middleName;
    private $lastName;
    private $maidenName;
    private $date;
    private $photo;
    
    function __construct($firstName, $middleName, $lastName, $maidenName, $date, $photo) {
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->maidenName = $maidenName;
        $this->date = $date;
        $this->photo = $photo;
    }
    
    function getFirstName() {
        return $this->firstName;
    }

    function getMiddleName() {
        return $this->middleName;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getMaidenName() {
        return $this->maidenName;
    }

    function getDate() {
        return $this->date;
    }

    function getPhoto() {
        return $this->photo;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function setMiddleName($middleName) {
        $this->middleName = $middleName;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    function setMaidenName($maidenName) {
        $this->maidenName = $maidenName;
    }

    function setDate($date) {
        $this->date = $date;
    }

    function setPhoto($photo) {
        $this->photo = $photo;
    }
    
}
