<?php

define('STATUS_OPEN', 'Open');
define('STATUS_CLOSED', 'Closed');

$file = $argv[1] ?? false;
if (empty($file) || !file_exists($file) || !is_readable($file)) {
	echo "ERROR: Please provide a valid readable file path for an Asana Project CSV export.\n";
	exit(1);
}

$file_asana = fopen($file, 'r');
$file_clickup = fopen($file . '-ClickUp-Ready.csv', 'w');
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
		$parent_task_name = $task_asana['Parent task'];
		if (empty($subtasks[$parent_task_name])) {
			$subtasks[$parent_task_name] = [];
		}
		$subtasks[$parent_task_name][$task_id] = $task_name;
		continue;
	}

	// Process projects and sections to lists
	$projects_array = array_map(
		'trim',
		explode(',', $task_asana['Projects'])
	);
	$section = $task_asana['Section/Column'];
	if (!empty($section) && $section !== '(no section)') {
		$projects_with_section_array = [];
		foreach ($projects_array as $project) {
			$projects_with_section_array[] = "$project:$section";
		}
		$projects_array = $projects_with_section_array;
	}
	$projects = implode("|", $projects_array);

	$task_clickup = [
		'Date Created' => $task_asana['Created At'],
		'Status' => $task_asana['Completed At'] ? STATUS_CLOSED : STATUS_OPEN,
		'Task Name' => $task_name,
		'List (ClickUp)' => $projects,
		'Task assignee(s)' => $task_asana['Assignee Email'],
		'Start Date' => $task_asana['Start Date'],
		'Due Date' => $task_asana['Due Date'],
		'Tags' => $task_asana['Tags'],
		'Description content' => $task_asana['Notes'],
		'Subtasks' => [],
	];

	// Only keep one task with same key details
	// - and prefer open version
	$existing_task = $tasks[$task_name] ?? [];
	foreach ($existing_task as $_existing_task_id => $_existing_task) {
		if (are_similar_tasks($task_clickup, $_existing_task)) {
			if ($task_clickup['Status'] === STATUS_OPEN && $_existing_task['Status'] === STATUS_CLOSED) {
				// Replace existing task with open version
				unset($tasks[$task_name][$_existing_task_id]);
			} else {
				// Skip this task
				continue 2;
			}
		}
	}

	if (empty($tasks[$task_name])) {
		$tasks[$task_name] = [];
	}
	$tasks[$task_name][$task_id] = $task_clickup;
}
fclose($file_asana);

// Loop through subtasks and add to their parent tasks
foreach ($subtasks as $parent_task_name => $subtask_list) {
	foreach ($subtask_list as $subtask_id => $subtask_name) {
		if (empty($tasks[$parent_task_name])) {
			echo "ERROR: Subtask $subtask_name has no parent task $parent_task_name\n";
			continue;
		}
		foreach ($tasks[$parent_task_name] as $task_id => $task) {
			$tasks[$parent_task_name][$task_id]['Subtasks'][] = $subtask_name;
		}
	}
}

// Loop through all tasks and flatten subtasks
foreach ($tasks as $task_name => $task_list) {
	foreach ($task_list as $task_id => $task) {
		$tasks[$task_name][$task_id]['Subtasks'] = implode("|", $task['Subtasks']);
	}
}

// Write to CSV
$headers_written=false;
foreach ($tasks as $task_list) {
	foreach ($task_list as $task) {
		if ( ! $headers_written) {
			fputcsv($file_clickup, array_keys($task));
			$headers_written = true;
		}
		fputcsv($file_clickup, $task);
	}
}
fclose($file_clickup);

function are_similar_tasks($task1, $task2) {

	if ($task1['Task Name'] !== $task2['Task Name']) return false;

	if ($task1['List (ClickUp)'] !== $task2['List (ClickUp)']) return false;

	if ($task1['Task assignee(s)'] !== $task2['Task assignee(s)']) return false;

	if ($task1['Description content'] !== $task2['Description content']) return false;

	return true;
}
