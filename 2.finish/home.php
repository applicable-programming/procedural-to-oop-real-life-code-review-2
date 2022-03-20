<?php 
ini_set("display_errors","off");
session_start();
 
include("mfile.php");


$ms=date('d-m');
$sql="select * from client_profile where client_birthday='$ms' ";
$result_get_admin_users=mysql_query($sql) or die(mysql_error());
$counts=mysql_num_rows($result_get_admin_users);


$d=date('d')-1;
$mY=date('Y-m');
$date=$mY.'-'.'0'.$d;
$tday=date('Y-m-d');

$sql="select * from book_appointment where next_appointment > '$tday'";
$result_get_book=mysql_query($sql) or die(mysql_error());
$count_on_book=mysql_num_rows($result_get_book);


$sql="select * from book_appointment where next_appointment < '$date'";
$result_off_book=mysql_query($sql) or die(mysql_error());
$count_off_book=mysql_num_rows($result_off_book);

$mydate=date('Y-m-d');

$sql="select * from book_log where m_date='$mydate'";
$result_total_count=mysql_query($sql) or die(mysql_error());
$count_applog=mysql_num_rows($result_total_count);



$chm=date('m-Y');

$product = new Product();
$result_check_record = $result_check_record = $product->selectProducts($chm, $sbranch);


$qry = mysql_query(" SELECT SUM(mp_balance) AS  mqty  from m_product where mp_branch='$sbranch'");
    $row = mysql_fetch_assoc($qry);
    $su= number_format($row['mqty']);
	
	
	
$sql="select * from ma_sales where masl_bal<1 and masl_branch='$sbranch'";
$result_completed=mysql_query($sql) or die(mysql_error());
$count_invoice=mysql_num_rows($result_completed);

	
$sql="select * from ma_sales where masl_bal>0 and masl_branch='$sbranch'";
$result_uncompleted=mysql_query($sql) or die(mysql_error());
$uncount_invoice=mysql_num_rows($result_uncompleted);


 $qry = mysql_query(" SELECT SUM(mp_tvalue) AS sellingvalue from m_product where mp_branch='$sbranch'");
    $row = mysql_fetch_assoc($qry);
    $sup= $row['sellingvalue'];
	$sema=number_format($sup);
	
	
	$sql="select reorder as lowstock from cbranch where bname='$sbranch'";
	$result_order=mysql_query($sql) or die(mysql_error());
	while($rows=mysql_fetch_array($result_order)){extract($rows);
	}
	


$sql="select * from client_profile";
$result_get_client=mysql_query($sql) or die(mysql_error());
$countj=mysql_num_rows($result_get_client);

?>

<?php include("header.php"); ?>

            <!-- Page Content Start -->
            <!-- ================== -->

            <div class="wraper container-fluid">
                <div class="page-title"> 
                    <h3 class="title">Welcome <?php echo $first_name; ?>!</h3> 
                </div>



                <div class="row">
                   <?php if($csuper=="1"){ ?> <div class="col-lg-3 col-sm-6">
                      <a href="productlog.php" target="_blank"> <div class="widget-panel widget-style-2 white-bg">
                            <h2 class="m-0 counter">=N=<?php echo $sema; ?></h2>
                            <div>Stock Value</div>
                        </div>
                    </div></a><?php } ?>
                   <a href="invoicestatus.php?status=Completed" target="_blank"> <div class="col-lg-3 col-sm-6">
                        <div class="widget-panel widget-style-2 white-bg">
                            <h2 class="m-0 counter"><?php echo $count_invoice; ?></h2>
                            <div>Completed Sales</div>
                        </div>
                    </div></a>
                 <a href="invoicestatus.php?status=Uncompleted" target="_blank">   <div class="col-lg-3 col-sm-6">
                        <div class="widget-panel widget-style-2 white-bg">
                            <h2 class="m-0 counter"><?php echo $uncount_invoice; ?></h2>
                            <div>Uncompleted Sales</div>
                        </div>
                    </div></a>
                  <a href="productlog.php" target="_blank">  <div class="col-lg-3 col-sm-6">
                        <div class="widget-panel widget-style-2 white-bg">
                        <h2 class="m-0 counter"><?php echo $su; ?></h2>
                            <div>Stock Available</div>
                        </div>
                    </div></a>
                </div> <!-- end row -->

 



               <?php if($nstype=="Product") { ?> <div class="row">
                    

                    <div class="col-lg-12">

                        <div class="portlet"><!-- /primary heading -->
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark text-uppercase">
                                 Low Stock Level(s)
                                </h3>
                                <div class="portlet-widgets">
                                    <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                                    <span class="divider"></span>
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#portlet2"><i class="ion-minus-round"></i></a>
                                    <span class="divider"></span>
                                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="portlet2" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                       <table id="datatable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
													<th> Name</th>
     													<th> Size</th>
             
     													<th>Quantity</th>
     													<th> Branch</th>

                                                </tr>
                                            </thead>

                                     
                                            <tbody>
                                           <?php
										   
										   
										   $sql="select * from  m_product where  	mp_balance<mp_reorder"; 
										   
										   $result_getlowstock=mysql_query($sql) or die(mysql_error()); while($rows=mysql_fetch_array($result_getlowstock)) {extract($rows); ?>     <tr>
                                                  
													                                                    <td><?php echo $mp_name; ?></td>
													                                                    <td><?php echo $mp_size; ?></td>

                                              													                                                    <td><?php 

echo $mp_balance; 

?> </td>

                                              													                                                    <td><?php echo $mp_branch; ?></td>


                                                </tr><?php } ?>
                                               
                                              
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->

                    
                </div> <!-- End row --><?php } ?>


                <div class="row">
                    <div class="col-lg-3">

                      <div class="panel panel-default">
                            <div class="panel-body"><a href="pos.php"><img src="img/post sales.png" style="width:200px; height:80px;"></a>							
                            </div> <!-- panel-body -->
							
                        </div> <!-- panel -->
                    </div> <!-- end col -->
					     <div class="col-lg-3">

                      <div class="panel panel-default">
                            <div class="panel-heading"><h3 class="panel-title">There <?php if($counts<2){ ?> is <?php } ?> <?php if($counts>2){ ?> are <?php } ?> <?php echo $counts; ?> <i class="fa fa-birthday-cake"></i> Birthday Today</h3></div>
                            <div class="panel-body">
							   <div class="panel-heading"></div>
                             <table id="" class="table table-striped table-bordered">
                                            <thead>
                                                
                                            </thead>

                                     
                                            <tbody>
                                           <?php while($rows=mysql_fetch_array($result_get_admin_users)) {extract($rows); ?>     <tr>
                                                
													                                                    <td>Today is  <?php echo $client_lastname; ?> <?php echo $client_firstname; ?>'s <i class="fa fa-birthday-cake"></i> Birthday </td>



                                                </tr><?php } ?>
                                               
                                              
                                            </tbody>
                                        </table>
                            </div> <!-- panel-body -->
							
                        </div> <!-- panel -->
                    </div> <!-- end col -->

                    <div class="col-lg-6">

                        <div class="portlet"><!-- /primary heading -->
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark text-uppercase">
                                 Top Selling Product(s)
                                </h3>
                                <div class="portlet-widgets">
                                    <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                                    <span class="divider"></span>
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#portlet2"><i class="ion-minus-round"></i></a>
                                    <span class="divider"></span>
                                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="portlet2" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                       <table id="datatable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
													<th> Name</th>
     													<th> Size</th>
             
     													<th>Quantity</th>
     													<th> Branch</th>

                                                </tr>
                                            </thead>

                                     
                                            <tbody>
                                           <?php while($rows=mysql_fetch_array($result_check_record)) {extract($rows); ?>     <tr>
                                                  
													                                                    <td><?php echo $tp_name; ?></td>
													                                                    <td><?php echo $tp_size; ?></td>

                                              													                                                    <td><?php 

echo $tp_quantity; 

?> </td>

                                              													                                                    <td><?php echo $tp_branch; ?></td>


                                                </tr><?php } ?>
                                               
                                              
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->

                    
                </div> <!-- End row -->
				
				
				
				
				
            
            </div>
            <!-- Page Content Ends -->
            <!-- ================== -->

          <?php include("footer.php"); ?>
            <!-- Footer Ends -->



        </section>
        <!-- Main Content Ends -->
        



        <!-- js placed at the end of the document so the pages load faster -->
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/pace.min.js"></script>
        <script src="js/modernizr.min.js"></script>
        <script src="js/wow.min.js"></script>
        <script src="js/jquery.nicescroll.js" type="text/javascript"></script>


        <script src="js/jquery.app.js"></script>
   <script src="assets/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/datatables/dataTables.bootstrap.js"></script>


        <script type="text/javascript">
            $(document).ready(function() {
                $('#datatable').dataTable();
            } );
        </script>
    

    </body>
</html>
