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
  foreach($_POST['itemNo'] as $cnt => $itemNo) {
	  $insertSQL = sprintf("INSERT INTO tbl_sold_item (saleID, itemNo, weight, amount, discount) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['saleID'], "int"),
                       GetSQLValueString($itemNo, "int"),
                       GetSQLValueString($_POST['weight'][$cnt], "int"),
                       GetSQLValueString($_POST['amount'][$cnt], "int"),
                       GetSQLValueString($_POST['subdiscount'][$cnt], "int"));

  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($insertSQL, $millwayconn) or die(mysql_error());

}
$updateSQL = sprintf("UPDATE tbl_sales SET modifiedby=%s, served=1 WHERE saleID=%s",
                       GetSQLValueString($_POST['modifiedby'], "int"),
                       GetSQLValueString($_POST['saleID'], "int"));
					   
  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($updateSQL, $millwayconn) or die(mysql_error());
  
  $insertGoTo = "waitingClientList.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsOrderedItems = "SELECT tbl_frontdeskentry.weight, tbl_item.itemID, tbl_item.itemName,(tbl_frontdeskentry.weight*tbl_item.charge) amount, discount, tbl_item.charge FROM tbl_frontdeskentry JOIN tbl_item ON tbl_frontdeskentry.item=tbl_item.itemID WHERE tbl_frontdeskentry.saleID=".$_GET['slID'];
$rsOrderedItems = mysql_query($query_rsOrderedItems, $millwayconn) or die(mysql_error());
$row_rsOrderedItems = mysql_fetch_assoc($rsOrderedItems);
$totalRows_rsOrderedItems = mysql_num_rows($rsOrderedItems);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz | Process Client Orders</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script type="text/javascript" src="SpryAssets/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
});
function showDiscount(inputID){
	if($("#weight").val()!=""&&$("#itemNo").val()!=""){
		$("#amount"+inputID).val(parseInt($("#charge"+inputID).val())*parseInt($("#weight"+inputID).val()));
		if($("#clientNo").val()!="")
		$.get("ajax_query.php",{clntId:$("#clientNo").val(),origin:"discount",tdaysweight:$("#weight"+inputID).val(),itemID:$("#itemNo"+inputID).val()},function(data,status){
			$("#subdiscount"+inputID).val(data);
			total();
			});	
	}
}
function total(){
	var amount=0;
	var discount=0;
	var totalweight=0;
		$.each($("input:text"),function(i,e) {
			if(e.name=="weight[]")
				totalweight+=parseInt(e.value);
			if(e.name=="amount[]")
				amount+=parseInt(e.value);
			if(e.name=="subdiscount[]")
				discount+=parseInt(e.value);
        });	
				$("#totalweight").val(totalweight);
				$("#total").val(amount);
				$("#discount").val(discount);
				$("#overaltotal").val(parseInt($("#total").val())-parseInt($("#discount").val()));
}
	function removeItem(rnum) {
		$("#rowNum"+rnum).remove();
	}
</script>
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
          <h1>Client orders [<?php echo $_GET['names'];?>]</h1>
        <p><a href="waitingClientList.php">Waiting list</a></p>
          <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
          <center>
            <table width="0" border="0" cellspacing="0" id="tbl_capture">
              <tr>
                <th colspan="2" scope="row"><label for="clientNo">Client No:</label></th>
                <td colspan="2"><span id="sprytextfield1">
                  <input name="clientNo" type="text" class="frm_fld" id="clientNo" value="<?php echo $_GET['cid']; ?>" readonly="readonly" />
                <br /><span class="textfieldRequiredMsg">
                Client No is required.</span></span><span id='clientNames'></span></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <th scope="row">#</th>
                <th scope="row"><label for="itemNo">Item:</label></th>
                <th>Weight (kgs)</th>
                <th>Amount (Ushs)</th>
                <th>Discount (Ushs)<input name="saleID" type="hidden" id="saleID" value="<?php echo $_GET['slID']; ?>" /></th>
              </tr>
              <?php $count=1; do { ?>
              <tr>
                <td scope="row"><?php echo $count; ?></td>
                <td scope="row"><?php echo $row_rsOrderedItems['itemName']; ?></td>
                <td><span id="sprytextfield<?php echo ++$count; ?>">
                  <input name="weight[]" type="text" id="weight<?php echo $count; ?>" value="<?php echo $row_rsOrderedItems['weight']; ?>"  onchange="showDiscount(<?php echo $count; ?>)" />
                <br/><span class="textfieldRequiredMsg">Weight is required.</span></span>
                  <input name="charge" type="hidden" id="charge<?php echo $count; ?>" value="<?php echo $row_rsOrderedItems['charge']; ?>" />
                <input name="itemNo[]" type="hidden" id="itemNo<?php echo $count; ?>" value="<?php echo $row_rsOrderedItems['itemID']; ?>" />
                </td>
                <td><input name="amount[]" type="text" id="amount<?php echo $count; ?>" value="<?php echo $row_rsOrderedItems['amount']; ?>" readonly="readonly" /></td>
                <td><input name="subdiscount[]" type="text" id="subdiscount<?php echo $count; ?>" value="<?php echo $row_rsOrderedItems['discount']; ?>" readonly="readonly" /></td>
              </tr>
                <?php } while ($row_rsOrderedItems = mysql_fetch_assoc($rsOrderedItems)); ?>
              <tr>
                <th scope="row">&nbsp;</th>
                <th scope="row">&nbsp;</th>
                <td colspan="2">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <th colspan="2" scope="row"><label for="total">Totals:</label></th>
                <td><input name="totalweight" type="text" id="totalweight" readonly="readonly" /></td>
                <td><input name="total" type="text" id="total" readonly="readonly" /></td>
                <td><input name="discount" type="text" id="discount" readonly="readonly" /></td>
              </tr>
              <tr>
                <th colspan="2" scope="row"><label for="overaltotal">Total charge:</label></th>
                <td colspan="2">
                <input name="overaltotal" type="text" id="overaltotal" readonly="readonly" /></td>
                <td>&nbsp;</td>
              </tr>
              <tr class="hideable">
                <th scope="row">&nbsp;</th>
                <th scope="row">&nbsp;</th>
                <td colspan="2">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr class="hideable">
                <th scope="row">&nbsp;</th>
                <th scope="row">&nbsp;</th>
                <td colspan="2"><input name="millBranch" type="hidden" id="millBranch" value="<?php echo $_SESSION['millID']; ?>" />
                <input type="submit" name="button2" id="button2" value="Submit" />
                <input name="modifiedby" type="hidden" id="modifiedby" value="<?php echo $_SESSION['staff_id']; ?>" /></td>
                <td>&nbsp;</td>
              </tr>
            </table>
            </center>
          <input type="hidden" name="MM_insert" value="form1" />
          </form>
          <p>&nbsp;</p>
        <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
<?php while($count!=1){?>
var sprytextfield<?php echo $count; ?> = new Spry.Widget.ValidationTextField("sprytextfield<?php echo $count--; ?>");
<?php }?>
total();
        </script>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsOrderedItems);
?>
