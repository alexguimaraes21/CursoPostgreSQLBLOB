<?php

define('APPLICATION_PATH', '../');

require APPLICATION_PATH . '/vendor/autoload.php';

use App\DAO\ArquivosDAO as BlobDB;

$blobDB = new BlobDB();

// get document id from the query string
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$blobDB->read($id);