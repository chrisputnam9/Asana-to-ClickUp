# Why not use ClickUp's Asana importer?
Depending on exactly what you want to pull over, the built-in importer might not do what you need.

And, at the time I wanted to migrate, the ClickUp importer was experiencing some issues, and I didn't want to wait on them to resolve it.

# Caveats
 - Not all data will be migrated (for example, completed date is lost)
 - If you use the script, projects & sections will map to lists in ClickUp, using ":" as a separator
 - Dependencies are not handled by the script at this time
 - Todos will be imported with status either 'TO DO' or 'Completed' with the script

# How to Import Asana to ClickUp via CSV

Unfortunately, at least on the free plan, you'll have to go project by project with the Asana export.

If you're scripting the cleanup, you might like to combine all the project CSVs into one after exporting - but it's highly recommended to do just 1 at first, to make sure everything comes through correctly.

For each Asana project you want to move over, do the following:

1. Create a space into which you'll import the Asana project.
2. [Export the Asana project to CSV](https://help.asana.com/hc/en-us/articles/14139896860955-Privacy-and-security#sts=Export)
3. Do some cleanup to prepare the CSV for ClickUp.
   You could do this manually, or you could use a script like the one in this repository (eg. `php cleanup.php data/Asana-Sample.csv`)
   These are the items to update. Refer to sample files in data/ for additional clarity where needed.
    - **TODO**
3. If you use a scripting method, it might also be worth updating these columns so Asana better recognizes them (the included php sample script does this for you):
    - **TODO**
4. Import the resulting clean CSV into ClickUp.
    - **TODO**
