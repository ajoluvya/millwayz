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

$MM_restrictGoTo = "index.php?loginMsg=Access denied, contact admin";
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

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsClientWaitList = "SELECT saleID , clientNo , salesDate , CONCAT(fname, ' ', lname) NAMES , phoneNo FROM tbl_sales JOIN tbl_client ON clientNo = client_ID WHERE millBranch=1 AND served=0 AND modifiedby IN (SELECT staff_id FROM tbl_staff WHERE role='Supervisor')";
$rsClientWaitList = mysql_query($query_rsClientWaitList, $millwayconn) or die(mysql_error());
$row_rsClientWaitList = mysql_fetch_assoc($rsClientWaitList);
$totalRows_rsClientWaitList = mysql_num_rows($rsClientWaitList);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz | Waiting Client List</title>
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
          <h1>Client waiting list</h1>
          <p>&nbsp;<?php echo $totalRows_rsClientWaitList ?>  Client[s] </p>
          <table width="0" border="0" cellspacing="0" class="tbl_view">
            <tr>
              <th scope="col">CLIENT NO</th>
              <th scope="col">CLIENT</th>
              <th scope="col">DATE</th>
              <th scope="col">PHONE</th>
            </tr>
              <?php do { ?>

            <tr>              <td><a href="serveReceivedCustomer.php?cid=<?php echo $row_rsClientWaitList['clientNo']; ?>&amp;names=<?php echo $row_rsClientWaitList['NAMES']; ?>&amp;slID=<?php echo $row_rsClientWaitList['saleID']; ?>"><?php echo $row_rsClientWaitList['clientNo']; ?></a></td>
              <td><a href="serveReceivedCustomer.php?cid=<?php echo $row_rsClientWaitList['clientNo']; ?>&amp;names=<?php echo $row_rsClientWaitList['NAMES']; ?>&amp;slID=<?php echo $row_rsClientWaitList['saleID']; ?>"><?php echo $row_rsClientWaitList['NAMES']; ?></a></td>
              <td><?php echo $row_rsClientWaitList['salesDate']; ?></td>
              <td><?php echo $row_rsClientWaitList['phoneNo']; ?></td>
            </tr>
                <?php } while ($row_rsClientWaitList = mysql_fetch_assoc($rsClientWaitList)); ?>
          </table>
          <p>&nbsp;</p>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsClientWaitList);
?>
