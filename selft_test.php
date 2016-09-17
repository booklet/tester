#!/usr/bin/php
<?php
include 'src/concerns/TesterFilesUntils.php';
include 'src/concerns/TesterLoger.php';
include 'src/concerns/TesterMigrationUntils.php';
include 'src/concerns/TesterStringUntils.php';
include 'src/Assert.php';
include 'src/Tester.php';
include 'src/TesterCase.php';
include 'src/TesterPendingException.php';
include 'src/TesterResult.php';
include 'src/TesterTestRequest.php';

require_once('vendor/autoload.php');

echo "\nRun all tests\n";
$time_start = microtime(true);
$tests = new Tester(['tests_paths' => ['tests']]);
$tests->run();
echo "\nFinished in ". number_format((microtime(true) - $time_start), 2)." seconds.\n\n";
