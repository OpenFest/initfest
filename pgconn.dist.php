<?php
# move this to pgconn.php
global $pgconn;

$pgconn = pg_connect("host=localhost dbname=DBNAME user=USERNAME password=PASSWORD");

