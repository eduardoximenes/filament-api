<?php

namespace App\Filters;

use Illuminate\Http\Request;


class ApiFilter{

    protected $safeParms= [];
    protected $columnMap= [];
    protected $operatorMap= [];

    public function transform(Request $request){
        $eloQuery= [];

        foreach($this->safeParms as $parm=> $operators){
            $query= $request->query($parm);
            
            //checo se o parametro é valido na consulta;
            if(!isset($query))
                continue;

            //se for um parameto que foi alterado devido ao json, eu o altero para o nome de coluna real;
            $column= $this->columnMap[$parm] ?? $parm;

            //
            foreach($operators as $operator){
                if(isset($query[$operator])){
                    $eloQuery[]= [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }
        return $eloQuery;
    }

}
