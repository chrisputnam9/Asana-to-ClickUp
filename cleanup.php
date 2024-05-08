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

	// If it's a subtask, we only need the name
	// We'll add it to the right spot later on
	if ( ! empty($task_asana['Parent task'])) {
		$subtasks[$task_id] = $task_name;
		continue;
	}

	$projects = implode("\n",
		array_map(
			explode(',', $task_asana['Projects']),
			'trim'
		)
	);

	$task_clickup = [
		'Date Created' => $task_asana['Created At'],
		'Status' => $task_asana['Completed'] ? 'Completed' : 'TO DO',
		'Task Name' => $task_name,
		'List (ClickUp)' => $task_asana['Projects'],
		'Task assignee(s)' => $task_asana['Assignee Email'],
		'Start Date' => $task_asana['Start Date'],
		'Due Date' => $task_asana['Due Date'],
		'Tags' => $task_asana['Tags'],
		'Description content' => $task_asana['Notes'],
		'Subtasks' => [],
		'Blocked By (Dependencies)' => $task_asana['Blocked By (Dependencies)'],
		'Blocking (Dependencies)' => $task_asana['Blocking (Dependencies)'],
	];

	$tasks[$task_name][$task_id] = $task_clickup;
}

// Loop through subtasks and add to their parent tasks
foreach ($subtasks as $subtask) {
	// TODO
}

// Loop through all tasks and flatten subtasks
// TODO

// Write to CSV
// TODO
