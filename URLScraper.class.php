<?php

/************************************************
* @author Olu Adeniyi
* @copyright 2018
*
* class to handle url from a number of sources
*
* Its currently specific to the BBC/Hacker News/Slashdot
*
************************************************/
class URLScraper
{
    public function __construct()
    {
        //
    }
    
    private function formatDate($dateString)
    {
        // convert to format to be loaded into mysql
        return date("Y-m-d h:i:s", strtotime($dateString));
    }

    public function scrapeRSS($url)
    {
        // this function is specific to BBC RRS feed
        $xmlDoc = new DOMDocument();
        $loaded = @$xmlDoc->load($url);
        $response = array();

        if ($loaded === true) { // Not parse XML parse error
            
            //get and output "<item>" elements
            $elements = $xmlDoc->getElementsByTagName('item');
            foreach ($elements as $node) {
                $title = $node->getElementsByTagName('title')->item(0)->nodeValue;
                $url = $node->getElementsByTagName('link')->item(0)->nodeValue;
                $description = $node->getElementsByTagName('description')->item(0)->nodeValue;
                $pubDate = $node->getElementsByTagName('pubDate')->item(0)->nodeValue;
                $fmtDate = $this->formatDate($pubDate);
                $data = array("BBC", $title, $description, $url , $fmtDate);
                $response[] = $data ;
            }
        }
        
        return $response;
    }

    public function scrapeDOM($url)
    {

        // this function is specific to slashdot.org
        $response = array();
        $html = file_get_contents($url);
        $doc = new DomDocument();
        libxml_use_internal_errors(true);
        @$doc->loadHTML($html);
        $xpath = new DomXpath($doc);
        $articles = $xpath->query("//div[@id='firehoselist']/article");

        foreach ($articles as $article) {
            $anchors = $xpath->query(".//h2[@class='story']/span[@class='story-title']/a", $article);
            $title = $anchors->item(0)->nodeValue;
            $url = $anchors->item(0)->getAttribute("href");
            $newsBody = $xpath->query(".//div[@class='body']/div[@class='p']", $article);
            $summary = ltrim($newsBody->item(0)->nodeValue);
            $time = $xpath->query(".//time", $article);
            $atDate= substr($time->item(0)->nodeValue, 3);
            $atDate= strtr($atDate, ' @', '  ');
            $fmtDate = $this->formatDate($atDate);
            $data = array("SLASHDOT", $title, $summary, $url , $fmtDate);
            $response[] = $data ;
        }
        return $response;
    }

   
    public function scrapeJSON($url)
    {

        // this function is specific to Hacker News via firebase JSON API
        // the scraping is done reading the top stories list, and using that to load individual stories
        $response = array();

        // load the top stories ids
        $data = file_get_contents($url);
        $newsList = json_decode($data); // decode the JSON feed

        // load all the stories in the list
        $count = 0;
        foreach ($newsList as $newsId) {
            if ($count++ > 100) {
                break ;
            } // get the first 100 top stories
            // load individual news items:
            $newsItem = "https://hacker-news.firebaseio.com/v0/item/".$newsId . ".json";
            $data = file_get_contents($newsItem);
            $newsItemData = json_decode($data); // decode the JSON feed
            if ($newsItemData->type === "story"  && isset($newsItemData->url)) {
                // only process story items with a URL
                $summary = $newsItemData->score . " by " . $newsItemData->by ;
                $fmtDate = $this->formatDate($newsItemData->time);
                $data = array("HACKER", $newsItemData->title, $summary, $newsItemData->url ,$fmtDate);
                $response[] = $data ;
            }
        }
        return $response;
    }
}
?>

