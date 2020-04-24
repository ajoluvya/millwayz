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
  $insertSQL = sprintf("INSERT INTO tbl_sales (clientNo, discount, salesDate, served, millBranch, modifiedby) VALUES (%s, 0, CURDATE(), 0, %s, %s)",
                       GetSQLValueString($_POST['clientNo'], "text"),
                       GetSQLValueString($_POST['millBranch'], "int"),
                       GetSQLValueString($_SESSION['staff_id'], "int"));

  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($insertSQL, $millwayconn) or die(mysql_error());

$query_rsSaleID = "SELECT MAX(tbl_sales.saleID) saleID FROM tbl_sales WHERE tbl_sales.modifiedby=".$_SESSION['staff_id'];
$rsSaleID = mysql_query($query_rsSaleID, $millwayconn) or die(mysql_error());
$row_rsSaleID = mysql_fetch_assoc($rsSaleID);

  foreach($_POST['itemNo'] as $cnt => $itemNo) {
	  $insertSQL = sprintf("INSERT INTO tbl_frontDeskEntry (saleID, item, weight,discount, modifiedBy) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($row_rsSaleID['saleID'], "int"),
                       GetSQLValueString($itemNo, "int"),
                       GetSQLValueString($_POST['weight'][$cnt], "int"),
                       GetSQLValueString($_POST['subdiscount'][$cnt], "int"),
                       GetSQLValueString($_SESSION['staff_id'], "int"));

  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($insertSQL, $millwayconn) or die(mysql_error());

}
mysql_free_result($rsSaleID);
  
  $insertGoTo = "receiveCustomer.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_millwayconn, $millwayconn);
$query_rsItems = "SELECT * FROM tbl_item";
$rsItems = mysql_query($query_rsItems, $millwayconn) or die(mysql_error());
$row_rsItems = mysql_fetch_assoc($rsItems);
$totalRows_rsItems = mysql_num_rows($rsItems);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millway | Receive Customer</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script type="text/javascript" src="SpryAssets/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#clientNo").change(function(){
		if($(this).val()!="")
		$.get("ajax_query.php",{clntId:$(this).val(),origin:"frontdesk"},function(data,status){
			$("#clientNames").html(data.slice(data.indexOf(" "),data.length));
			if(data.charAt(0)==0){
				$("#itemNo,#weight,#button2,#button").prop("disabled","disabled");
			}
			else{
				$("#itemNo,#weight,#button2,#button").removeProp("disabled");
			}
		});
	});
});
	function addItem(display){
		if($("#weight").val()!=""&&$("#itemNo").val()!=""){
			if(!document.getElementById("rowNum"+$("#itemNo").val())){
				var row = '<span id="rowNum'+$("#itemNo").val()+'" onclick="showItem('+$("#itemNo").val()+');">'+$("#itemNo").find('option:selected').text()+'<input type="hidden" name="itemNo[]" value="'+$("#itemNo").val()+'"><input type="hidden" name="subdiscount[]" value="'+$("#subdiscount").val()+'"><input type="hidden" name="weight[]" value="'+$("#weight").val()+'"> <input type="button" value="X" title="Remove" style="font-size:9px; width:auto; padding:0;" onclick="removeItem('+$("#itemNo").val()+');"></span>';
				$('#addedItems').append(row);
				$("#weight").val("");
				$("#subdiscount").val("");
				$("#itemNo").prop("selectedIndex", 0);
			}
			else
			if(!display)
			alert($("#itemNo").find('option:selected').text()+" is in your list, please consider editing existing one");			
		}
		else{
			if(!display)
			alert("Please ensure all the fields for item have been filled");
		}
	}
	function removeItem(rnum) {
		$("#rowNum"+rnum).remove();
	}
	function showItem(rnum) {
		var display=true;
		addItem(display);
		$.each($("#rowNum"+rnum).children("input:hidden"),function(i,e) {
			switch(e.name){
				case "itemNo[]":
				$("#itemNo").val(e.value);
				break;
				case "weight[]":
				$("#weight").val(e.value);
				break;
				case "subdiscount[]":
				$("#subdiscount").val(e.value);
				break;
			}
        });	
		removeItem(rnum);	
	}
function showPrice(){
	if($("#weight").val()!=""&&$("#clientNo").val()!="")
	$.get("ajax_query.php",{clntId:$("#clientNo").val(),origin:"discount",tdaysweight:$("#weight").val(),itemID:$("#itemNo").val()},function(data,status){
		$("#subdiscount").val(data);
	});	
}
</script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
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
          <h1>Receive Client</h1>
          <form id="form1" name="form1" method="post" action="<?php echo $editFormAction; ?>">
            <center>
              <table width="0" border="0" cellspacing="0" id="tbl_capture">
                <tr>
                  <th scope="row"><label for="clientNo">Client No:</label></th>
                  <td><span id="sprytextfield1">
                    <input name="clientNo" type="text" class="frm_fld" id="clientNo" />
                    <br />
                    <span class="textfieldRequiredMsg"> Client No is required.</span></span><span id='clientNames'></span></td>
</tr>
                <tr class="hideable">
                  <th scope="row"><label for="itemNo">Item:</label></th>
                  <td><span id="spryselect1">
                    <select name="itemNo[]" class="frm_fld" id="itemNo" onchange="showPrice()">
                      <option value="">Select one item...</option>
                      <?php
do {  
?>
                      <option value="<?php echo $row_rsItems['itemID']?>"  id="<?php echo $row_rsItems['charge']?>"><?php echo $row_rsItems['itemName']?></option>
                      <?php
} while ($row_rsItems = mysql_fetch_assoc($rsItems));
  $rows = mysql_num_rows($rsItems);
  if($rows > 0) {
      mysql_data_seek($rsItems, 0);
	  $row_rsItems = mysql_fetch_assoc($rsItems);
  }
?>
                    </select>
                    <br />
                    <span class="selectRequiredMsg">Please select an item.</span></span></td>
</tr>
                <tr class="hideable">
                  <th scope="row"><label for="weight">Weight (kg):
                      <input type="hidden" name="subdiscount[]" id="subdiscount" />
                  </label></th>
                  <td><span id="sprytextfield2">
                    <input name="weight[]" type="text" class="frm_fld" id="weight" onchange="showPrice()" />
                    <br />
                    <span class="textfieldRequiredMsg">weight is required.</span><span class="textfieldInvalidFormatMsg">Enter a number.</span></span></td>
</tr>
                <tr class="hideable">
                  <th scope="row">Orders:</th>
                  <td id="addedItems"></td>
                </tr>
                <tr class="hideable">
                  <th scope="row">&nbsp;</th>
                  <td><input type="button" name="button" id="button" value="Another item[+]:" onclick="addItem(false)" /></td>
                </tr>
                <tr class="hideable">
                  <th scope="row">&nbsp;</th>
                  <td><input name="millBranch" type="hidden" id="millBranch" value="<?php echo $_SESSION['millID']; ?>" />
                    <input type="submit" name="button2" id="button2" value="Submit" />
                    <input name="modifiedby" type="hidden" id="modifiedby" value="<?php echo $_SESSION['staff_id']; ?>" /></td>
                </tr>
              </table>
            </center>
            <input type="hidden" name="MM_insert" value="form1" />
            <center>
              <table width="0" border="0" cellspacing="0">
                <tr class="hideable"> </tr>
                <tr class="hideable"> </tr>
                <tr class="hideable"> </tr>
                <tr> </tr>
              </table>
            </center>
          </form>
          <p>&nbsp;</p>
        <script type="text/javascript">
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
        </script>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsItems);
?>
