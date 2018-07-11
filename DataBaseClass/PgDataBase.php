<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PgDataBase
 *
 * @author Edgar
 */
class PgDataBase {
    
    private $pgConnect;
    private $dataBaseName = 'OpticalDataBase';
    private $portDataBase = '5432';
    private $dataBaseHost = '127.0.0.1';
    private $userDataBase = 'postgres';
    private $passwordDataBase = 'Pakhok5.5';
    
    function __construct() {
        $this->pgConnect =  pg_connect( " host=". $this->dataBaseHost.
                                        " dbname=".$this->dataBaseName.
                                        " user=".$this->userDataBase.
                                        " password=".$this->passwordDataBase)
       or die('No se ha podido conectar: ' . pg_last_error());

    }
    
    
    function getPgConnect() {
        return $this->pgConnect;
    }


    
    
    


}
