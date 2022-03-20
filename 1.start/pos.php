<?php 
ini_set("display_errors","off");
session_start();

include("connect.php");
include("mfile.php");
$date=date('Y-m-d');


if(isset($_POST['submit']) && $_POST['submit']=="click"){
$scanbarcode=mysql_real_escape_string($_POST['scanbarcode']);
$scanqty=mysql_real_escape_string($_POST['scanqty']);
if($scanqty==""){$pqts=1;} else{$pqts=$scanqty;}
$sql="select * from bar_code  where mcode='$scanbarcode' or bcode='$scanbarcode'";
$result_check_code=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_check_code)){extract($rows);
}
$newcode=$mcode;
if(mysql_num_rows($result_check_code)<1){$errorbarcode="Product Not Found";}else {
$sql="select mp_balance as mainbalance from m_product where mp_code='$newcode' ";
$result_new_check=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_new_check)) {extract($rows);
}
if($mainbalance<$pqts){$errorbalance="Not Enough Balance to Complete this Sales $mainbalance";}else {
$sql="select mp_selling as sellprice,mp_cost as costprice,mp_tvalue as tvalue,mp_tcvalue as tcvalue,mp_name as pname,mp_size as psize from m_product where mp_code='$newcode' and mp_branch='$sbranch'";
$result_check_price=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_check_price)) {extract($rows);
}
$newsellprice=$sellprice*$pqts;
$newcostprice=$costprice*$pqts;
$newprofit=$newsellprice-$newcostprice;
$newtvalue=$tvalue-$newsellprice;
$newtcvalue=$tcvalue-$newcostprice;
$newmainbalance=$mainbalance-$pqts;

$sql="insert into ma_temp(mat_tid,mat_service,mat_size,mat_code,mat_rate,mat_qty,mat_amount,mat_date,mat_status,mat_by,mat_profit,mat_type,mat_branch) values('".$_SESSION['userid']."','$pname','$psize','$newcode','$sellprice','$pqts','$newsellprice','$date','Pending','".$_SESSION['userid']."','$newprofit','Product','$sbranch')";
$result_insert_temp=mysql_query($sql) or die(mysql_error());

$sql="update m_product set mp_balance='$newmainbalance',mp_tvalue='$newtvalue',mp_tcvalue='$newtcvalue' where mp_code='$newcode' and mp_branch='$sbranch'";
$result_update_mproduct=mysql_query($sql) or die(mysql_error());



$chm=date('m-Y');


$sql="select * from top_product where tp_code='$newcode' and tp_month='$chm' and tp_branch='$sbranch' and tp_size='$psize'";
$result_check_record=mysql_query($sql) or die(mysql_error());
if(mysql_num_rows($result_check_record)<1){$sql="insert into top_product(tp_name,tp_size,tp_code,tp_quantity,tp_branch,tp_month) values('$pname','$psize','$newcode','$pqts','$sbranch','$chm')";
$result_top_insert=mysql_query($sql) or die(mysql_error());
}else{
$sql="select tp_quantity as topqty from top_product where tp_code='$newcode' and tp_month='$chm' and tp_branch='$sbranch' and tp_size='$psize'";
$result_tp_quantity=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_tp_quantity)) {extract($rows);
}
$newqty=$topqty+$pqts;
$sql="update top_product set tp_quantity='$newqty'  where tp_code='$newcode' and tp_month='$chm' and tp_branch='$sbranch' and tp_size='$psize'";
$result_update_tp=mysql_query($sql) or die(mysql_error());



}


}}
}


if(isset($_POST['submit']) && $_POST['submit']=="Post Sales"){

$cpaid=mysql_real_escape_string($_POST['cpaid']);
$ppaid=mysql_real_escape_string($_POST['ppaid']);
$bpaid=mysql_real_escape_string($_POST['bpaid']);
$poffice=mysql_real_escape_string($_POST['poffice']);
$boffice=mysql_real_escape_string($_POST['boffice']);
$stype=mysql_real_escape_string($_POST['stype']);

$gname=mysql_real_escape_string($_POST['gname']);
$gemail=mysql_real_escape_string($_POST['gemail']);
$gtel=mysql_real_escape_string($_POST['gtel']);



$sql="select cp from client_profile";
$result_count=mysql_query($sql) or die(mysql_error());
$count=mysql_num_rows($result_count);
$da=date('Ymd');
$co=$count+1;
$cound=$da.$co;
$y=date('Y');

if($stype=="new"){$sql="insert into client_profile (clid,client_lastname,client_firstname,client_email,client_tele,client_birthday,client_year,client_branch,client_cardno) values('$cound','$gname','','$gemail','234".substr($gtel,1,10)."','','$y','$sbranch','')";
$result_insertclient=mysql_query($sql) or die(mysql_error());

$sql="select clid as clientid from client_profile where  clid='$cound' ";
$result_profile=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_profile)) {extract($rows);
}
}elseif($stype=="existing"){$clientid=$_POST['clname'];}



$clname=$clientid;
$discount=$_POST['discount'];
$ttotal=$_POST['ttotal'];
$nwtotal=$ttotal-$discount;
$amr=$cpaid+$ppaid+$bpaid;

$mop=$_POST['mop'];
$serby=$_POST['serby'];
$sbal=$nwtotal-$amr;
$ddate=date('Y-m-d');
$mprofit=$_POST['profit'];


$sql_vv="select * from client_profile where clid='$clname'";
$result_get_client=mysql_query($sql_vv) or die(mysql_error());
while($rows=mysql_fetch_array($result_get_client)) {extract($rows);
}
$cmyname=$client_lastname.' '.$client_firstname;
$sql="select * from ma_sales";
$result_gen=mysql_query($sql) or die(mysql_error());
$counts=mysql_num_rows($result_gen);
$coun=$counts+1;
$da=date('md');
$iid=$da.$coun;

$climituseable=$ttotal-$amr;

$myavbalance=number_format($cl_avil,2);


$sql="insert into ma_sales(masl_cname,masl_id,masl_transid,masl_total,masl_bal,masl_paid,masl_branch,masl_officer,masl_mop,masl_date,masl_profit,masl_discount) values('$cmyname','$clname','$iid','$ttotal','$sbal','$amr','$sbranch','".$_SESSION['userid']."','$mop','$ddate','$mprofit','$discount')";
$result_insert_msales=mysql_query($sql) or die(mysql_error());

$sql="insert into ma_paylog(mpl_tid,mpl_client,mpl_client_id,mpl_paid,mpl_mop,mpl_by,mpl_date,mpl_branch) values('$iid','$cmyname','$clname','$amr','$mop','".$_SESSION['userid']."','$ddate','$sbranch')";
$resut_paylog=mysql_query($sql) or die(mysql_error());
$sql="update ma_temp set mat_tid='$iid',mat_status='Completed' where mat_by='".$_SESSION['userid']."' and mat_status='Pending'";
$result_update_temp=mysql_query($sql) or die(mysql_error());

$sql="update orderlog set o_type='Sold' where o_by='".$_SESSION['userid']."' and o_type='Pending'";
$result_sold=mysql_query($sql) or die(mysql_error());


if($sbal>0){

	
	$sql="select cl_avil as oldcavil,cl_used as oldusedcl from client_profile where clid='$clname' ";
	$result_clientbalancelimist=mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_array($result_clientbalancelimist)) {extract($rows); }
	
	$newcused=$oldusedcl+$sbal;
			$newavail=$cl_crelimit-$newcused;

	$sql="update client_profile set  cl_avil='$newavail',cl_used='$newcused' where clid='$clname'  ";
	$result_updatecreditlmit=mysql_query($sql) or die(mysql_error());
	
}

if($cpaid!=""){
$sql="update ma_paylog set mpl_cash='$cpaid' where  	mpl_tid='$iid'";
$result_cash=mysql_query($sql) or die(mysql_error());
}
if($ppaid!=""){
$sql="update ma_paylog set mpl_pamount='$ppaid',mpl_pacc='$poffice' where  	mpl_tid='$iid'";
$result_pos=mysql_query($sql) or die(mysql_error());

}
if($bpaid!=""){

$sql="update ma_paylog set mpl_bamount='$bpaid',mpl_bacc='$boffice' where  	mpl_tid='$iid'";
$result_office=mysql_query($sql) or die(mysql_error());
}
$sql="select * from mp_salesandexpenses where ms_date='$ddate' and ms_branch='$sbranch' ";
$result_salesandexpens=mysql_query($sql) or die(mysql_error());
if(mysql_num_rows($result_salesandexpens)<1){$sql="insert into mp_salesandexpenses (ms_date,ms_sales,ms_expenses,ms_branch,ms_difference) values('$ddate','$amr','','$sbranch','$amr')";
$result_insert_sales=mysql_query($sql) or die(mysql_error());
}else{
$sql="select ms_sales as oldsales,ms_difference as diff from mp_salesandexpenses where ms_date='$ddate' and ms_branch='$sbranch' ";
$result_sport=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_sport)) {extract($rows); 
}
$newsales=$oldsales+$amr; 
$newdiff=$diff+$amr;

$sql="update mp_salesandexpenses set ms_sales='$newsales',ms_difference='$newdiff' where ms_date='$ddate' and ms_branch='$sbranch' ";
$resutl_insert_log=mysql_query($sql) or die(mysql_error());


}
echo "<script type='text/javascript'> document.location='viewinvoice.php?view=$iid'; </script>";

}


if(isset($_POST['add']) && $_POST['add']=="Add"){
$scanbarcode=mysql_real_escape_string($_POST['prorate']);
$ssbranch=mysql_real_escape_string($_POST['sbranch']);
$scanqty=mysql_real_escape_string($_POST['proqty']);
if($scanqty==""){$pqts=1;} else{$pqts=$scanqty;}
$sql="select mpt_code  from mproduct  where mpt_code='$scanbarcode'";
$result_check_code=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_check_code)){extract($rows);
}
$newcode=$mpt_code ;
if(mysql_num_rows($result_check_code)<1){$errorbarcode="Product Not Found";}else {
$sql="select mp_balance as mainbalance from m_product where mp_code='$newcode' and mp_branch='$ssbranch' ";
$result_new_check=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_new_check)) {extract($rows);
}
if($mainbalance<$pqts){$errorbalance="Not Enough Balance to Complete this Sales $mainbalance";}else {
$sql="select mpt_price as sellprice,mpt_cost as costprice from mproduct where mpt_code='$newcode'";
$result_check_price=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_check_price)) {extract($rows);
}

$sql="select mp_tvalue as tvalue,mp_tcvalue as tcvalue,mp_name as pname,mp_size as psize,mp_category as pcat from m_product where mp_code='$newcode' and mp_branch='$sbranch'";
$result_check_price=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_check_price)) {extract($rows);
}
$newsellprice=$sellprice*$pqts;
$newcostprice=$costprice*$pqts;
$newprofit=$newsellprice-$newcostprice;
$newtvalue=$tvalue-$newsellprice;
$newtcvalue=$tcvalue-$newcostprice;
$newmainbalance=$mainbalance-$pqts;
$nnmae=mysql_real_escape_string($pname);
$sql="insert into ma_temp(mat_tid,mat_service,mat_size,mat_code,mat_rate,mat_qty,mat_amount,mat_date,mat_status,mat_by,mat_profit,mat_type,mat_branch,mat_category) values('".$_SESSION['userid']."','$nnmae','$psize','$newcode','$sellprice','$pqts','$newsellprice','$date','Pending','".$_SESSION['userid']."','$newprofit','Product','$ssbranch','$pcat')";
$result_insert_temp=mysql_query($sql) or die(mysql_error());

$sql="select mat as mid from ma_temp where mat_tid='".$_SESSION['userid']."' and mat_status='Pending'";
$result_insert_id=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_insert_id)) {extract($rows);}
    $last_id= mysql_insert_id(mysql_query);
	
$ttm=$pqts*$sellprice;
$sql="insert into orderlog(o_product,o_code,o_qty,o_price,o_tvalue,o_date,o_by,o_branch,o_type,o_soldid) values('$nnmae','$newcode','$pqts','$costprice','$ttm','$date','".$_SESSION['userid']."','$ssbranch','Pending','$mid')";
$result_create_new_product=mysql_query($sql) or die(mysql_error());

$sql="update m_product set mp_balance='$newmainbalance',mp_tvalue='$newtvalue',mp_tcvalue='$newtcvalue' where mp_code='$newcode' and mp_branch='$ssbranch'";
$result_update_mproduct=mysql_query($sql) or die(mysql_error());


$chm=date('m-Y');

 
$sql="select * from top_product where tp_code='$newcode' and tp_month='$chm' and tp_branch='$ssbranch' and tp_size='$psize'";
$result_check_record=mysql_query($sql) or die(mysql_error());
if(mysql_num_rows($result_check_record)<1){$sql="insert into top_product(tp_name,tp_size,tp_code,tp_quantity,tp_branch,tp_month,tp_cat) values('$nnmae','$psize','$newcode','$pqts','$ssbranch','$chm','$pcat')";
$result_top_insert=mysql_query($sql) or die(mysql_error());
}else{
$sql="select tp_quantity as topqty from top_product where tp_code='$newcode' and tp_month='$chm' and tp_branch='$ssbranch' and tp_size='$psize'";
$result_tp_quantity=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_tp_quantity)) {extract($rows);
}
$newqty=$topqty+$pqts;
$sql="update top_product set tp_quantity='$newqty'  where tp_code='$newcode' and tp_month='$chm' and tp_branch='$ssbranch' and tp_size='$psize'";
$result_update_tp=mysql_query($sql) or die(mysql_error());



}


}}
}

if(isset($_POST['delete']) && $_POST['delete']=="Delete"){
$query10="select * from ma_temp where mat='".$_POST['cname']."'";
$result_temp_first_check=mysql_query($query10) or die(mysql_error());
while($rows=mysql_fetch_array($result_temp_first_check)) {extract($rows); 
}
$pname=$mat_service;
$psize=$mat_size;
$pcode=$mat_code;
$pqty=$mat_qty;
$pamount=$mat_amount;
$sprofit=$mat_profit;
$bcost=$mat_amount-$mat_profit;
$newbuy=$bcost/$pqty;
$ssbranch=$mat_branch;

$sql="delete from orderlog where o_soldid='".$_POST['cname']."'";
$result_delete=mysql_query($sql) or die(mysql_error());

$sql="select mp_balance as oldbalance,mp_tvalue as tvalue,mp_tcvalue as tcvalue from m_product where mp_code='$pcode' and mp_size='$psize' and mp_branch='$ssbranch'";
$result_check=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_check)) {extract($rows);
}
$newbalance=$oldbalance+$pqty;
$newvalue=$tvalue+$pamount;
$newtcvalue=$tcvalue+$bcost;

$sql="update m_product set mp_balance='$newbalance',mp_tvalue='$newvalue',mp_tcvalue='$newtcvalue' where mp_code='$pcode' and mp_size='$psize' and mp_branch='$ssbranch'";
$result_update_product=mysql_query($sql) or die(mysql_error());


$chm=date('m-Y');


$sql="select tp_quantity as topqty from top_product where tp_code='$pcode' and tp_month='$chm' and tp_branch='$ssbranch' and tp_size='$psize'";
$result_tp_quantity=mysql_query($sql) or die(mysql_error());
while($rows=mysql_fetch_array($result_tp_quantity)) {extract($rows);
}

$newqty=$topqty-$pqty;
$sql="update top_product set tp_quantity='$newqty'  where tp_code='$pcode' and tp_month='$chm' and tp_branch='$ssbranch' and tp_size='$psize'";
$result_update_tp=mysql_query($sql) or die(mysql_error());

$sql="delete from ma_temp where mat='".$_POST['cname']."'";
$result_delete=mysql_query($sql) or die(mysql_error());


}




$sql="select * from ma_temp where mat_by='".$_SESSION['userid']."' and mat_status='Pending'";
$result_temp=mysql_query($sql) or die(mysql_error());
$count_all=mysql_num_rows($result_temp);

 $qry = mysql_query(" SELECT SUM(mat_amount) AS total from ma_temp where mat_by='".$_SESSION['userid']."' and mat_status='Pending'");
    $row = mysql_fetch_assoc($qry);
    $su= $row['total'];
	$se=$su;
$sum=number_format($se);


 $qry = mysql_query(" SELECT SUM(mat_qty) AS stcount from ma_temp where mat_by='".$_SESSION['userid']."' and mat_status='Pending'");
    $row = mysql_fetch_assoc($qry);
    $stcount= $row['stcount'];





 $qry = mysql_query(" SELECT SUM(mat_profit) AS profit from ma_temp where mat_by='".$_SESSION['userid']."' and mat_status='Pending'");
    $row = mysql_fetch_assoc($qry);
    $pro= $row['profit'];

?>

<?php include("header.php"); ?>

<script src="datetimepicker_css.js"></script>

        <script src="js/jquery.js"></script>
<script>
$(document).ready(function(){

    part1Count = 160;
    part2Count = 145;
    part3Count = 152;

    $('#message').keyup(function(){
        var chars = $(this).val().length;
            messages = 0;
            remaining = 0;
            total = 0;
        if (chars <= part1Count) {
            messages = 1;
            remaining = part1Count - chars;
        } else if (chars <= (part1Count + part2Count)) { 
            messages = 2;
            remaining = part1Count + part2Count - chars;
        } else if (chars > (part1Count + part2Count)) { 
            moreM = Math.ceil((chars - part1Count - part2Count) / part3Count) ;
            remaining = part1Count + part2Count + (moreM * part3Count) - chars;
            messages = 2 + moreM;
        }
        $('#remaining').text(remaining);
        $('#messages').text(messages);
        $('#total').text(chars);
        if (remaining > 1) $('.cplural').show();
            else $('.cplural').hide();
        if (messages > 1) $('.mplural').show();
            else $('.mplural').hide();
        if (chars > 1) $('.tplural').show();
            else $('.tplural').hide();
    });
    $('#message').keyup();
});
</script>
<script type="application/javascript">

  function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }

</script><head>
   <script type="text/javascript" language="javascript">
function choses()
{
document.form1.prorate.value=document.form1.proname.options[document.form1.proname.selectedIndex].value;
document.form1.hdequipname.value=document.form1.proname.options[document.form1.proname.selectedIndex].text;
//alert("Yes");
}
</script>



            <!-- Page Content Start -->
            <!-- ================== -->

            
   <div class="wraper container-fluid">
                <div class="page-title"> 
                    <h3 class="title">Post New Sales</h3> 
                </div>
				


  <div class="row">
                    <!-- Basic example -->
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h3 class="panel-title">Post New Sales</h3></div>
                            <div class="panel-body">
<?php if($errormessage!=""){ ?><div class="alert alert-danger">
                                     <?php echo $errormessage; ?>
                                </div><?php } ?>     
                             
							
					<?php if($msg!=""){ ?><a href="bigprint.php?view=<?php echo $iid; ?>" target="_blank"><div class="alert alert-success"><h4 align="center">
CLICK TO PRINT INVOICE</h4>                               </div></a>  <?php } ?>   	
          
								 <form class="form-inline" role="form" id="form1" name="form1" action="<?php mysql_real_escape_string($_SERVER["PHP_SELF"]);?>" method="post">
					

									 
                                   <div class="form-group">
                                        <label class="sr-only" for="exampleInputPassword2">Qty</label>
                                        <input type="text" class="form-control" id="exampleInputPassword2" placeholder="Enter Qty" name="scanqty">
                                    </div>
                                    
									  <div class="form-group">
                                        <label class="sr-only" for="exampleInputPassword2">Scan Barcode</label>
                                        <input type="text" class="form-control" name="scanbarcode" id="scanbarcode" placeholder="Scan Barcode">
                                    </div>
<input type="submit" value="click" name="submit"  placeholder="Scan Barcode" style="visibility:hidden" />															
   <?php
function display()
{
}
if(isset($_POST['submit']))
{
   display();
} 
?>
              <span style="color:#F00; font-weight:bold;"> <?php echo $errorbarcode; ?></span>
                                    
								 <hr>  <span style="color:#F00; font-weight:bold;"> <?php echo $errorbalance; ?></span><br>
								  <div class="form-group">
                                        <label class="sr-only" for="exampleInputPassword2">Product</label>
<select class="select2" data-placeholder="Select Product/Service" style="width:500px;" onChange="choses()" id="proname" name="proname">
											   <?php if($_POST['hdequipname']==""){?>
                      <option value="" selected="selected">Select Product / Service</option>
                      <?php }else{?>
                      <option value="<?php echo $_POST['hdequipname'];?>" selected="selected"><?php echo $_POST['hdequipname'];?></option>
                      <?php }?>
                                            <?php $sql="select mp_name,mp_code,mp_selling,mp_size from m_product GROUP BY mp_code";
											$result_ma_services=mysql_query($sql) or die(mysql_error());
											while($rows=mysql_fetch_array($result_ma_services)) {extract($rows); ?>
											<option value="<?php echo $mp_code; ?>"><?php echo $mp_name; ?>/ <?php echo $mp_size; ?>(<?php echo $mp_code; ?>)</option><?php } ?>
											  
											  </select>                                     </div>
                                                       <input name="hdequipname" type="hidden" id="hdequipname" value="<?php echo $_POST['hdequipname']; ?>" />

									 
                                    <div class="form-group">
                                        <label class="sr-only" for="exampleInputPassword2">Qty</label>
                                        <input type="text" class="form-control" id="exampleInputPassword2" placeholder="Enter Qty" name="proqty" onkeypress="return isNumberKey(event,this)" >
                                    </div>
<?php if($csuper==1 || $cadmin==1) { ?>     <div class="form-group">
                                        <label class="sr-only" for="exampleInputPassword2">Branch</label>
                                        <select  class="form-control" id="exampleInputPassword2"  name="sbranch">
                                        <?php $sql="select bname from cbranch"; $result_selctbranch=mysql_query($sql) or die(mysql_error());
										while($rows=mysql_fetch_array($result_selctbranch)) {extract($rows);  ?>
                                        
                                        <option value="<?php echo $bname; ?>"><?php echo $bname; ?></option>
                                        <?php } ?>
                                        
                                        
                                        </select>
                                    </div>   <?php } ?>
                                    
                                    
                                    <?php if($csuper!=1  || $cadmin!=1) { ?>  
                                    
                                    <input value="<?php echo $_SESSION['cbranch']; ?>" name="sbranch" type="hidden">
                                    
                                    <?php } ?>                                 
									  <div class="form-group">
                                        <label class="sr-only" for="exampleInputPassword2">Code</label>
                                        <input type="hidden" class="form-control" id="prorate" placeholder="Enter Rate" name="prorate"  value="<?php echo $_POST['prorate']; ?>">
                                    </div>
									                                    <input type="submit" class="btn btn-success m-l-10" name="add" id="add" value="Add">
																		<hr>
																		
																		<div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Product</th>
                                                        <th>Qty</th>
                                                        <th>Rate</th>
                                                        <th>Size</th>
                                                        <th>Amount</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                 <?php while($rows=mysql_fetch_array($result_temp)) {extract($rows);
												 $_POST['cname']=$mat;
												 $_POST['proname2']=$mat_service;
												 $_POST['proqty2']=$mat_qty;
												 $_POST['prorate2']=$mat_rate;
												 $_POST['proamount2']=$mat_amount;
												 $_POST['prosize2']=$mat_size;
												  ?>   <tr>
                                                        <td><input type="checkbox" name="cname" value="<?php echo $mat; ?>"> </td>
                                                        <td><input type="text" name="proname2[]" value="<?php echo $_POST['proname2']; ?>" style="width:200px;"></td>
                                                        <td><input type="text" name="proqty2[]" value="<?php echo $_POST['proqty2']; ?>" style="width:50px;"></td>
                                                        <td><input type="text" name="prorate2[]" value="<?php echo $_POST['prorate2']; ?>" style="width:80px;"></td>
                                                                                                                <td><input type="text" name="prosize[]" value="<?php echo $_POST['prosize2']; ?>" style="width:80px;"></td>

                                                        <td><input type="text" name="proamount2[]" value="<?php echo $_POST['proamount2']; ?>" style="width:80px;"></td>
                                                        <td><input type="submit" name="delete" value="Delete"></td>
                                                    </tr><?php } ?>
													</tbody>
													</table></div>
													<input value="<?php echo $count_all; ?>" type="hidden" name="troww">
                                                    Total Qty: <?php echo $stcount; ?>

                                </form>
								<hr>
								                        <form class="form-horizontal" name="form2" id="form2" action="" method="post">
														  
														   <div class="form-group" id="myRadioGroup">
                                        <label for="inputEmail3" class="col-sm-4 control-label"></label>
                                        <div class="col-sm-7">
                                            <label class="radio-inline c-radio">
                                 <input  type="radio" name="stype" value="new" required>
                               New Customer</label>
                              <label class="radio-inline c-radio">
                                 <input type="radio"  name="stype" value="existing" required>
                               Existing Customer</label>                                        
                                        </div>
										<p></p><br>
										<p></p><br>

                                   <div   id="existing" class="desc">
								                           <div class="form-group">

                                        <label for="inputEmail3" class="col-sm-4 control-label">Client Name</label>
                                        <div class="col-sm-7">
<select class="select2" data-placeholder="Select Product/Service" style="width:500px;" id="clname" name="clname" >
											   <?php if($_POST['clname']==""){?>
                      <option value="" selected="selected">Select Client Information</option>
                      <?php }else{?>
                      <option value="<?php echo $_POST['clname'];?>" selected="selected"><?php echo $_POST['clname'];?></option>
                      <?php }?>
                                            <?php $sql="select * from client_profile";
											$result_ma_client_profile=mysql_query($sql) or die(mysql_error());
											while($rows=mysql_fetch_array($result_ma_client_profile)) {extract($rows); ?>
											<option value="<?php echo $clid ; ?>"><?php echo $client_lastname; ?> <?php echo $client_firstname; ?> (<?php echo $client_tele; ?>)</option><?php } ?>
											  
											  </select> 										                                            <input type="hidden" class="form-control" id="inputEmail3" name="cemailadd" required value="<?php echo $_POST['cemailadd']; ?>" readonly>
                                          <input type="hidden" class="form-control" id="inputEmail3"  name="ctelephone" required value="<?php echo $_POST['ctelephone']; ?>" readonly>

                                        </div>
                                    </div></div>
									
									
									<div id="new" class="desc">
							 
							 
							
                        <div class="form-group">
    <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                                        <div class="col-sm-7">                                 <input id="input-id-1" name="gname"type="text" placeholder="Enter Customer Full Name" class="form-control" >
                           </div>
                        </div>
            
                        <div class="form-group">
    <label for="inputEmail3" class="col-sm-4 control-label">Customer Email</label>
                                        <div class="col-sm-7">                                 <input id="gemail" name="gemail" type="text" placeholder="Enter Customer Email" class="form-control"  >										   <span id="code_status" class="Estilo1"></span>										  

                           </div>
                        </div>
                 
                        <div class="form-group">
     <label for="inputEmail3" class="col-sm-4 control-label">Customer Tel</label>
                                        <div class="col-sm-7">                              <input id="input-id-1" name="gtel" type="text" placeholder="Enter Customer Telephone" class="form-control">  
                           </div>
						
                        </div>
                   
                       
                   </div>
				   
				   
										 <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-4 control-label">Total</label>
                                        <div class="col-sm-7">
                                          <input type="text" class="form-control" id="inputEmail3"  name="lappoint" value="<?php echo $sum; ?>" readonly>                                          <input type="hidden" class="form-control" id="inputEmail3"  name="ttotal" value="<?php echo $su; ?>" readonly>    
                                          
<input type="hidden" class="form-control" id="inputEmail3"  name="profit" value="<?php echo $pro; ?>" readonly>                                        
                                        </div>
                                    </div>
									
                                     <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-4 control-label">Sales Discount</label>
                                        <div class="col-sm-7">
                              <input type="text" placeholder="Enter Discounted Amount" class="form-control" name="discount" >
                                        </div>
                                    </div>
									 
								
										 <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-4 control-label">Cash</label>
                                        <div class="col-sm-7">
                              <input type="text" placeholder="Enter Cash Amount Receive" class="form-control" name="cpaid">
                                        </div>
                                    </div>
									 
									   <div class="form-group">
  <label for="inputEmail3" class="col-sm-4 control-label">POS Method</label>
                                        <div class="col-sm-7">
							  <select name="poffice" id="poffice" class="form-control" >
							  							  <option value="">Select POS</option>

							  <?php $sql="select * from office_details";
							  $result_getbanck=mysql_query($sql) or die(mysql_error());
							  while($rows=mysql_fetch_array($result_getbanck)) {extract($rows);
							  						    
							  ?>
							  <option value="<?php echo $office_id; ?>"><?php echo $office_accname; ?>/<?php echo $office_id; ?>(<?php echo $office; ?>)</option>
							  <?php } ?>
							  
							  </select>
							   <div style='display:none;' id='ppaid'>
<br/>
    <input type='text' class="form-control" name='ppaid'  placeholder="POS Amount" />
    <br/>
</div>
							  
							   <span class="Estilo1" ><?php echo $error; ?> <?php echo $pos1; ?></span>
							   <span id="user_status" class="Estilo1"></span>
</div>
                           </div>
						   
						 
						   
						  
                            </div><!-- panel-body -->
                        </div> <!-- panel -->
                    </div> <!-- col-->
                    
                    <!-- Horizontal form -->
                  

                </div> <!-- End row -->

    
            </div>
            <!-- Page Content Ends -->
            <!-- ================== -->

            <!-- Footer Start -->
         <?php include("footer.php"); ?>
            <!-- Footer Ends -->



        </section>
        <!-- Main Content Ends -->
        
        <!-- js placed at the end of the document so the pages load faster -->
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/pace.min.js"></script>
        <script src="js/wow.min.js"></script>
        <script src="js/jquery.nicescroll.js" type="text/javascript"></script>


        <script src="assets/tagsinput/jquery.tagsinput.min.js"></script>
        <script src="assets/toggles/toggles.min.js"></script>
        <script src="assets/timepicker/bootstrap-timepicker.min.js"></script>
        <script src="assets/timepicker/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="assets/colorpicker/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="assets/jquery-multi-select/jquery.multi-select.js"></script>
        <script type="text/javascript" src="assets/jquery-multi-select/jquery.quicksearch.js"></script>
        <script src="assets/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/spinner/spinner.min.js"></script>
        <script src="assets/select2/select2.min.js" type="text/javascript"></script>


        <script src="js/jquery.app.js"></script>


	

    </body>
</html>
