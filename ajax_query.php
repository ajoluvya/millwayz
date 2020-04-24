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
if (isset($_GET['clntId'])&&strlen($_GET['clntId'])>0) {
	if(isset($_GET['origin'])&&$_GET['origin']=="frontdesk"){
		$clntId_rsClient = $_GET['clntId'];
		mysql_select_db($database_millwayconn, $millwayconn);
		$query_rsClient = sprintf("SELECT tbl_client.fname, tbl_client.lname, tbl_client.phoneNo FROM tbl_client WHERE tbl_client.client_ID=%s", GetSQLValueString($clntId_rsClient, "text"));
		$rsClient = mysql_query($query_rsClient, $millwayconn) or die(mysql_error());
		$row_rsClient = mysql_fetch_assoc($rsClient);
		$totalRows_rsClient = mysql_num_rows($rsClient);
		
		$message="";
		if($totalRows_rsClient>0)
		$message="$totalRows_rsClient Names: ".$row_rsClient['fname']." ".$row_rsClient['lname']."  Phone: ".$row_rsClient['phoneNo'];
		else
		$message="$totalRows_rsClient Client not found in records, please correct client No<br/> or <a href='regClient.php'>register</a> the client in order to proceed";
		echo $message;
		mysql_free_result($rsClient);
	}
	if(isset($_GET['origin'])&&$_GET['origin']=="discount"&&isset($_GET['itemID'])&&isset($_GET['tdaysweight'])){
		$itemID=$_GET['itemID'];
		
		mysql_select_db($database_millwayconn, $millwayconn);
		$query_rsTWeight="SELECT SUM(tbl_sold_item.weight) Tweight FROM tbl_sales JOIN tbl_sold_item ON tbl_sales.saleID=tbl_sold_item.saleID WHERE  tbl_sales.clientNo=".$_GET['clntId']." AND tbl_sold_item.itemNo=$itemID";
		$rsTWeight = mysql_query($query_rsTWeight, $millwayconn) or die(mysql_error());
		$row_rsTWeight = mysql_fetch_assoc($rsTWeight);
		$totalRows_rsTWeight = mysql_num_rows($rsTWeight);
		//the total weight accumulated so far by the client
		$cumulative_weight=isset($row_rsTWeight['Tweight'])?$row_rsTWeight['Tweight']+$_GET['tdaysweight']:$_GET['tdaysweight'];
		
		mysql_select_db($database_millwayconn, $millwayconn);
		$query_rsDiscount = "SELECT rate FROM tbl_discount WHERE $cumulative_weight BETWEEN st_weight AND end_weight AND tbl_discount.itemID=$itemID";
		$rsDiscount = mysql_query($query_rsDiscount, $millwayconn) or die(mysql_error());
		$row_rsDiscount = mysql_fetch_assoc($rsDiscount);
		$totalRows_rsDiscount = mysql_num_rows($rsDiscount);
		
		echo isset($row_rsDiscount['rate'])?$row_rsDiscount['rate']:0;
		
		mysql_free_result($rsTWeight);
		mysql_free_result($rsDiscount);
	}
}
?>