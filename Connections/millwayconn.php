<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_millwayconn = "localhost";
$database_millwayconn = "millways";
$username_millwayconn = "root";
$password_millwayconn = "admin";
$millwayconn = mysql_pconnect($hostname_millwayconn, $username_millwayconn, $password_millwayconn) or trigger_error(mysql_error(),E_USER_ERROR); 
?>