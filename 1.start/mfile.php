<?php 
ini_set("display_errors","off");
session_start();

include("connect.php");

if($_SESSION['userid']=="" || $_SESSION['pswd']=="")
{
	header("location:index.php");
}


$sql="select * from adminuser where usr='".$_SESSION['userid']."'";
$result_admin=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_admin)){extract($rows);
}
$_SESSION['cadmin']=$cadmin;
$_SESSION['csales']=$csales;
$_SESSION['cmps']=$cmps;
$_SESSION['csaleslog']=$csaleslog;
$_SESSION['cexpenses']=$cexpenses;
$_SESSION['csuper']=$csuper;

$_SESSION['cbranch']=$sbranch;
$sql="select sms_id,sms_username,sms_password,n_email from sms_notify";
$result_notify_m=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_notify_m)) {extract($rows);
}

$sql="select * from main_setup";
$result_main_stepup=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_main_stepup)) {extract($rows);
}
$nstype=$stype;
$nbranches=$nbranch;
$nproll=$proll;

?>