<?php

$val = 'true';
$bool = filter_var($val, FILTER_VALIDATE_BOOLEAN);
var_dump($bool);
