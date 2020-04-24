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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsSales1 = 30;
$pageNum_rsSales1 = 0;
if (isset($_GET['pageNum_rsSales1'])) {
  $pageNum_rsSales1 = $_GET['pageNum_rsSales1'];
}
$startRow_rsSales1 = $pageNum_rsSales1 * $maxRows_rsSales1;

$gte_rsSales1 = "0";
if (isset($_SESSION['millID'])) {
  $gte_rsSales1 = $_SESSION['millID'];
}

$monthCheck="AND MONTH(tss.salesDate) = MONTH(CURDATE())";

if(isset($_GET['month'])&&strlen($_GET['month'])>0){
	$monthCheck="AND MONTH(tss.salesDate) = '".$_GET['month']."'";
}
if((isset($_GET['date1'])&&strlen($_GET['date1'])>0)&&(isset($_GET['date2'])&&strlen($_GET['date2'])>0)){
	$datePortion="BETWEEN '".$_GET['date1']."' AND  '".$_GET['date2']."'";
	$monthCheck="AND tss.salesDate $datePortion";
}

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsSales1 = sprintf("SELECT tss.saleID, tss.salesDate, tss.modifiedby, SUM(tsi.amount) amt, GROUP_CONCAT(tsi.itemName) items FROM 
                           tbl_sales tss JOIN ( SELECT itemName, amount, discount, saleID FROM tbl_sold_item JOIN tbl_item ON
						   tbl_sold_item.itemNo=tbl_item.itemID) tsi ON tss.saleID = tsi.saleID WHERE tss.millBranch=%s $monthCheck GROUP BY 
						   tss.saleID ORDER BY tss.salesDate", GetSQLValueString($gte_rsSales1, "int"));
$query_limit_rsSales1 = sprintf("%s LIMIT %d, %d", $query_rsSales1, $startRow_rsSales1, $maxRows_rsSales1);
$rsSales1 = mysql_query($query_limit_rsSales1, $millwayconn) or die(mysql_error());
$row_rsSales1 = mysql_fetch_assoc($rsSales1);


if (isset($_GET['totalRows_rsSales1'])) {
  $totalRows_rsSales1 = $_GET['totalRows_rsSales1'];
} else {
  $all_rsSales1 = mysql_query($query_rsSales1);
  $totalRows_rsSales1 = mysql_num_rows($all_rsSales1);
}
$totalPages_rsSales1 = ceil($totalRows_rsSales1/$maxRows_rsSales1)-1;

$queryString_rsSales1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsSales1") == false && 
        stristr($param, "totalRows_rsSales1") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsSales1 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsSales1 = sprintf("&totalRows_rsSales1=%d%s", $totalRows_rsSales1, $queryString_rsSales1);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz | Sales</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
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
          <h1>SALES REPORT</h1>
          <center>
    <form action="sales.php" method="get" name="search_form">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <th scope="row"><label for="frmsch3">Search for:</label></th>
          <th scope="row">&nbsp;</th>
          <th scope="row"><input name="frmsch" placeholder="any text" type="text" class="frm_fld" id="frmsch" value="<?php echo (isset($_GET['frmsch']))?$_GET['frmsch']:"";?>"/></th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th scope="row"><label for="month2">Month:</label></th>
          <th scope="row">&nbsp;</th>
          <td scope="row"><select name="month" id="month">
            <option value="1" <?php if (!(strcmp(1, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>January</option>
            <option value="2" <?php if (!(strcmp(2, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>February</option>
            <option value="3" <?php if (!(strcmp(3, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>March</option>
            <option value="4" <?php if (!(strcmp(4, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>April</option>
            <option value="5" <?php if (!(strcmp(5, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>May</option>
            <option value="6" <?php if (!(strcmp(6, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>June</option>
            <option value="7" <?php if (!(strcmp(7, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>July</option>
            <option value="8" <?php if (!(strcmp(8, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>August</option>
            <option value="9" <?php if (!(strcmp(9, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>September</option>
            <option value="10" <?php if (!(strcmp(10, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>October</option>
            <option value="11" <?php if (!(strcmp(11, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>November</option>
            <option value="12" <?php if (!(strcmp(12, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>December</option>
          </select></td>
          <td>From:</td>
          <td><span id="sprydate1">
            <input name="date1" type="text" id="date1" value="<?php if(isset($_GET['date1'])&&strlen($_GET['date1'])>0)echo $_GET['date1'];?>" />
            <span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldRequiredMsg">date is required.</span></span></td>
          <td><label for="date2">To:</label></td>
          <td><span id="sprydate2">
            <input name="date2" type="text" id="date2" value="<?php if(isset($_GET['date2'])&&strlen($_GET['date2'])>0) echo $_GET['date2'];?>" />
            <span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldRequiredMsg">date is required.</span></span></td>
        </tr>
        <tr>
          <th scope="row">&nbsp;</th>
          <th scope="row">&nbsp;</th>
          <th scope="row"><input type="submit" name="btn" id="btn" value="SEARCH" /></th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>           
    </form>
    </center>
    <p></p>
          <?php if ($totalRows_rsSales1 == 0) { // Show if recordset empty  
		  if(isset($_GET['frmsch'])||(isset($_GET['date1'])&&isset($_GET['date2']))||isset($_GET['month'])) {?><p>Sorry there are no records matching your search, try again</p><?php } else {?>
  <p>No sales records</p>
  <?php } } // Show if recordset empty ?>
          <?php if ($totalRows_rsSales1 > 0) { // Show if recordset not empty?>
            <p><?php echo ($startRow_rsSales1 + 1) ?> - <?php echo min($startRow_rsSales1 + $maxRows_rsSales1, $totalRows_rsSales1) ?> of <?php echo $totalRows_rsSales1 ?> total sales records</p>
    
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["table"]});
      google.setOnLoadCallback(drawTable);

      function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'Receipt No.');
        data.addColumn('string', 'Date');
        data.addColumn('string', 'Items');
        data.addColumn('number', 'Total Amount');
        data.addRows([
      <?php //do { ?>
          [{v: <a href="sales_detail.php?saleID=<?php echo $row_rsSales1['saleID']; ?>" title="Details"><?php echo $row_rsSales1['saleID']; ?></a>}, '<?php echo $row_rsSales1['salesDate']; ?>','<a href="sales_detail.php?saleID=<?php echo $row_rsSales1['saleID']; ?>" title="Details"><?php echo $row_rsSales1['items']; ?></a>',{v: <?php echo $row_rsSales1['amt']; ?>}],
          <?php //} while ($row_rsSales1 = mysql_fetch_assoc($rsSales1)); ?>
        ]);

        var table = new google.visualization.Table(document.getElementById('table_div'));

        table.draw(data, {showRowNumber: true, allowHtml: true, page:'enable'});
      }
    </script>
    <div id="table_div"></div>

          <table width="0" border="0" cellspacing="0" class="tbl_view">
              <tr>
                <th scope="row">Receipt No.</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total Amount</th>
              </tr>
              <tr>
                <?php do { ?>
                <td><a href="sales_detail.php?saleID=<?php echo $row_rsSales1['saleID']; ?>" title="Details"><?php echo $row_rsSales1['saleID']; ?></a></td>
                  <td><?php echo $row_rsSales1['salesDate']; ?></td>
                  <td><a href="sales_detail.php?saleID=<?php echo $row_rsSales1['saleID']; ?>" title="Details"><?php echo $row_rsSales1['items']; ?></a></td>
                  <td><?php echo $row_rsSales1['amt']; ?></td>
                  <?php } while ($row_rsSales1 = mysql_fetch_assoc($rsSales1)); ?>
              </tr>
            </table>
            <p>&nbsp;<a href="<?php printf("%s?pageNum_rsSales1=%d%s", $currentPage, 0, $queryString_rsSales1); ?>">First</a> | <a href="<?php printf("%s?pageNum_rsSales1=%d%s", $currentPage, max(0, $pageNum_rsSales1 - 1), $queryString_rsSales1); ?>">Previous</a> | <a href="<?php printf("%s?pageNum_rsSales1=%d%s", $currentPage, min($totalPages_rsSales1, $pageNum_rsSales1 + 1), $queryString_rsSales1); ?>">Next</a> | <a href="<?php printf("%s?pageNum_rsSales1=%d%s", $currentPage, $totalPages_rsSales1, $queryString_rsSales1); ?>">Last</a></p>
            <?php } // Show if recordset not empty ?>
        <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprydate2", "date", {format:"yyyy-mm-dd", useCharacterMasking:true, validateOn:["blur"], hint:"Eg: 2014-12-31", isRequired:false});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprydate1", "date", {validateOn:["blur"], format:"yyyy-mm-dd", hint:"Eg: 2010-01-31", useCharacterMasking:true, isRequired:false});
        </script>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsSales1);
?>
