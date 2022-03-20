<?php 
ini_set("display_errors","off");
session_start();
$cons=mysql_connect("host","someone","")  or die(mysql_error());
$sel=mysql_select_db("somebase") or die(mysql_error());


?> 
