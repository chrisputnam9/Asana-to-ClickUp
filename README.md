# Why not use ClickUp's Asana importer?

Depending on exactly what you want to pull over, the built-in importer might not do what you need.

At the time of our migration, it was lacking support for some data we wanted to bring over, and there were some bugs preventing us from completing imports with it at all.

# Instructions: How to Import Asana to ClickUp via CSV

Unfortunately, at least on the free plan, you'll have to go project by project with the Asana export.

We highly recommend following the full process with a single project into a testing space in ClickUp, and verifying everything worked as you like before moving ahead with your full import plan.

For each Asana project you want to move over, do the following:

1. [Export the Asana project to CSV](https://help.asana.com/hc/en-us/articles/14139896860955-Privacy-and-security#gl-export)
2. Clean up the CSV to match ClickUp's format. See the below sections for options.
3. [Import your cleaned-up CSV to ClickUp](https://help.clickup.com/hc/en-us/articles/6310834724247-Import-a-data-file-into-ClickUp)
    - **Note:** If you used our script, make sure you [select PIPE(|) as the delimiter for Subtasks and Tags](https://cmp.onl/tnHn).

## Cleanup Option 1 - PHP Script

### Caveats

-   Not all data will be migrated (for example, completed date and dependencies are ignored/lost)
-   Todos will be imported with status configured at top of script based on completed date
-   Projects & sections will map to lists in ClickUp, using ":" as a separator
-   Items in multiple lists are not supported by the CSV importer, so they will pull into lists with titles like "Sample1:Section|Sample2:Section". You can then manually organize those items in ClickUp after import.
-   Also, to make this organization easier, completed items will only go in the first list they belong to.

### Usage

1. Run `php cleanup.php path/to/Asana-Export.csv` replacing the path with the actual filepath of your export
2. The script will output a new CSV file in the same directory as your Asana file with "ClickUp-Ready.csv" appended to the filename

## Cleanup Option 2 - Manual Steps

These steps could be done completely manually, or with help from sheet formulas, macros, scripts, etc.

Refer to the Asana and ClickUp sample CSVs in the data-samples folder to help clarify these steps.

1.  Remove these columns (except for any you would like to import as custom fields):

    -   Task ID
    -   Last Modified
    -   Assignee
    -   Blocked By (Dependencies)
    -   Blocking (Dependencies)

2.  Rename the following columns:

    -   Created At -> Date Created
    -   Completed At -> Status
    -   Name -> Task Name
    -   Assignee Email -> Task assignee(s)
    -   Notes -> Description content
    -   Projects -> List

3.  Add a new column called Subtasks

4.  Choose what you'd like to do with Section/Column. For example you could:

    -   Add it to project/list names (as our script does)
    -   Add it as a status (in which case make sure you refer to step 6 below as you decide how to map everything)
    -   Leave it as-is and map to a custom field during import

5.  For each task with a "Parent task" value

    -   Find the parent task in the CSV
    -   Add the Task Name to Subtasks in the parent task's row - separate multiple subtasks with a comma or a pipe - "|"
    -   Remove the subtask row (the one with the "Parent task" value)

6.  Update the Status column to match ClickUp's statuses. For example, you might:

    -   Change each date value (indicating completion) to "Closed"
    -   Change each blank value to "Open"

7.  Remove these columns once they are no longer needed for reference.

    -   Section/Column
    -   Parent Task

8.  The following columns may remain unchanged

    -   Start Date
    -   Due Date
    -   Tags (unless you need to change the delimiter to match other data)

9.  Remove any rows you do not want to import.

    -   For example, Asana creates a new task every time a repeating task is completed. You might only want to import one version of each task.
    -   Perhaps you prefer not to import any completed tasks at all

10. Decide how to handle items in multiple projects/lists
    -   At this time, Asana exports these as comma separated values in the Projects column
    -   But, ClickUp's CSV importer does not support multiple lists per task
    -   You could leave the values as-is, and they will import to lists like "Project1, Project2" which you can then process manually in ClickUp.
    -   Or, perhaps you'll prefer to pre-process and remove extra projects or bring them in some other way (description, custom field, etc.)

## Cleanup Option 3 - Write Your Own Script

-   Use the manual steps and PHP script as a reference
-   Submit your script here as a PR if you want to share with others!

# Issues & Contributions

Found a bug or a mistake? Have a suggestion?

-   [Submit an issue here](https://github.com/chrisputnam9/Asana-to-ClickUp/issues)

Want to add your own script or other improvements?

-   [Fork & Submit a PR in GitHub](https://github.com/chrisputnam9/Asana-to-ClickUp)

Developer sustenance funding is welcome, but not expected

-   [Ko-fi](https://ko-fi.com/chrisputnam9)
-   [Github Sponsor](https://github.com/sponsors/chrisputnam9)
