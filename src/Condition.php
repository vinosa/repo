<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo;

/**
 * Description of Condition
 *
 * @author vino
 */
class Condition
{
    //put your code here
    public $field;
    public $operator;
    public $value;
    public $escape ;

    
    public function __construct($field, $value, $escape = true, $operator = null)
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator ;
        $this->escape = $escape;

    }
}
