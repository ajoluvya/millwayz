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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO tbl_discount (itemID, rate, millID, st_weight, end_weight) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['itemID'], "int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_SESSION['millID'], "int"),
                       GetSQLValueString($_POST['st_weight'], "int"),
                       GetSQLValueString($_POST['end_weight'], "int"));

  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($insertSQL, $millwayconn) or die(mysql_error());

  $insertGoTo = "itemDetails.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

  $hfg_rsDiscount = $_GET['itemID'];
  $yht_rsDiscount = $_SESSION['millID'];
mysql_select_db($database_millwayconn, $millwayconn);
$query_rsDiscount = sprintf("SELECT tbl_discount.rate, tbl_discount.st_weight, tbl_discount.end_weight, tbl_discount.discountID FROM tbl_discount WHERE tbl_discount.itemID=%s AND tbl_discount.millID=%s", GetSQLValueString($hfg_rsDiscount, "int"),GetSQLValueString($yht_rsDiscount, "int"));
$rsDiscount = mysql_query($query_rsDiscount, $millwayconn) or die(mysql_error());
$totalRows_rsDiscount = mysql_num_rows($rsDiscount);

$ter_rsItem = "0";
if (isset($_GET['itemID'])) {
  $ter_rsItem = $_GET['itemID'];
}
mysql_select_db($database_millwayconn, $millwayconn);
$query_rsItem = sprintf("SELECT tbl_item.itemName, tbl_item.charge FROM tbl_item WHERE tbl_item.itemID=%s", GetSQLValueString($ter_rsItem, "int"));
$rsItem = mysql_query($query_rsItem, $millwayconn) or die(mysql_error());
$row_rsItem = mysql_fetch_assoc($rsItem);
$totalRows_rsItem = mysql_num_rows($rsItem);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz::..Item detail</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script type="text/javascript" src="SpryAssets/jquery.min.js"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function checkSearch(delValue){
				  var really=confirm("Are you sure about deleting this?\n"+delValue);
				  return really;
		}
</script>
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
          <h1>Item details</h1>
          <p><a href="listItems.php">Main list of items</a></p>
        <h2><?php echo $row_rsItem['itemName']; ?>, rate: Ushs    <?php echo $row_rsItem['charge']; ?> (per kg)</h2>
        <center>
        <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
        <table width="0" border="0" cellspacing="0" class="tbl_view">
          <tr>
            <td scope="col">&nbsp;</td>
            <th scope="col">START WEIGHT (kg)</th>
            <th scope="col">END WEIGHT (kg)</th>
            <th scope="col">DISCOUNT (Ushs)</th>
            <td scope="col">&nbsp;</td>
          </tr>
          <?php $cnt=1; while ($row_rsDiscount = mysql_fetch_assoc($rsDiscount)){ ?>
          <tr>
            <td>&nbsp;</td>
            <td><?php echo $row_rsDiscount['st_weight']; ?></td>
            <td><?php $end_weight=$row_rsDiscount['end_weight']; echo $end_weight; ?></td>
            <td><?php echo $row_rsDiscount['rate']; ?></td>
            <td><?php if($cnt++==$totalRows_rsDiscount) {?><a href="delete.php?itemID=<?php echo $_GET['itemID']; ?>&amp;discountID=<?php echo $row_rsDiscount['discountID']; ?>" onclick="return checkSearch('<?php echo $row_rsItem['itemName'].":  Start weight: ".$row_rsDiscount['st_weight']." , Ending weight: ".$row_rsDiscount['end_weight']; ?>');">Delete</a><?php }?></td>
          </tr>
            <?php } ?>
          <tr>
            <th>Add</th>
            <td><span id="sprytextfield2">
            <input name="st_weight" type="text" id="st_weight" value="<?php echo isset($end_weight)?($end_weight+1)."\" readonly=\"readonly":10; ?>"/>
            <br />
            <span class="textfieldRequiredMsg">This field is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed in this field.</span><span class="textfieldMinValueMsg">Start weight must be greater than the previous end weight.</span></span></td>
            <td>
              <input name="itemID" type="hidden" id="itemID" value="<?php echo $_GET['itemID']; ?>" />
              <span id="sprytextfield3">
            <input name="end_weight" type="text" id="end_weight" />
            <br />
            <span class="textfieldRequiredMsg">This field is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed in this field.</span><span class="textfieldMinValueMsg">End weight must be greater than the start weight.</span></span></td>
            <td><span id="sprytextfield1">
            <input name="rate" type="text" id="rate" />
            <br />
            <span class="textfieldRequiredMsg">This field is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed in this field.</span></span></td>
            <td>
              <input type="submit" name="button" id="button" value="Add" />
            </td>
          </tr>
        </table>
        <input type="hidden" name="MM_insert" value="form1" />
        </form>
        </center>
        <p>&nbsp;</p>
        <script type="text/javascript">
		var st_weight=<?php echo isset($end_weight)?($end_weight+2):11; ?>;
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer", {validateOn:["blur"], minValue:st_weight});
$(document).ready(function(){
	$("#st_weight").change(function(){
		if($(this).val()!="")
		st_weight=parseInt($(this).val())+1;
		sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer", {validateOn:["blur"], minValue:st_weight});
	});
});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {useCharacterMasking:true, minValue:<?php echo isset($row_rsDiscount['end_weight'])?($row_rsDiscount['end_weight']+1):10; ?>});
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {useCharacterMasking:true});
        </script>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsDiscount);

mysql_free_result($rsItem);
?>
