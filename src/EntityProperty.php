<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo;

/**
 * Description of EntityProperty
 *
 * @author vinosa
 */
class EntityProperty
{
    protected $name;
    protected $readOnly = false;
    
    public function __construct($name, $readonly = false)
    {
        $this->name = $name;
        $this->readOnly = $readonly ;
    }
    
    public function readOnly()
    {
        return $this->readOnly ;
    }
    
    public function name()
    {
        return $this->name ;
    }
}
