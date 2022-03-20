<?php

class BaseModel {
    protected $tableName = '';
    protected $tableFields = [];

    function select($newcode = '', $chm = '', $sbranch = '' , $psize = ''){
        $sqlWhereString = '';
        if($psize !=''){
            $sqlWhereString = " and tp_size='$psize' ";
        }


        // Dynamically build query (non working code)
        /*
        $sql .= "select * from " . $this->tableName . " where
        foreach ($this->tableFields as $field){
            $sqlWhere [] = " and " . $field . " = " . $values;
        }
        $sql .= join($sqlWhere);
        */

        $sql="select * from $this->tableName where tp_code = '$newcode ' and tp_month='$chm' and tp_branch='$sbranch' 
          $sqlWhereString";
        return mysql_query($sql);

    }

    function insert($pname,$psize,$newcode, $pqts,$sbranch,$chm) {
        $sql="insert into $this->tableName(tp_name,tp_size,tp_code,tp_quantity,tp_branch,tp_month) values('$pname','$psize','$newcode','$pqts','$sbranch','$chm')";
        return mysql_query($sql);
    }

    function update($newqty, $newcode, $chm, $sbranch, $psize) {
        $sql="update $this->tableName set tp_quantity='$newqty'  where tp_code='$newcode' and tp_month='$chm' and tp_branch='$sbranch' and tp_size='$psize'";
        return mysql_query($sql);
    }
}
