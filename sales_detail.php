<?php require_once('Connections/millwayconn.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Supervisor";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php?loginMsg=Access denied.";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$hde_rsSaleDetail = "0";
if (isset($_GET['saleID'])) {
  $hde_rsSaleDetail = $_GET['saleID'];
}
$gte_rsSaleDetail = "0";
if (isset( $_SESSION['millID'])) {
  $gte_rsSaleDetail =  $_SESSION['millID'];
}
mysql_select_db($database_millwayconn, $millwayconn);
$query_rsSaleDetail = sprintf("SELECT tss.clientNo, tss.salesDate, tss.modifiedby, tsi.discount, tsi.weight, tsi.amount, tsi.itemName FROM tbl_sales tss JOIN ( SELECT itemName, weight, itemNo, amount, discount, saleID FROM tbl_sold_item JOIN tbl_item ON itemNo = itemID )tsi ON tss.saleID = tsi.saleID WHERE tss.millBranch=%s AND tss.saleID=%s", GetSQLValueString($gte_rsSaleDetail, "int"),GetSQLValueString($hde_rsSaleDetail, "int"));
$rsSaleDetail = mysql_query($query_rsSaleDetail, $millwayconn) or die(mysql_error());
$row_rsSaleDetail = mysql_fetch_assoc($rsSaleDetail);
$totalRows_rsSaleDetail = mysql_num_rows($rsSaleDetail);

$fde_sale_front_desk_entry = "0";
if (isset($_GET['saleID'])) {
  $fde_sale_front_desk_entry = $_GET['saleID'];
}
mysql_select_db($database_millwayconn, $millwayconn);
$query_front_desk_entry = sprintf("SELECT tss.clientNo, tss.modifiedby, fde.weight, fde.discount, fde.itemName FROM tbl_sales tss JOIN ( SELECT itemName, weight, item, discount, saleID FROM tbl_frontdeskentry JOIN tbl_item ON item = itemID )fde ON tss.saleID = fde.saleID WHERE tss.saleID=%s", GetSQLValueString($fde_sale_front_desk_entry, "int"));
$front_desk_entry = mysql_query($query_front_desk_entry, $millwayconn) or die(mysql_error());
$row_front_desk_entry = mysql_fetch_assoc($front_desk_entry);
$totalRows_front_desk_entry = mysql_num_rows($front_desk_entry);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz | Sales detail</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="CSS/default.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="print" href="CSS/print.css"/>
</head>

<body>
<div class="wrapOverall">
    
        <div class="header">
          <img class="logo" src="imgs/header_logo.png" width="279" height="52" alt="millways" />
          <div class="navMain">
                    <ul>
                     <li><a href="redirect.php">Home</a></li>
                     <li><a href="clients.php">Clients</a></li>
                     <li><a href="sales.php">Sales</a></li>
                     <li><a href="expenses.php">Expenses</a></li>
                     <li><a href="logout.php">Logout</a></li>
                    </ul>
                
          </div><!--END navMain-->
        
        </div>
        
        <div class="content"><!-- InstanceBeginEditable name="Content" -->
        <h1>Sales detail</h1>
        <p>
          <?php if ($totalRows_rsSaleDetail == 0) { // Show if recordset empty ?>
            Invalid  request
                <?php } // Show if recordset empty ?>
        </p>
        <p><a href="sales.php">Back</a></p>
        <?php if ($totalRows_rsSaleDetail > 0) { // Show if recordset not empty ?>
  <p><strong>Receipt No:</strong> <a href="sales_detail.php?saleID=<?php echo $_GET['saleID']; ?>" title="Details"> <?php echo $_GET['saleID']; ?></a> <strong>Date:</strong> <?php echo $row_rsSaleDetail['salesDate']; ?> Cl<strong>ient No:</strong> <?php echo $row_rsSaleDetail['clientNo']; ?></p>
  <h3 align="left">First Entry:</h3>
  <table width="0" border="0" cellspacing="0" class="tbl_view">
    <tr>
      <th scope="col">&nbsp;</th>
      <th scope="col">Item</th>
      <th scope="col">Weight</th>
      <th scope="col">Discount</th>
    </tr>
          <?php 
		  $total_weight= $total_amount = $total_discount=0;
		  ?>
    <?php do { ?>
    <tr>
      <td>&nbsp;</td>
      <td><?php echo $row_front_desk_entry['itemName']; ?></td>
      <td><?php echo $row_front_desk_entry['weight']; $total_weight+=$row_front_desk_entry['weight']; ?></td>
      <td><?php echo $row_front_desk_entry['discount']; $total_discount+=$row_front_desk_entry['discount']; ?></td>
    </tr>
    <?php } while ($row_front_desk_entry = mysql_fetch_assoc($front_desk_entry)); ?>
            <tr>
              <th>TOTALS</th>
              <th>&nbsp;</th>
              <th><?php echo $total_weight; ?></th>
              <th><?php echo $total_discount; ?></th>
            </tr>
  </table>
  <br />
  <hr />
  <h3 align="left">Second Entry:</h3>
        <table width="0" border="0" cellspacing="0" class="tbl_view">
            <tr>
              <th scope="col">&nbsp;</th>
              <th scope="col">Item</th>
              <th scope="col">Weight</th>
              <th scope="col">Amount</th>
              <th scope="col">Discount</th>
          </tr>
          <?php 
		  $total_weight= $total_amount = $total_discount=0;
		  ?>
            <?php do { ?>
            <tr>
              <td>&nbsp;</td>
                <td><?php echo $row_rsSaleDetail['itemName']; ?></td>
                <td><?php echo $row_rsSaleDetail['weight']; $total_weight+=$row_rsSaleDetail['weight']; ?></td>
                <td><?php echo $row_rsSaleDetail['amount']; $total_amount+=$row_rsSaleDetail['amount']; ?></td>
                <td><?php echo $row_rsSaleDetail['discount']; $total_discount+=$row_rsSaleDetail['discount'];?></td>
            </tr>
              <?php } while ($row_rsSaleDetail = mysql_fetch_assoc($rsSaleDetail)); ?>
            <tr>
              <th>TOTALS</th>
              <th>&nbsp;</th>
              <th><?php echo $total_weight; ?></th>
              <th><?php echo $total_amount; ?></th>
              <th><?php echo $total_discount; ?></th>
            </tr>
        </table>
          <?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
  <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsSaleDetail);

mysql_free_result($front_desk_entry);
?>
