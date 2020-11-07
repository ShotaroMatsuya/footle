<?php

/**
 * Library Requirements
 *
 * 1. Install composer (https://getcomposer.org)
 * 2. On the command line, change to this directory (api-samples/php)
 * 3. Require the google/apiclient library
 *    $ composer require google/apiclient:~2.0
 */
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ . '"');
}

require_once __DIR__ . '/vendor/autoload.php';



// This code will execute if the user entered a search query in the form
// and submitted the form. Otherwise, the page displays the form above.
if (isset($_GET['term'])) {
    /*
   * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
   * {{ Google Cloud Console }} <{{ https://cloud.google.com/console }}>
   * Please ensure that you have enabled the YouTube Data API for your project.
   */
    $DEVELOPER_KEY = 'AIzaSyBcE_wcVTih0l0d7dMA2Qx8byQi0wYlWUc';

    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);

    // Define an object that will be used to make all API requests.
    $youtube = new Google_Service_YouTube($client);


    try {

        // Call the search.list method to retrieve results matching the specified
        // query term.
        $searchResponse = $youtube->search->listSearch('id,snippet', array(
            'q' => $_GET['term'],
            'maxResults' => 5,
            'pageToken' => $_GET['pToken'],
            'type' => 'video'

        ));
        header("Content-Type: application/json; charset=utf-8");

        echo json_encode($searchResponse);
        exit;
    } catch (Google_Service_Exception $e) {
        echo sprintf(
            '<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage())
        );
    } catch (Google_Exception $e) {
        echo sprintf(
            '<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage())
        );
    }
}
