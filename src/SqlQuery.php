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

namespace Vinosa\Repo;

/**
 * Description of SqlQuery
 *
 * @author vinosa
 */
class SqlQuery extends AbstractQuery
{     
    protected $insert = [];
    protected $update = [];
    protected $join = "" ;
    protected $from ;
      
    public function insert($column, $value): SqlQuery
    {
        $new = clone $this ;
        $new->insert[$column] = $value ;
        return $new ;
    }
    
    public function update($column, $value): SqlQuery
    {
        $new = clone $this ;
        $new->update[$column] = $value ;
        return $new ;
    }
    
    public function getInsert(): array
    {
        return $this->insert ;
    }
    
    public function getUpdate(): array
    {
        return $this->update ;
    }
      
    public function from( $table ): SqlQuery
    {
        $new = clone $this ;
        $new->from = $this->tableString($table);
        return $new ;
    }
    
    public function getFrom(): string
    {
        return $this->from ;
    }
    
    public function getJoin() : string
    {
        return $this->join ;
    }
       
    public function join($table, array $joinFields, string $type): SqlQuery
    {
        $new = clone $this ;
        $new->join .=  " " . $type . " JOIN " . $this->tableString($table) ;              
        $t = [] ;        
        foreach($joinFields as $key => $val){
            $t[] = $key . "=" . $val ;
        }        
        $new->join .= " ON (" . implode(" AND ", $t) . ") " ;        
        return $new ;
    }
    
    public function whereNull( $col ): SqlQuery
    {
        return $this->whereSafe($col, " NULL ", " IS ") ;        
    }
    
    private function tableString($var): string
    {
        if(is_string($var)){
            return $var ;
        }
        if(is_a($var, DatabaseRepository::class)){
            return $var->table() ;
        }
        if(is_array($var)){
            return implode(",", \array_map([$this,"tableString"],$var) ) ;
        }
    }
        
}
