<?php
require_once 'init.php';

$bootstrap = $application->getBootstrap();

if(!isset($argv[1])) {
    die('Please specify a file where the object can be located.');
}
if(!isset($argv[2])) {
    die('Please specify the function to call.');
}
$functionName = $argv[2];
$sendOnlyToSupervisor = $argv[3];

$project = unserialize(base64_decode(file_get_contents($argv[1])));
$email = new Application_Model_Export_EmailAsync($bootstrap);

$email->$functionName($project, $sendOnlyToSupervisor);
?>