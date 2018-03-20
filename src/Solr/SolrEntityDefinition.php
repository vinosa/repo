<?php

/*
 * Copyright (C) 2018 vino
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

use Vinosa\Repo\Reflection\EntityDefinition ;
use Vinosa\Repo\Reflection\EntityException ;
use Vinosa\Repo\Reflection\DocCommentLine ;

/**
 * Description of SolrEntityDefinition
 *
 * @author vino
 */
class SolrEntityDefinition extends EntityDefinition
{
    protected $core = null ;
    
    public function __construct($class)
    {
        parent::__construct($class);
        
        $lines = array_map( function($line) {return new DocCommentLine( $line) ; },
                            explode("\n", (new \ReflectionClass($class) )->getDocComment() )
                          );
        
        foreach($lines as $line){
            
            if( $line->isCore() ){
                
                $this->core = $line->core() ;
                
            }
        }
    }
    
    public function core()
    {
        if(is_null($this->core)){
            
            throw new EntityException("no core in this entity definition");
        }
        
        return $this->core ;
    }
}
