<?php


namespace base\model;


abstract  class BaseModelMethods
{

    final protected function createFields( $set,$table =false){

        $set['fields'] = (is_array($set['fields']) && !empty($set['fields']))
            ? $set['fields'] : '*';
        $table = $table ? $table . '.' : '';
        $fields = '';

        foreach($set['fields'] as $field){
            $fields .= $table . $field .',';
        }
        return trim($fields,',');


    }

    final protected function createWhere($set, $table =false, $instruction = "WHERE"){
        $table = $table ? $table . '.' : '';

        $where = '';
        if(is_array($set['where']) && !empty($set['where'])){

            $set['operand'] = (is_array($set['operand']) && !empty($set['operand'])
                ? $set['operand'] : [' = ']);

            $set['condition'] = (is_array($set['condition']) && !empty($set['condition'])
                ? $set['condition'] : ['AND']);
            $where = $instruction;
            $o_count = 0;
            $c_count = 0;

            foreach($set['where'] as $key => $item){
                $where .= ' ';

                if($set['operand'][$o_count]){
                    $operand = $set['operand'][$o_count];
                    $o_count ++;
                }else{
                    $operand  = $set['operand'][$o_count -1];
                }
                if($set['condition'][$c_count]){
                    $condition = $set['condition'][$c_count];
                    $c_count ++;
                }else{
                    $condition  = $set['condition'][$c_count -1];
                }

                if($operand === "IN" || $operand === "NOT IN"){

                    if(is_string($item) && strpos($item, "SELECT") === 0){
                        $in_str = $item;
                    }else{
                        if(is_array($item)) $temp_item =$item;
                        else $temp_item = explode(',', $item);
                        $in_str = '';

                        foreach($temp_item as $valeu){
                            $in_str .= "'" . addslashes( trim($valeu)) . "',";
                        }
                    }
                    $where .= $table . $key . ' '. $operand . ' (' . trim($in_str) . ') ' .$condition;
                }elseif(strpos($operand,"LIKE") !== false){

                    $like_tamplate = explode('%', $operand);

                    foreach($like_tamplate as $lt_key => $lt){
                        if(!$lt){
                            if(!$lt_key){
                                $item = '%'. $item;
                            }else{
                                $item .=   '%';
                            }
                        }
                    }
                    $where .= $table .$key. ' LIKE ' . "'" .addslashes($item)  . "' $condition";
                }else{

                    if(strpos($item, "SELECT") === 0){
                        $where .= $table.$key.$operand. ' ('. $item .")$condition";
                    }else{
                        $where .= $table.$key.$operand. "'". addslashes($item) ."' $condition";
                    }
                }
            }
            $where = substr($where, 0, strrpos($where, $condition));

        }
        return $where;
    }


    final protected function createOrder( $set, $table =false){

        $table = $table ? $table .'.' : "";

        $order_by = '';
        if(is_array($set['order']) && !empty($set['order'])){
            $set['order_direction'] = (is_array($set['order_direction']) && !empty($set['order_direction']))
                ? $set['order_direction'] : "ASC";


            $order_by = "ORDER BY ";
            $direct_count = 0;

            foreach($set['order'] as $order){
                if($set['order_direction'][$direct_count]){
                    $order_direction = strtoupper($set['order_direction'][$direct_count]);
                    $direct_count ++;
                }else{
                    $order_direction = strtoupper($set['order_direction'][$direct_count -1]);
                }
                if(is_int($order)) $order_by .= $order. ' '. $order_direction . ',';
                else $order_by .= $table . $order. ' '. $order_direction . ',';
            }
            $order_by = trim($order_by, ',');
        }
        return $order_by;
    }

    final protected function createJoin($set, $table,  $new_where = false){

        $fields = '';
        $join = '';
        $where ='';

        if($set['join']){

            $join_table = $table;
            foreach($set['join'] as $key => $valeu){

                if(is_int($key)){
                    if(!$valeu['table']) continue;
                    else $key = $valeu['table'];
                }

                if($join) $join .= " ";
                if($valeu['on']){
                    $join_fields = "";
                    switch(2){
                        case isset($item['on']['fields']) && count($valeu['on']['fields']);
                            $join_fields = $valeu['on']['fields'];
                            break;
                        case count($valeu['on']);
                            $join_fields = $valeu['on'];
                            break;

                        default:
                            continue 2;
                            break;

                    }

                    if(!$valeu['type']) $join .= "LEFT JOIN ";
                    else $join .= trim(strtoupper($valeu['type'])). " JOIN ";

                    $join .= $$key. " ON ";

                    if($valeu["on"]["table"]) $join .= $valeu["on"]["table"];
                    else $join .=$join_table;

                    $join .= '.'. $join_fields[0] . '=' . $key .'.'. $join_fields[1];
                    $join_table = $key;

                    if($new_where){
                        if($valeu["where"]){
                            $new_where = false;
                        }
                        $group_condition = "WHERE";
                    }else{
                        $group_condition = $valeu['group_condition'] ? strtoupper($valeu['group_condition']) : "AND";
                    }

                    $fields .= $this->createFields($valeu, $key );
                    $where .= $this->createWhere( $valeu, $key, $group_condition);
                }

            }
        }
        return compact('fields','join','where');

    }

}