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

$maxRows_rsClients = 10;
$pageNum_rsClients = 0;
if (isset($_GET['pageNum_rsClients'])) {
  $pageNum_rsClients = $_GET['pageNum_rsClients'];
}
$startRow_rsClients = $pageNum_rsClients * $maxRows_rsClients;

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsClients = "SELECT tbl_client.client_ID, tbl_client.fname, tbl_client.lname, tbl_client.address1, tbl_client.address2, tbl_client.phoneNo, tbl_client.occupation, tbl_mill.millName FROM tbl_client JOIN tbl_mill ON tbl_client.millID=tbl_mill.millID ORDER BY tbl_client.datemodified";
$query_limit_rsClients = sprintf("%s LIMIT %d, %d", $query_rsClients, $startRow_rsClients, $maxRows_rsClients);
$rsClients = mysql_query($query_limit_rsClients, $millwayconn) or die(mysql_error());
$row_rsClients = mysql_fetch_assoc($rsClients);

if (isset($_GET['totalRows_rsClients'])) {
  $totalRows_rsClients = $_GET['totalRows_rsClients'];
} else {
  $all_rsClients = mysql_query($query_rsClients);
  $totalRows_rsClients = mysql_num_rows($all_rsClients);
}
$totalPages_rsClients = ceil($totalRows_rsClients/$maxRows_rsClients)-1;

$queryString_rsClients = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsClients") == false && 
        stristr($param, "totalRows_rsClients") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsClients = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsClients = sprintf("&totalRows_rsClients=%d%s", $totalRows_rsClients, $queryString_rsClients);
$query_rsClients = "SELECT tbl_client.client_ID, tbl_client.fname, tbl_client.lname, tbl_client.address1, tbl_client.address2, tbl_client.phoneNo, tbl_client.occupation, tbl_mill.millName FROM tbl_client JOIN tbl_mill ON tbl_client.millID=tbl_mill.millID ORDER BY tbl_client.datemodified";
$rsClients = mysql_query($query_rsClients, $millwayconn) or die(mysql_error());
$row_rsClients = mysql_fetch_assoc($rsClients);
$totalRows_rsClients = mysql_num_rows($rsClients);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz::..Clients</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="CSS/default.css" rel="stylesheet" type="text/css" />
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
          <h1>Clients</h1>
          <?php if ($totalRows_rsClients == 0) { // Show if recordset empty ?>
  <p>No records found</p>
  <?php } // Show if recordset empty ?>
<p><a href="regClient.php">Reg new client</a></p>
<?php if ($totalRows_rsClients > 0) { // Show if recordset not empty ?>
  <p><?php echo $totalRows_rsClients ?> total records. Displaying:&nbsp;<?php echo ($startRow_rsClients + 1) ?> - <?php echo min($startRow_rsClients + $maxRows_rsClients, $totalRows_rsClients) ?></p>
  <table width="0" border="0" cellspacing="0" id="tbl_view">
    <tr>
      <th scope="col">S/N</th>
      <th scope="col">CLIENTNO</th>
      <th scope="col">NAMES</th>
      <th scope="col">MILL</th>
      <th scope="col">ADDRESS1</th>
      <th scope="col">ADDRESS2</th>
      <th scope="col">PHONE NO</th>
      <th scope="col">OCCUPATION</th>
      <th scope="col">&nbsp;</th>
      <th scope="col">Edit</th>
    </tr>
    <tr>
      <?php do { ?>
        <td>&nbsp;</td>
        <td><?php echo $row_rsClients['client_ID']; ?></td>
        <td><?php echo $row_rsClients['fname']; ?> <?php echo $row_rsClients['lname']; ?></td>
        <td><?php echo $row_rsClients['millName']; ?></td>
        <td><?php echo $row_rsClients['address1']; ?></td>
        <td><?php echo $row_rsClients['address2']; ?></td>
        <td><?php echo $row_rsClients['phoneNo']; ?></td>
        <td><?php echo $row_rsClients['occupation']; ?></td>
        <td>Purchases</td>
        <td><a href="editClient.php?clientID=<?php echo $row_rsClients['client_ID']; ?>">Edit</a> | <a href="delete.php?clientID=<?php echo $row_rsClients['client_ID']; ?>">Delete</a></td>
        <?php } while ($row_rsClients = mysql_fetch_assoc($rsClients)); ?>
    </tr>
  </table>
  <p><?php echo $totalRows_rsClients ?> total records. Displaying:&nbsp;<?php echo ($startRow_rsClients + 1) ?> - <?php echo min($startRow_rsClients + $maxRows_rsClients, $totalRows_rsClients) ?></p>
  <p>&nbsp;<a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, 0, $queryString_rsClients); ?>">First</a> | <a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, max(0, $pageNum_rsClients - 1), $queryString_rsClients); ?>">Previous</a> | <a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, min($totalPages_rsClients, $pageNum_rsClients + 1), $queryString_rsClients); ?>">Next</a> | <a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, $totalPages_rsClients, $queryString_rsClients); ?>">Last</a></p>
  <?php } // Show if recordset not empty ?>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsClients);
?>
