<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class R2Client
{
    protected $s3;
    protected $bucket;
    protected $publicUrl;

    public function __construct()
    {
        $this->bucket = env('R2_BUCKET');
        $this->publicUrl = env('R2_PUBLIC_URL');
        $this->s3 = new S3Client([
            'region' => env('R2_REGION', 'auto'),
            'endpoint' => env('R2_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'credentials' => [
                'key' => env('R2_ACCESS_KEY_ID'),
                'secret' => env('R2_SECRET_ACCESS_KEY'),
            ],
            'version' => 'latest',
        ]);
    }

    /**
     * Upload a file to R2
     * 
     * @param string $key
     * @param mixed $body
     * @param string $contentType
     * @return string The public URL of the uploaded file
     */
    public function upload(string $key, $body, string $contentType = 'image/jpeg')
    {
        try {
            log_message('debug', 'R2 Attempting upload: ' . $key . ' with ContentType: ' . $contentType);
            
            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'Body' => $body,
                'ContentType' => $contentType,
            ]);

            $url = rtrim($this->publicUrl, '/') . '/' . $key;
            log_message('debug', 'R2 Upload Success. URL: ' . $url);
            
            return $url;
        } catch (AwsException $e) {
            log_message('error', 'R2 Upload Error: ' . $e->getAwsErrorMessage() ?: $e->getMessage());
            log_message('error', 'R2 Error Details: ' . json_encode($e->getAwsErrorAttributes()));
            return false;
        }
    }

    /**
     * Delete a file from R2
     * 
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        if (empty($key)) return;

        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
        } catch (AwsException $e) {
            log_message('error', 'R2 Delete Error: ' . $e->getMessage());
        }
    }

    /**
     * Get the public URL for a file
     * 
     * @param string $key
     * @return string
     */
    public function getUrl(string $key)
    {
        if (empty($key)) return '';
        
        // If it's already a full URL, return it
        if (filter_var($key, FILTER_VALIDATE_URL)) {
            return $key;
        }

        return rtrim($this->publicUrl, '/') . '/' . $key;
    }
}
