<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo;

use Vinosa\Repo\QueryBuilders\QueryBuilderInterface ;

/**
 * Description of RepositoryInterface
 *
 * @author vinosa
 */
interface RepositoryInterface
{
    public function quote( $var) ;
    
    public function query() ;
    
    public function get( $class, QueryBuilderInterface $builder ) ;
    
    public function fetch( $class, QueryBuilderInterface $builder ) ;
}
