<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$id = $_GET['id'] ?? 0;

// URL of the page you want to fetch
$url = 'https://www.infovojna.bz/article/'.$id.'';

// HTTP referer
$referer = 'https://www.infovojna.bz/';

// Initialize cURL session
$curl = curl_init();

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_REFERER, $referer); // Set the referer header
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification for simplicity

// Execute cURL request
$response = curl_exec($curl);

// Close cURL session
curl_close($curl);

// Check if cURL request was successful
if ($response === false) {
    die('Error fetching content');
}

// Create a new DOMDocument
$dom = new DOMDocument();

// Set the internal encoding to UTF-8
$dom->encoding = 'UTF-8';

// Load the HTML content into the DOMDocument
libxml_use_internal_errors(true); // Suppress warnings
$dom->loadHTML('<?xml encoding="UTF-8">' . $response);
libxml_clear_errors();

// Create a DOMXPath object to query the DOMDocument
$xpath = new DOMXPath($dom);

// Query for list items with class "lde"
$listItems = $xpath->query('//section');

$result = array();

// Loop through each list item
$item = $listItems[0];

// Find the link source within the list item
$a_href = $xpath->evaluate('string(.//a/@href)', $item);

// Find the image source within the list item
$img_src = $xpath->evaluate('string(.//div[@id="arthead"]/@image)', $item);

// Find the heading within the list item
$heading = $xpath->evaluate('string(.//h2)', $item);

// Find the heading within the list item
$content = ''; $i=0;
foreach($xpath->query('//article//div[@class="body"]')->item(0)->childNodes as $child){
    if($i<2){$i++; continue;}
    $content .= strval($dom->saveHTML($child));
}

// Find the comments within the list item
$perex = $xpath->evaluate('string(.//span[@class="perex"])', $item);

// Find the comments within the list item
$comments = $xpath->evaluate('string(.//span[@id="hdcnts"])', $item);

// Find the date within the list item
$date = $xpath->evaluate('string(.//span[@id="hddate"])', $item);

// Output the image source and heading
$item = new StdClass();

$item->link = $a_href;
$item->image = $img_src;
$item->title = $heading;
$item->perex = $perex;
$item->content = $content;
$item->comments = trim($comments);

$item->date = $date;

$result[] = $item;


// Clean up the DOMDocument
unset($dom);

echo json_encode($result);
