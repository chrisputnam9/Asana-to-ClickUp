<?php

// Parse args
$search = $argv[1] ?? null;
$search = in_array($search, ['all', 'subtasks', 'reoccur']) ? $search : null;
if ($search === 'all') {
	$search = ['subtasks', 'reoccur'];
} elseif(!empty($search)) {
	$search = [$search];
}

$output_directory = realpath($argv[2] ?? getcwd()) . "/";

// If no args, show help and usage
if (empty($search)) {
	echo "This tool looks for potential problems in Asana, like:\n";
	echo "  - Subtasks that may not have improted correctly\n";
	echo "  - Tasks that seem to have reoccurring due dates which may not have imported correctly\n";
	echo "\n";
	echo "USAGE:\n";
	echo "    php asana_find_problems.php <all|subtasks|reoccur> [output_directory]\n";
	exit();
}

// Check for PACLI
$pacli_exec = '/usr/local/bin/pacli';
if (!file_exists($pacli_exec) || !is_readable($pacli_exec)) {
	echo "ERROR: PACLI (PHP Asana CLI) is not available at $pacli_exec\n";
	echo " - Please download and install from https://github.com/chrisputnam9/pacli\n";
	exit(1);
}

// Init PACLI
ob_start();
$__no_direct_run__ = true;
require_once($pacli_exec);
$output = ob_get_clean();
$pacli = new Pacli();
$pacli->initConfig();

echo "=========================================\n";
echo "Gathering Open Asana Tasks...\n";
echo "=========================================\n";
$tasks_open = [];
$tasks_completed = [];
$tasks_open_with_subtasks = [];

// Loop through projects
$response = $pacli->get('projects', false);
foreach ($response->data as $project)
{
	$project_gid = $project->gid;
	$project_name = $project->name;
	//echo "Project: $project_name\n";
	echo ".";
	$url = "projects/$project_gid/tasks?limit=100&opt_fields=name,num_subtasks,due_on,due_at,completed";
	$response = $pacli->get($url, false);
	foreach ($response->data as $task) {
		$task_subtasks = $task->num_subtasks;
		$task_completed = $task->completed;
		if ($task_completed) {
			add_by_key($tasks_completed, $task->name, $task);
		} else {
			add_by_key($tasks_open, $task->name, $task);
			if ($task_subtasks) {
				add_by_key($tasks_open_with_subtasks, $task->name, $task);
			}
		}
	}
}

// Run the search(es)
foreach ($search as $type) {
	switch ($type) {

		case 'subtasks':
			echo "\n\n";
			echo "=========================================\n";
			echo "Tasks with subtasks\n";
			echo "=========================================\n";
			$filepath = $output_directory . 'asana_subtasks.csv';
			@unlink($filepath);
			$file = fopen($filepath, 'w');;
			fputcsv($file, ['TASK NAME', 'TASK URL']);
			$count = 0;
			foreach ($tasks_open_with_subtasks as $task_name => $tasks) {
				foreach ($tasks as $task) {
					$count++;
					fputcsv($file, [trim($task->name), "https://app.asana.com/0/{$project_gid}/{$task->gid}/f"]);
				}
			}
			fclose($file);
			echo "$count open tasks with subtasks found. See {$filepath}\n";

			break;

		case 'reoccur':
			echo "\n\n";
			echo "=========================================\n";
			echo "Tasks that seem to have reoccurring due dates\n";
			echo "=========================================\n";
			$filepath = $output_directory . 'asana_reoccur.csv';
			@unlink($filepath);
			$file = fopen($filepath, 'w');;
			fputcsv($file, ['TASK NAME', 'COMPLETED VERSIONS', 'TASK URL']);
			$count = 0;
			foreach ($tasks_open as $task_name => $tasks) {
				$tasks_completed_count = count($tasks_completed[$task_name] ?? []);
				if ($tasks_completed_count < 1) {
					continue;
				}
				foreach ($tasks as $task) {
					$count++;
					fputcsv($file, [trim($task->name), $tasks_completed_count, "https://app.asana.com/0/{$project_gid}/{$task->gid}/f"]);
					break;// Just one if multiple open
				}
			}
			fclose($file);
			echo "$count open tasks with reoccurring due dates found. See {$filepath}\n";

			break;

	}
}

echo "Done!\n";

function add_by_key(&$array, $key, $value) {
	if (!isset($array[$key])) {
		$array[$key] = [];
	}
	$array[$key][] = $value;
}
