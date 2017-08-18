<?php
require('metamodel.php');
require('readers.php');
require('writers.php');

$reader = new XLSXProgramReader('./programme-pre-v9.xlsx','./programme-pre-v9.xlsx');
write_program($reader->parseProgram());
