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

namespace Vinosa\Repo\Database;

use Vinosa\Repo\Reflection\EntityDefinition ;
use Vinosa\Repo\Reflection\DocCommentLine ;

/**
 * Description of DatabaseEntityDefinition
 *
 * @author vino
 */
class DatabaseEntityDefinition extends EntityDefinition
{
    protected $table ;
    protected $keys = [] ;
    
    public function __construct($class)
    {
        parent::__construct($class);
        
        $lines = array_map( function($line) {return new DocCommentLine( $line) ; },
                            explode("\n", (new \ReflectionClass($class) )->getDocComment() )
                          );
        
        foreach($lines as $line){
            
            if( $line->isTable() ){
                
                $this->table = $line->table() ;
            }
            
            if( $line->isKey() ){
                
                $this->keys[] = $line->key();
                
            }
        }
    }
    
    public function table()
    {
        return $this->table ;
    }
    
    public function keys()
    {
        return $this->keys ;
    }
}
