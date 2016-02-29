<?php
session_start();
session_destroy();
header('Location: http://gotme.site-meute.com/api/v1/dashboard');
exit();