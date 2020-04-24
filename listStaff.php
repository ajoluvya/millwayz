<?php require_once('Connections/millwayconn.php'); ?>
<?php require_once('Connections/millwayconn.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin";
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsStaff = 10;
$pageNum_rsStaff = 0;
if (isset($_GET['pageNum_rsStaff'])) {
  $pageNum_rsStaff = $_GET['pageNum_rsStaff'];
}
$startRow_rsStaff = $pageNum_rsStaff * $maxRows_rsStaff;

$adMill_rsStaff = "0";
if (isset($_SESSION['millID'])) {
  $adMill_rsStaff = $_SESSION['millID'];
}
mysql_select_db($database_millwayconn, $millwayconn);
$query_rsStaff = sprintf("SELECT tbl_staff.staff_id, tbl_staff.fname, tbl_staff.lname, tbl_staff.address1, tbl_staff.address2, tbl_staff.nssfno, tbl_staff.tin, tbl_staff.`role`, tbl_mill.millName FROM tbl_staff JOIN tbl_mill ON tbl_staff.millID=tbl_mill.millID WHERE tbl_staff.millID = %s ORDER BY tbl_staff.datemodified", GetSQLValueString($adMill_rsStaff, "int"));
$query_limit_rsStaff = sprintf("%s LIMIT %d, %d", $query_rsStaff, $startRow_rsStaff, $maxRows_rsStaff);
$rsStaff = mysql_query($query_limit_rsStaff, $millwayconn) or die(mysql_error());
$row_rsStaff = mysql_fetch_assoc($rsStaff);

if (isset($_GET['totalRows_rsStaff'])) {
  $totalRows_rsStaff = $_GET['totalRows_rsStaff'];
} else {
  $all_rsStaff = mysql_query($query_rsStaff);
  $totalRows_rsStaff = mysql_num_rows($all_rsStaff);
}
$totalPages_rsStaff = ceil($totalRows_rsStaff/$maxRows_rsStaff)-1;

$queryString_rsStaff = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsStaff") == false && 
        stristr($param, "totalRows_rsStaff") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsStaff = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsStaff = sprintf("&totalRows_rsStaff=%d%s", $totalRows_rsStaff, $queryString_rsStaff);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tmp_millways.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Millwayz::..Staff List</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
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
          <h1>Staff</h1>
          <?php if ($totalRows_rsStaff == 0) { // Show if recordset empty ?>
  <p>No records found</p>
  <?php } // Show if recordset empty ?>
<p><a href="regStaff.php">Register staff</a></p>
          <?php if ($totalRows_rsStaff > 0) { // Show if recordset not empty ?>
            <center>
              <table width="0" border="0" cellspacing="0" id="tbl_view">
                <tr>
                  <th scope="col">S/N</th>
                  <th scope="col">ID</th>
                  <th scope="col">Names</th>
                  <th scope="col">Mill</th>
                  <th scope="col">Address1</th>
                  <th scope="col">Address2</th>
                  <th scope="col">NSSF No.</th>
                  <th scope="col">TIN</th>
                  <th scope="col">Role</th>
                  <th scope="col">Regn. Date</th>
                  <th scope="col">EDIT</th>
                </tr>
                <?php $cnt=1; do { ?>
                  <tr>
                    <td><?php echo $cnt++; ?></td>
                    <td><?php echo $row_rsStaff['staff_id']; ?></td>
                    <td><?php echo $row_rsStaff['fname']; ?> <?php echo $row_rsStaff['lname']; ?></td>
                    <td><?php echo $row_rsStaff['millName']; ?></td>
                    <td><?php echo $row_rsStaff['address1']; ?></td>
                    <td><?php echo $row_rsStaff['address2']; ?></td>
                    <td><?php echo $row_rsStaff['nssfno']; ?></td>
                    <td><?php echo $row_rsStaff['tin']; ?></td>
                    <td><?php echo $row_rsStaff['role']; ?></td>
                    <td><?php echo $row_rsStaff['datemodified']; ?></td>
                    <td><a href="editStaff.php?staffId=<?php echo $row_rsStaff['staff_id']; ?>">Edit</a> | <a href="delete.php?staffId=<?php echo $row_rsStaff['staff_id']; ?>">Delete</a></td>
                  </tr>
                  <?php } while ($row_rsStaff = mysql_fetch_assoc($rsStaff)); ?>
              </table>
            </center>
            <p>&nbsp;<?php echo ($startRow_rsStaff + 1) ?> - <?php echo min($startRow_rsStaff + $maxRows_rsStaff, $totalRows_rsStaff) ?> of <?php echo $totalRows_rsStaff ?> staff</p>
            <p><a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, 0, $queryString_rsStaff); ?>">First</a> | <a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, max(0, $pageNum_rsStaff - 1), $queryString_rsStaff); ?>">Previous</a> | <a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, min($totalPages_rsStaff, $pageNum_rsStaff + 1), $queryString_rsStaff); ?>">Next</a> | <a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, $totalPages_rsStaff, $queryString_rsStaff); ?>">Last</a></p>
            <?php } // Show if recordset not empty ?>
        <!-- InstanceEndEditable --></div><!--END content-->
        
      <div class="footer">&copy;millways 2012 - 2014</div><!--END footer-->
    
    </div>
<p>&nbsp;</p>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsStaff);

mysql_free_result($rsStaff);
?>
