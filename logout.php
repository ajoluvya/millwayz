<?php
// *** Logout the current user.
$logoutGoTo = "index.php?loginMsg=You have been logged out";
if (!isset($_SESSION)) {
  session_start();
}
$_SESSION['MM_Username'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
$_SESSION['PrevUrl'] = NULL;
$_SESSION['staff_id'] = NULL;
unset($_SESSION['MM_Username']);
unset($_SESSION['MM_UserGroup']);
unset($_SESSION['PrevUrl']);
unset($_SESSION['staff_id']);
if ($logoutGoTo != "") {header("Location: $logoutGoTo");
exit;
}
?>