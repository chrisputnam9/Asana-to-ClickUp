<?php

// TODO will load in specified Asana CSV, process data, and write to a CSV that's ready for ClickUp import

$file = $argv[1] ?? false;
if (empty($file) || !file_exists($file) || !is_readable($file)) {
	echo "ERROR: Please provide a valid readable file path for an Asana Project CSV export.\n";
	exit(1);
}

$file_asana = fopen($file, 'r');
$headers = fgetcsv($file_asana);

$subtasks = [];
$tasks = [];

// Process all Asana tasks
while ($row = fgetcsv($file_asana)) {
	$task_asana = array_combine($headers, $row);

	$task_id = $task_asana['Task ID'];
	$task_name = $task_asana['Name'];
	
	$task_clickup = [
		'Date Created' => $task_asana['Created At'],
		'Status' => $task_asana['Completed'] ? 'Completed' : 'TO DO',
	];

	if (empty($task_asana['Parent task'])) {
		if (empty($tasks[$task_name])) {
			$tasks[$task_name] = [];
		}
		$tasks[$task_name][$task_id] = $task;
	} else {
		$subtasks[$task_id] = $task;
	}
}

// Loop through subtasks and add to their parent tasks
foreach ($subtasks)
