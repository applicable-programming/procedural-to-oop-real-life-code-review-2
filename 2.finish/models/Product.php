<?php

class Product extends BaseModel {
    protected $tableName = 'top_product';


    protected $tableFields = ['tp_size', 'tp_month', 'tp_branch'];

    function insert()
    {
        // new code that will overwrite method from BaseModel
    }
}
