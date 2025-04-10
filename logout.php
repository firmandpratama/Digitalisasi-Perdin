<?php
session_name('e_perdin');
session_start();
$_session = [];
session_unset();
session_destroy();

header("Location: logout-basic.php");
exit;
