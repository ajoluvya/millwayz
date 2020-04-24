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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "role";
  $MM_redirectLoginSuccess = "redirect.php";
  $MM_redirectLoginFailed = "index.php?loginMsg=Wrong username or password, try again";
  $MM_redirecttoReferrer = true;
  
  $loginFoundUser=0;
  
  mysql_select_db($database_millwayconn, $millwayconn);  //we select from the other table	
  $LoginRS__query=sprintf("SELECT username, password, millID, fname, lname, staff_id, role FROM tbl_staff WHERE username=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
      
  $LoginRS = mysql_query($LoginRS__query, $millwayconn) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS); 
  if (!$loginFoundUser) {
  //go ahead to search from admin table
  $LoginRS__query=sprintf("SELECT staff_id, username, password, millID, fname,  lname, role FROM admin WHERE username=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text"));
      
  $LoginRS = mysql_query($LoginRS__query, $millwayconn) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS); 
  }
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'role');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
	$_SESSION['millID'] = mysql_result($LoginRS,0,'millID');
	$_SESSION['staff_id'] = mysql_result($LoginRS,0,'staff_id');	
	$_SESSION['unames'] = mysql_result($LoginRS,0,'fname');	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_index.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millways::.Login</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationPassword.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="CSS/style.css"/>
</head>

<body>
        <div class="wrapperOverall">
            
            <div class="header">
            <img class="logo" src="imgs/header_logo.png" width="279" height="52" />
            
            </div>
        
        </div> <div class="content"><!-- InstanceBeginEditable name="contentIndex" -->
        <center>
        <h1>LOGIN</h1>
        <form id="form1" name="form1" method="POST" action="<?php echo $loginFormAction; ?>">
          <p><?php echo isset($_GET['loginMsg'])?$_GET['loginMsg']:""; ?></p>
          <table width="0" border="0" id="tbl_capture">
            <tr>
              <th scope="row"><label for="username2">Username:</label></th>
              <td><span id="sprytextfield1">
                <input name="username" type="text" class="frm_fld" id="username" value="<?php echo $_POST['username']; ?>" />
                <br />
              <span class="textfieldRequiredMsg">Username is required</span></span></td>
            </tr>
            <tr>
              <th scope="row"><label for="password">Password:</label></th>
              <td><span id="sprypassword1">
                <input name="password" type="password" class="frm_fld" id="password" />
                <br />
              <span class="passwordRequiredMsg">Password is required</span></span></td>
            </tr>
            <tr>
              <th scope="row">&nbsp;</th>
              <td><input type="submit" name="button" id="button" value="Login" /></td>
            </tr>
            <tr>
              <th colspan="2" scope="row"><a href="forgottenPass.php">Forgotten Username or password?</a></th>
            </tr>
          </table>
        </form>
        <p>&nbsp;</p>
        <p>&nbsp;</p></center>
        <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword1");
        </script>
        <!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
