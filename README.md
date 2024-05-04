# Why not use ClickUp's Asana importer?
Depending on exactly what you want to pull over, the built-in importer might not do what you need.

And, at the time I wanted to migrate, the ClickUp importer was experiencing some issues, and I didn't want to wait on them to resolve it.

# How to Import Asana to ClickUp via CSV

Unfortunately, at least on the free plan, you'll have to go project by project with the Asana export.

If you're scripting the cleanup, you might like to combine all the project CSVs into one after exporting - but it's highly recommended to do just 1 at first, to make sure everything comes through correctly.

For each Asana project you want to move over, do the following:

1. Create the project in ClickUp with the same name to keep things simple (you can rename later if you want)
   - **NOTE:** If you have some todos in multiple projects in Asana, make sure you create *all* the relevant projects in ClickUp first.
   - Eg. to import Asana-Sample.csv successfully, you would need 
2. [Export the Asana project to CSV](https://help.asana.com/hc/en-us/articles/14139896860955-Privacy-and-security#sts=Export)
3. Do some cleanup to prepare the CSV for ClickUp.
   You could do this manually, or you could use a script like the one in this repository (eg. `php cleanup.php data/Asana-Sample.csv`)
   Either way, these are the items to update. Refer to sample files in data/ for additional clarity where needed.
    - IN PROGRESS
4. If you use a scripting method, it might also be worth updating these columns so Asana better recognizes them:
    - IN PROGRESS
5. Import the resulting clean CSV into ClickUp.
    - TODO
