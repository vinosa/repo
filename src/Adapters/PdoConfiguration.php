<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\Adapters ;

/**
 * Description of PdoConfiguration
 *
 * @author vinosa
 */
class PdoConfiguration
{
    //put your code here
    protected $host;
    protected $user;
    protected $password;
    protected $database ;
    
    public function __construct($config)
    {
        foreach($config as $key => $val){
            
            $this->{$key} = $val;
        }
    }
    
    public function getHost()
    {
        return $this->host ;
    }
    
    public function getUser()
    {
        return $this->user ;
    }
    
    public function getPassword()
    {
        return $this->password ;
    }
    
    public function getDatabase()
    {
        return $this->database ;
    }
}
