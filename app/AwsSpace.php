<?php

namespace app;

/**
* class MSG91 to send SMS on Mobile Numbers.
* @author Shantanu Kulkarni
*/

use Aws\S3\S3Client as AWS;


class AwsSpace {



    private $client;

    public function __construct()
    {
        $this->client=  new AWS([
            'version' => 'latest',
            'region'  => env('DO_SPACES_REGION'),
            'endpoint' => env('DO_SPACES_ENDPOINT'),
            'credentials' => [
                    'key'    => env('DO_SPACES_KEY'),
                    'secret' => env('DO_SPACES_SECRET'),
                ],
    ]);
    }

    public function getSpaceInfo(){
        $spaces = $this->client->listBuckets();
foreach ($spaces['Buckets'] as $space){
    echo $space['Name']."\n";
}
    }




}


