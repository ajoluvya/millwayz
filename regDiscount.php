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
  $insertSQL = sprintf("INSERT INTO tbl_discount (itemID, rate, millID, st_weigth, end_weigth) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['itemID'], "int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['millID'], "int"),
                       GetSQLValueString($_POST['st_weigth'], "int"),
                       GetSQLValueString($_POST['end_weigth'], "int"));

  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($insertSQL, $millwayconn) or die(mysql_error());

  $insertGoTo = "listItems.php";
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
<title>Millwayz::..Add discount</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script type="text/javascript" src="SpryAssets/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#clientNo").change(function(){
		if($(this).val()!="")
		$.get("ajax_query.php",{clntId:$(this).val(),origin:"frontdesk"},function(data,status){
			$("#clientNames").html(data.slice(data.indexOf(" "),data.length));
			if(data.charAt(0)==0){
			$("#itemNo").prop("disabled","disabled");
			$("#weight").prop("disabled","disabled");
			}
			else{
			$("#itemNo").prop("disabled","enabled");
			$("#weight").prop("disabled","enabled");
			}
		});
	});
});
function total(){
	var amount=0;
		$.each($("#addedItems").children("input:hidden"),function(i,e) {
			if(e.name=="amount[]")
				amount+=parseInt(e.value);
        });	
				$("#total").val(amount+parseInt($("#amount").val()));
}
function showPrice(){
	if($("#weight").val()!=""&&$("#itemNo").val()!=""){
	$("#amount").val(parseInt($("#itemNo").find('option:selected').prop("id"))*parseInt($("#weight").val()));
	total();
	}
}
	function addItem(display){
		if($("#weight").val()!=""&&$("#amount").val()!=""&&$("#itemNo").val()!=""){
			if(!document.getElementById("rowNum"+$("#itemNo").val())){
				var row = '<span id="rowNum'+$("#itemNo").val()+'" onclick="showItem('+$("#itemNo").val()+');">'+$("#itemNo").find('option:selected').text()+'<input type="hidden" name="amount[]" value="'+$("#amount").val()+'"><input type="hidden" name="itemNo[]" value="'+$("#itemNo").val()+'"><input type="hidden" name="weight[]" value="'+$("#weight").val()+'"> <input type="button" value="X" title="Remove" style="font-size:9px; width:auto; padding:0;" onclick="removeItem('+$("#itemNo").val()+');"></span>';
				$('#addedItems').append(row);
				$("#weight").val("");
				$("#amount").val("");
				$("#itemNo").prop("selectedIndex", 0);
			}
			else
			if(!display)
			alert($("#itemNo").find('option:selected').text()+" is in your list, please consider editing that one");			
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
				case "amount[]":
				$("#amount").val(e.value);
				break;
				case "weight[]":
				$("#weight").val(e.value);
				break;
			}
        });	
		removeItem(rnum);	
	}
</script>
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
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
          <h1>Add discounts</h1>
          <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
          <center>
            <table width="0" border="0" cellspacing="0" id="tbl_capture">
              <tr>
                <th scope="row"><label for="itemID">Item:</label></th>
                <td><span id="spryselect1">
                  <select name="itemID" class="frm_fld" id="itemID">
                    <option value="" <?php if (!(strcmp("", $_GET['itemID']))) {echo "selected=\"selected\"";} ?>>Select an item</option>
                    <?php
do {  
?>
                    <option value="<?php echo $row_rsItems['itemID']?>"<?php if (!(strcmp($row_rsItems['itemID'], $_GET['itemID']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsItems['itemName']?></option>
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
              <tr>
                <th scope="row"><label for="st_weigth">Start weight (kg):</label></th>
                <td><span id="sprytextfield2">
                <input name="st_weigth" type="text" class="frm_fld" id="st_weigth" />
                <br />
                <span class="textfieldRequiredMsg">This is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed in this field.</span></span></td>
              </tr>
              <tr>
                <th scope="row"><label for="end_weigth">End weight (kg):</label></th>
                <td><span id="sprytextfield3">
                <input name="end_weigth" type="text" class="frm_fld" id="end_weigth" />
                <br />
                <span class="textfieldRequiredMsg">This is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed in this field.</span></span></td>
              </tr>
              <tr>
                <th scope="row"><label for="rate">Discount amount (Shs):</label></th>
                <td><span id="sprytextfield1">
                <input name="rate" type="text" class="frm_fld" id="rate" />
                <br />
                <span class="textfieldRequiredMsg">This is required.</span><span class="textfieldInvalidFormatMsg">Only numbers allowed in this field.</span></span></td>
              </tr>
              <tr>
                <th scope="row">&nbsp;</th>
                <td><input name="millID" type="hidden" id="millID" value="<?php echo $_SESSION['millID']; ?>" /></td>
              </tr>
              <tr>
                <th scope="row">&nbsp;</th>
                <td><input type="submit" name="button" id="button" value="Submit" /></td>
              </tr>
            </table>
            </center>
          <input type="hidden" name="MM_insert" value="form1" />
          </form>
          <p>&nbsp;</p>
        <script type="text/javascript">
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {useCharacterMasking:true});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer", {useCharacterMasking:true});
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