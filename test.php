<?php
require('metamodel.php');
require('readers.php');
require('writers.php');

XLSXProgramReader reader = new XLSXProgramReader();
Program p = reader.parseProgram;
write_program(p);
