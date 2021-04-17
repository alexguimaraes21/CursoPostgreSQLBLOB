<?php

define('APPLICATION_PATH', '../');

require APPLICATION_PATH . '/vendor/autoload.php';

use App\DAO\ArquivosDAO as BlobDB;

try {
    // connect to the PostgreSQL database
//    $pdo = Connection::get()->connect();
    //
    $blobDB = new BlobDB();
    $fileId = $blobDB->insert('logo', 'image/png', 'assets/images/google.png');

    echo 'A file has been inserted with id ' . $fileId;
} catch (\PDOException $e) {
    echo $e->getMessage();
}