<?php

namespace common\components\filesystem;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use trntv\filekit\filesystem\FilesystemBuilderInterface;

class ClouldflareR2 implements FilesystemBuilderInterface
{

    public $key;

    public $secret;

    public $region;

    public $bucket;

    public $end_point;

    public function build()
    {
        $client = new S3Client([
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret
            ],
            'region' => $this->region,
            'version' => 'latest',
            'endpoint' => $this->end_point
        ]);
        $adapter = new AwsS3Adapter($client, $this->bucket);
        $filesystem = new Filesystem($adapter);

        return $filesystem;
    }
}