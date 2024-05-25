<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


$id = $_GET['id'] ?? 0;

// URL of the page you want to fetch
$url = 'https://www.infovojna.bz/theme/infovojna/app/load.php?fn=load&id='.$id.'&cid=1';

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
$listItems = $xpath->query('//li[contains(@class, "lde")]');

$result = array();

// Loop through each list item
foreach ($listItems as $item) {

    // Find the image source within the list item
    $a_href = $xpath->evaluate('string(.//a/@href)', $item);	
	
    // Find the image source within the list item
    $img_src = $xpath->evaluate('string(.//img/@src)', $item);

    // Find the heading within the list item
    $heading = $xpath->evaluate('string(.//h2)', $item);

    // Find the heading within the list item
    $content = $xpath->evaluate('string(.//i)', $item);
    
    // Find the heading within the list item
    $comments = $xpath->evaluate('string(.//small)', $item);

    // Output the image source and heading
    $item = new StdClass();

    $path = parse_url($a_href, PHP_URL_PATH);

    // Get the last part of the path as if it were a filename
    $link = pathinfo($path, PATHINFO_BASENAME);


    $item->link = $link;
    $item->image = $img_src;
    $item->title = $heading;
    $item->content = $content;
    $item->comments = trim($comments);

    $item->date = date("d.m.Y | H:i", strtotime("2024-04-20 15:00"));
	
    $result[] = $item;
}

// Clean up the DOMDocument
unset($dom);

echo json_encode($result);
