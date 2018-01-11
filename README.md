# webscraper
scrape news items from some sites
This is a set of php scripts to aggregate news from the following sites
1. Hacker Newe via their firebase JSON API
2. BBC Technology News via their RSS feed
3. slashdot.org by crawling the DOM

The program is made up of the following files
 config.php -- Database for configuration data
 Database.class.php - PDO database class
 getfeeds.php -- main php script
 URLScraper.class.php - URL scraper Class file

For simplicity and because of time constraint , the scraper functions are tailored
specifically to the current format of the URLs mentioned above.

Scraped data are initially stored in an array before loading into the database.

A dump of the test database is included

the script can be run via a cron job as often as required.

Duplicate articles from the same URL are not loaded during subsequent runs

Future Plan.

1. To have script generate load files to be loaded into the database by a shell script. This will reduce the hit on the database. It will also be possible to clean up and dedupe the data prior to loading into the database.
2. Have a rules file to be used to describe the structure of a URL so that a more generic web crawler can be built.
3. Have a  template file to specify the data fields to be extracted



