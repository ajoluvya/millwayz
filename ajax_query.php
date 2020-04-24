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

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsStWeight = "SELECT tbl_discount.st_weigth FROM tbl_discount WHERE tbl_discount.itemID AND tbl_discount.st_weigth AND tbl_discount.st_weigth";
$rsStWeight = mysql_query($query_rsStWeight, $millwayconn) or die(mysql_error());
$row_rsStWeight = mysql_fetch_assoc($rsStWeight);
$totalRows_rsStWeight = mysql_num_rows($rsStWeight);
?>
<?php require_once('Connections/millwayconn.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Supervisor";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php?loginMsg=Access denied";
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
if (isset($_GET['clntId'])&&isset($_GET['origin'])) {
  $clntId_rsClient = $_GET['clntId'];
mysql_select_db($database_millwayconn, $millwayconn);
$query_rsClient = sprintf("SELECT tbl_client.fname, tbl_client.lname, tbl_client.phoneNo FROM tbl_client WHERE tbl_client.client_ID=%s", GetSQLValueString($clntId_rsClient, "text"));
$rsClient = mysql_query($query_rsClient, $millwayconn) or die(mysql_error());
$row_rsClient = mysql_fetch_assoc($rsClient);
$totalRows_rsClient = mysql_num_rows($rsClient);

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsEWeight = "SELECT tbl_discount.end_weigth FROM tbl_discount WHERE tbl_discount.itemID AND tbl_discount.end_weigth AND tbl_discount.end_weigth";
$rsEWeight = mysql_query($query_rsEWeight, $millwayconn) or die(mysql_error());
$row_rsEWeight = mysql_fetch_assoc($rsEWeight);
$totalRows_rsEWeight = mysql_num_rows($rsEWeight);

$message="";
if($totalRows_rsClient>0)
$message.="$totalRows_rsClient Names:".$row_rsClient['fname']." ".$row_rsClient['lname']."  Phone: ".$row_rsClient['phoneNo'];
else
$message.="$totalRows_rsClient Client not found in records, please correct client No<br/> or <a href='regClient.php'>register</a> the client in order to proceed";
echo $message;
mysql_free_result($rsClient);

mysql_free_result($rsEWeight);
}

mysql_free_result($rsStWeight);
?>