<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo ;

/**
 * Description of ReflectionCollection
 *
 * @author vinosa
 */
class ReflectionCollection
{
    protected $reflections = [];
    
    public function getReflection(string $className) : Reflection
    {
        if(!isset($this->reflections[$className])){
            
            $this->reflections[$className] = new Reflection( $className);
        }
        
        return $this->reflections[$className] ;
    }
    
}
