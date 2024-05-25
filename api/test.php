<?php

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

// Load XML content into DOMDocument
$doc = new DOMDocument();
libxml_use_internal_errors(true); // Suppress warnings
$doc->loadHTML($response);

// Create an XPath instance
$xpath = new DOMXPath($doc);

// Perform the query
$divNodeList = $xpath->query("//article");

// Get the first div element (assuming there's only one)
$divElement = $divNodeList->item(0);

    // Get inner HTML of the div element
    $innerHtml = '';
    foreach ($divElement->childNodes as $child) {
        $innerHtml .= $doc->saveHTML($child);
    }

    // Output inner HTML
    echo "Inner HTML of the div:\n$innerHtml";


