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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE tbl_item SET itemName=%s, charge=%s WHERE itemID=%s",
                       GetSQLValueString($_POST['itemName'], "text"),
                       GetSQLValueString($_POST['charge'], "int"),
                       GetSQLValueString($_POST['itemID'], "int"));

  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($updateSQL, $millwayconn) or die(mysql_error());

  $updateGoTo = "listItems.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsEditItem = "SELECT * FROM tbl_item WHERE tbl_item.itemID=".$_GET['itemID'];
$rsEditItem = mysql_query($query_rsEditItem, $millwayconn) or die(mysql_error());
$row_rsEditItem = mysql_fetch_assoc($rsEditItem);
$totalRows_rsEditItem = mysql_num_rows($rsEditItem);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millway | Edit Item</title>
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
          <h1>Edit Item</h1>
          <p><a href="listItems.php">Back</a></p>
          <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
          <center>
            <table width="0" border="0" cellspacing="0" id="tbl_capture">
              <tr>
                <th scope="row"><label for="item2">Item:</label></th>
                <td><span id="sprytextfield1">
                  <input name="itemName" type="text" class="frm_fld" id="item2" value="<?php echo $row_rsEditItem['itemName']; ?>" />
                  <br />
                  <span class="textfieldRequiredMsg">Item is required.</span></span></td>
              </tr>
              <tr>
                <th scope="row"><label for="charge">Charge (per kg):</label></th>
                <td><span id="sprytextfield2">
                  <input name="charge" type="text" class="frm_fld" id="charge" value="<?php echo $row_rsEditItem['charge']; ?>" />
                  <br />
                  <span class="textfieldRequiredMsg">Rate is required.</span><span class="textfieldInvalidFormatMsg">Enter a number.</span></span></td>
              </tr>
              <tr>
                <th scope="row"><input name="itemID" type="hidden" id="itemID" value="<?php echo $row_rsEditItem['itemID']; ?>" /></th>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <th scope="row">&nbsp;</th>
                <td><input type="submit" name="button" id="button" value="Submit" /></td>
              </tr>
            </table>
            </center>
          <input type="hidden" name="MM_update" value="form1" />
          </form>
          <p>&nbsp;</p>
          <p>&nbsp;</p>
        <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer");
        </script>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsEditItem);
?>
