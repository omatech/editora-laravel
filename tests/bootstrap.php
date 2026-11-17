<?php

require dirname(__DIR__).'/vendor/autoload.php';

// The admin helpers keep any function that already exists, so this one records
// each URL the admin would send a HEAD request to instead of reaching the network.
function _getRemoteFileSize($file)
{
    return \Omatech\Editora\Tests\RemoteHeaders::contentLength($file);
}
