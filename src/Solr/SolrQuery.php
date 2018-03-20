<?php

/*
 * Copyright (C) 2018 vinosa
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Vinosa\Repo\Solr;

use Vinosa\Repo\AbstractQuery ;
use Vinosa\Repo\QueryException ;
/**
 * Description of SolrQuery
 *
 * @author vinosa
 */
class SolrQuery extends AbstractQuery
{
    //protected $where ;
    protected $repository;
    protected $core = null ;
    
    public function __construct(SolrRepository $repository)
    {
        $this->repository = $repository ;
        
        $this->whereClause = new SolrWhereClause( $this ) ;
    }
   
    public function getQuery()
    {
        
        return  $this->getWhere()->output() ;
        
    }
    
    public function setQuery($query)
    {
        $this->query = $query ;
    }
      
    
    public function formatDate( $time = null )
    {
        if(empty($time)){
            
            $time = time();
            
        }
        
        return $this->getHelper()->formatDate($time);
    }
    
    public function dateBetween($start, $end)
    {
        return "[" . $this->formatDate( $start ) . " TO " . $this->formatDate($end) . "]" ;
    }
    
    public function setCore($core)
    {
       
        $this->core = $core;
    }
    
    public function core()
    {
         if(is_null($this->core) ){
             
             throw new QueryException("core is not defined") ;
        }
         
        return $this->core ;
    }
       
    private function getHelper()
    {
        return $this->getRepository()->getHelper() ;
    }
    
    
}
