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

use Vinosa\Repo\AbstractWhereClause ;

/**
 * Description of SolrWhereClause
 *
 * @author vinosa
 */
class SolrWhereClause extends AbstractWhereClause
{
    public function whereNotNull( $col )
    {
                 
        return $this->whereSafe($col, "[* TO *]", ":") ; 
        
    }
    
    public function whereNot($col, $val)
    {
       
        return $this->where("!" . $col, $val, ":", "AND");
                       
    }
    
    public function whereIn($col, $unsafeValues = [])
    {
        $safeValues = array_map( array( $this, "quote" ), $unsafeValues );
               
        $safeString = "(". implode( " OR ", $safeValues ) . ")" ; 
        
        return $this->whereSafe( $col, $safeString ) ;
               
    }
    
    protected function operator($operator)
    {
           
        return ":" ;
            
    }
}
