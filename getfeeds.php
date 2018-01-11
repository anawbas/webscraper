<?php
/************************************************
* @author Olu Adeniyi
* @copyright 2018

* wrapper script to scrape data from the following sources
* Hacker News via the Firebase JSON API
* BBC Technology RRS Feed
* Slashdot.org
* The script will be run by a cron job every 10 minutes
*
* @param  none
************************************************/
require_once 'URLScraper.class.php' ;
require_once 'config.php';
require_once 'Database.class.php';

function insertFeed($feedData)
{
    $db = new Database();
    
    foreach ($feedData as $feedItem) {
        $sqlStmt = "INSERT INTO news_items (news_source, news_title, news_summary, news_url, news_date) VALUES (?,?,?,?,?) " ;

        try {
            //$insertRow = $db->insertRow($sqlStmt, $feedItem);
            $insertRow = $db->execSQL($sqlStmt, $feedItem);
        } catch (Exception $e) {
            // "Error Insert into Database -- Duplicate";
            //print_r($e->getCode());
        }
    }
    $db->Disconnect();
}
    
// scrape bbc tech news
    try {
        $url=("http://feeds.bbci.co.uk/news/technology/rss.xml#");
        $urlScraper = new URLScraper();
        $urlData = $urlScraper->scrapeRSS($url);
        insertFeed($urlData);
    } catch (Exception $e) {
        $urlDdata = 'server error';
    }

// scrape slashdot.org
    try {
        $url=('https://slashdot.org');
        $urlScraper = new URLScraper();
        $urlData = $urlScraper->scrapeDOM($url);
        insertFeed($urlData);
    } catch (Exception $e) {
        $urlDdata = 'server error';
    }

// scrape hackernews.com
    try {
        $url=('https://hacker-news.firebaseio.com/v0/topstories.json');
        $urlScraper = new URLScraper();
        $urlData    = $urlScraper->scrapeJSON($url);
        insertFeed($urlData);
    } catch (Exception $e) {
        $urlDdata = 'server error';
    }
