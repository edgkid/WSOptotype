<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'DataBaseClass/PgDataBase.php';
/**
 * Description of UserDB
 *
 * @author Edgar
 */
class UserDB extends PgDataBase{
   
    function __construct() {
        parent::__construct();
    }
    
     /**
     * obtiene todos los registros de la tabla "people"
     * @return Array array con los registros obtenidos de la base de datos
     */
    public function getUsers(){  
        $data = array();
        $query = "SELECT IdUser, UserName, UserPassword, Fk_idRoll "
                . "FROM User_System;";
        $users = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
       
        while ($line = pg_fetch_array($users, null, PGSQL_ASSOC)) {
            $data[] = $line;
        }
     
        return $data; 
    }
    
    /**
     * obtiene un solo registro dado su ID
     * @param int $id identificador unico de registro
     * @return Array array con los registros obtenidos de la base de datos
     */
    public function getUserById($id=0){   
        
        $data = array();
        $query = "SELECT IdUser, UserName, UserPassword, Fk_idRoll "
                . "FROM User_System "
                . "WHERE IdUser=".$id;
        $user = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
        while ($line = pg_fetch_array($user, null, PGSQL_ASSOC)) {
            $data[] = $line;
        }
     
        return $data;              
    }
    
    /**
     * obtiene un solo registro dado el nombre de usuario y contraseña
     * @param String $userName nombre de usuario del registro
     * @param String $userPassword contraseña de usuario
     * @return Array array con los registros obtenidos de la base de datos
     */
    public function getUserByLogin ($userName="", $userPassword=""){
        
        $data = array();
        /*$query = "  SELECT IdUser, UserName, UserPassword, Fk_UserRoll 
                    FROM user_system 
                    WHERE UserName = '".$userName."'".
                        " AND UserPassword= '".$userPassword."';";*/
        
        $query = "  SELECT usy.iduser,usy.username,". 
                            " usy.userpassword, usy.fk_idRoll,". 
                            " usr.idroll, usr.rollname, usr.rolldescription".
                    " FROM user_system usy, user_roll usr".
                    " WHERE usy.fk_idroll = usr.idroll".
                              " AND usy.username = '".$userName."'".
                              " AND usy.userpassword = '".$userPassword."'";
        
        $user = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
        while ($line = pg_fetch_array($user, null, PGSQL_ASSOC)) {
            $data[] = $line;
        }
        
        return $data;
    }

}
