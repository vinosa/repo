<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\QueryBuilders ;

/**
 * Description of QueryBuilderInterface
 *
 * @author vinosa
 */
interface QueryBuilderInterface
{
   public function start($start) ;
    
    public function limit($limit) ;
    
    public function quote( $unsafeString ) ;
    
    public function select($fields) ;
    
    public function getClause() ;
}
