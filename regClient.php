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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO tbl_client (millID, fname, lname, address1, address2, phoneNo, occupation) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['millID'], "int"),
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['address1'], "text"),
                       GetSQLValueString($_POST['address2'], "text"),
                       GetSQLValueString($_POST['phoneNo'], "text"),
                       GetSQLValueString($_POST['occupation'], "text"));

  mysql_select_db($database_millwayconn, $millwayconn);
  $Result1 = mysql_query($insertSQL, $millwayconn) or die(mysql_error());

  $insertGoTo = "regClient.php?clientAddMsg=New client successfully registered";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz::..Register client</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
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
          <h1>New Client Form</h1>
          <?php echo isset($_GET['clientAddMsg'])?"<p>".$_GET['clientAddMsg']."</p>":""; ?>
          <p><a href="clients.php">Client List</a></p>
          <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
          <center>
            <table width="0" border="0" cellspacing="0" id="tbl_capture">
              <tr>
                <th scope="row"><label for="fname">Firstname:</label></th>
                <td><span id="sprytextfield1">
                  <input name="fname" type="text" class="frm_fld" id="fname" />
                  <br />
                  <span class="textfieldRequiredMsg"> Firstname is required.</span></span></td>
</tr>
              <tr>
                <th scope="row"><label for="lname">Lastname:</label></th>
                <td><span id="sprytextfield2">
                  <input name="lname" type="text" class="frm_fld" id="lname" />
                  <br />
                  <span class="textfieldRequiredMsg">Lastname is required.</span></span></td>
</tr>
              <tr>
                <th scope="row"><label for="address1">Address1:</label></th>
                <td><span id="sprytextfield3">
                  <input name="address1" type="text" class="frm_fld" id="address1" />
                  <span class="textfieldRequiredMsg"><br />
                    Lastname is required.</span></span></td>
</tr>
              <tr>
                <th scope="row"><label for="address2">Address2:</label></th>
                <td><span id="sprytextfield4">
                  <input name="address2" type="text" class="frm_fld" id="address2" />
                  <span class="textfieldRequiredMsg"><br />
                    Address2 is required.</span></span></td>
</tr>
              <tr>
                <th scope="row"><label for="phoneNo">Phone No:</label></th>
                <td><span id="sprytextfield5">
                <input name="phoneNo" type="text" class="frm_fld" id="phoneNo" />
                <br />
                <span class="textfieldRequiredMsg">Phone number is required.</span><span class="textfieldInvalidFormatMsg">Invalid phone No</span></span></td>
              </tr>
              <tr>
                <th scope="row"><label for="occupation">Occupation:</label></th>
                <td><span id="spryselect1">
                  <select name="occupation" class="frm_fld" id="occupation">
                    <option>Select one..</option>
                    <option value="Farmer">Farmer</option>
                    <option value="Transporter">Transporter</option>
                  </select>
                  <span class="selectRequiredMsg"><br />
                    Please select occupation.</span></span></td>
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
            <input type="hidden" name="MM_insert" value="form1" />
            <table width="0" border="0" cellspacing="0">
              <tr> </tr>
              <tr> </tr>
              <tr> </tr>
              <tr> </tr>
              <tr> </tr>
            </table>
            </center>
          </form>
          <p>&nbsp;</p>
        <script type="text/javascript">
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "custom", {pattern:"+256000000000", hint:"+256771234567", useCharacterMasking:true});
        </script>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
