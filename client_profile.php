<?php require_once('Connections/millwayconn.php'); ?>
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

$currentPage = $_SERVER["PHP_SELF"];

$clid_clientDetail = "0";
if (isset($_GET['clientID'])) {
  $clid_clientDetail = $_GET['clientID'];
}
mysql_select_db($database_millwayconn, $millwayconn);
$query_clientDetail = sprintf("SELECT tbl_client.client_ID, tbl_client.fname, tbl_client.lname, tbl_client.address1, tbl_client.address2, tbl_client.phoneNo, tbl_client.occupation, tbl_mill.millName FROM tbl_client JOIN tbl_mill ON tbl_client.millID=tbl_mill.millID WHERE tbl_client.client_ID=%s ORDER BY tbl_client.datemodified ", GetSQLValueString($clid_clientDetail, "int"));
$clientDetail = mysql_query($query_clientDetail, $millwayconn) or die(mysql_error());
$row_clientDetail = mysql_fetch_assoc($clientDetail);
$totalRows_clientDetail = mysql_num_rows($clientDetail);

$maxRows_sales = 10;
$pageNum_sales = 0;
if (isset($_GET['pageNum_sales'])) {
  $pageNum_sales = $_GET['pageNum_sales'];
}
$startRow_sales = $pageNum_sales * $maxRows_sales;

$clid_sales = "0";
if (isset($_GET['clientID'])) {
  $clid_sales = $_GET['clientID'];
}
mysql_select_db($database_millwayconn, $millwayconn);
$query_sales = sprintf("SELECT tss.saleID, tss.salesDate, tss.modifiedby, SUM( tsi.amount ) amt, GROUP_CONCAT( tsi.itemName ) items FROM tbl_sales tss JOIN ( SELECT itemName, amount, discount, saleID FROM tbl_sold_item JOIN tbl_item ON tbl_sold_item.itemNo=tbl_item.itemID) tsi ON tss.saleID = tsi.saleID WHERE tss.clientNo=%s GROUP BY tss.saleID ORDER BY tss.salesDate", GetSQLValueString($clid_sales, "int"));
$query_limit_sales = sprintf("%s LIMIT %d, %d", $query_sales, $startRow_sales, $maxRows_sales);
$sales = mysql_query($query_limit_sales, $millwayconn) or die(mysql_error());
$row_sales = mysql_fetch_assoc($sales);

if (isset($_GET['totalRows_sales'])) {
  $totalRows_sales = $_GET['totalRows_sales'];
} else {
  $all_sales = mysql_query($query_sales);
  $totalRows_sales = mysql_num_rows($all_sales);
}
$totalPages_sales = ceil($totalRows_sales/$maxRows_sales)-1;

$queryString_sales = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_sales") == false && 
        stristr($param, "totalRows_sales") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_sales = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_sales = sprintf("&totalRows_sales=%d%s", $totalRows_sales, $queryString_sales);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz | Client Transaction History</title>
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
          <h1>Client Transaction History</h1>
          <h2>Personal details</h2>
          <table width="0" border="0" cellspacing="2">
            <tr>
              <th scope="row">Names</th>
              <td><?php echo $row_clientDetail['fname']; ?><?php echo $row_clientDetail['lname']; ?></td>
            </tr>
            <tr>
              <th scope="row">Cient No:</th>
              <td><?php echo $row_clientDetail['client_ID']; ?></td>
            </tr>
            <tr>
              <th scope="row">Mill:</th>
              <td><?php echo $row_clientDetail['millName']; ?></td>
            </tr>
            <tr>
              <th scope="row">Address:</th>
              <td><?php echo $row_clientDetail['address1']; ?><?php echo $row_clientDetail['address2']; ?></td>
            </tr>
            <tr>
              <th scope="row">Phone no:</th>
              <td><?php echo $row_clientDetail['phoneNo']; ?></td>
            </tr>
            <tr>
              <th scope="row">Occupation:</th>
              <td><?php echo $row_clientDetail['occupation']; ?></td>
            </tr>
          </table>
          <hr />
          <h2>Transaction History</h2>
          <?php if ($totalRows_sales == 0) { // Show if recordset empty ?>
  <p>No transaction records found</p>
  <?php } // Show if recordset empty ?>
          <?php if ($totalRows_clientDetail > 0) { // Show if recordset not empty ?>
            <p>&nbsp;<?php echo ($startRow_sales + 1) ?>- <?php echo min($startRow_sales + $maxRows_sales, $totalRows_sales) ?> of <?php echo $totalRows_sales ?> total transactions</p>
            <table width="0" border="0" cellspacing="0" class="tbl_view" >
              <tr>
                <th scope="row">Receipt No.</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total Amount</th>
              </tr>
              <tr>
                <?php do { ?>
                  <td><a href="sales_detail.php?saleID=<?php echo $row_sales['saleID']; ?>" title="Details"><?php echo $row_sales['saleID']; ?></a></td>
                  <td><?php echo $row_sales['salesDate']; ?></td>
                  <td><a href="sales_detail.php?saleID=<?php echo $row_sales['saleID']; ?>" title="Details"><?php echo $row_sales['items']; ?></a></td>
                  <td><?php echo $row_sales['amt']; ?></td>
                  <?php } while ($row_sales = mysql_fetch_assoc($sales)); ?>
              </tr>
            </table>
            <p>&nbsp;<a href="<?php printf("%s?pageNum_sales=%d%s", $currentPage, 0, $queryString_sales); ?>">First</a> | <a href="<?php printf("%s?pageNum_sales=%d%s", $currentPage, max(0, $pageNum_sales - 1), $queryString_sales); ?>">Previous</a> | <a href="<?php printf("%s?pageNum_sales=%d%s", $currentPage, min($totalPages_sales, $pageNum_sales + 1), $queryString_sales); ?>">Next</a> | <a href="<?php printf("%s?pageNum_sales=%d%s", $currentPage, $totalPages_sales, $queryString_sales); ?>">Last</a></p>
            <?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($clientDetail);

mysql_free_result($sales);
?>
