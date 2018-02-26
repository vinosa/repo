<?php

/*
 * Copyright (C) 2018 vinogradov
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

namespace Vinosa\Repo\QueryBuilders;

/**
 * Description of SqlWhereClause
 *
 * @author vinogradov
 */
class SqlWhereClause extends WhereClause
{
    public function whereNotNull( $col )
    {
        
        $value = " NOT NULL " ;
            
        $operator = " IS " ;
                               
        return $this->whereSafe($col, $value, $operator) ; 
        
    }
    
    public function whereIn($col, $unsafeValues = [])
    {
        $safeValues = array_map( array( $this, "quote" ), $unsafeValues );
               
        $safeString = "(". implode( ",", $safeValues ) . ")" ; 
            
        return $this->whereSafe( $col, $safeString, " IN " ) ;

    }
    
    public function whereNot($col, $val)
    {
        
        return $this->where($col, $val, "<>", "AND");
                   
    }
    
    public function whereNull( $col )
    {

        return $this->whereSafe($col, " NULL ", " IS " ) ; 
        
    }
    
    protected function operator($operator)
    {
        if( is_string($operator) ){
            
            return $operator ;
            
        }
                
        return "=" ;
                  
    }
}
