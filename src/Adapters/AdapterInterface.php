<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\Adapters;

/**
 *
 * @author vinosa
 */
interface AdapterInterface
{
    public function createEntity($class, $result) ;
    
    public function createEntities($class, $result) ;
    
    public function quote($str) ;
    
    public function query($str) ;
}
