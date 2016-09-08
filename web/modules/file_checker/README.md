INTRODUCTION
------------

The purpose of File Checker is to verify that files managed by Drupal actually 
exist at the location where Drupal believes they are. 

It offers the following features:

 * You can initiate file checking through the UI at 
   admin/config/media/file-system/file-checker.
 * You can arrange to have file checking initiated by cron.
 * If you choose to initiate by cron, you can choose to further limit how often 
   file checking is initiated.
 * Regardless of how checking, is initiated, actual checking happens in the 
   background after cron runs, thanks to the Queue API.
 * Checking processes all file entities, and reports if a file does not exist 
   at the uri given for the file entity.
 * Missing files are reported both in the UI and logged as a warning.


REQUIREMENTS
------------

No special requirements.


RECOMMENDED MODULES
-------------------

 * Various monitoring modules would allow you to receive an email notification 
   if missing files were detected.
 
 * Cron modules like Elysia Cron and Ultimate Cron allow for fine-grained
   control of when specific cron tasks are executed.


INSTALLATION
------------
 
 * Install as you would normally install a contributed Drupal module. See:
   https://www.drupal.org/docs/8/extending-drupal/installing-contributed-modules
   for further information.


CONFIGURATION
-------------
 
 * Configure File Checking in
   Administration » Configuration » Media » File system  » File Checker:

   - Check files once.

     This button allows a user to queue all file entities to be checked the next
	 time cron runs.

   - Initiate file checking whenever cron runs.

     If checked, when cron runs all file entities are queued for checking.
	 
   - Do not initiate file checking more often than ...

     If a time value other than "No limit" is selected, then file entities will 
	 not be queued for checking (even if "Initiate file checking whenever cron runs" 
	 is checked) if a previous file checking run has happened within the specified 
	 time. This allows you to schedule file checking to happen automatically by
	 cron, but less frequently than cron itself.

	 
LIMITATIONS
------------

The current Drupal 8 version of File Checker has not been tested on sites with large
(10,000+) amounts of files. You may experience performance issues at this scale.
 

MAINTAINERS
-----------

Current maintainers:
 * Jonathan Shaw (jonathanjfshaw) - https://drupal.org/user/54136

Initial development was sponsored by:
 * Awakened Heart Sangha
   A Buddhist community in the UK - visit http://www.ahs.org.uk for more information.
   
 @todo
 coding standards
 revisioning