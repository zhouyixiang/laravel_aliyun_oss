<?php
namespace Yixiang\LaravelAliOss;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;

class AliOssAdapter extends AbstractAdapter
{
    private $aliyunClient;
    private $bucket;
    private $acl;

    /**
     * @param \ALIOSS $client
     * @param $bucket
     * @param string $prefix
     * @param string $acl
     */
    public function __construct(\ALIOSS $client, $bucket, $prefix = '', $acl = 'public-read')
    {
        $this->aliyunClient = $client;
        $this->bucket = $bucket;
        $this->setPathPreFix($prefix);
        $this->acl = $acl;
        //$this->createBucket();
    }

    /**
     * create the bucket.
     *
     * @return bool
     */
    private function createBucket()
    {
        $oss = $this->aliyunClient;
        $bucket = $this->getBucket();
        $oss->create_bucket($bucket, $this->acl);
        return true;
    }

    /**
     * @return mixed
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param $location
     *
     * @return array
     */
    private function getObjectMetaHeader($location)
    {
        $response = $this->aliyunClient->get_object_meta($this->bucket, $location);

        return $response->header;
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        $options = [
            'content' => $contents,
            'length' => strlen($contents),
        ];

        $location = $this->applyPathPrefix($path);
        $res = $this->aliyunClient->upload_file_by_content($this->bucket, $location, $options);
        if ($res->isOK()) {
            return $res->header;
        } else {
            $this->ifErrorMessageThenThrow($res);
            return false;
        }
    }

    /**
     * Write a new file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        $contents = stream_get_contents($resource);
        $options = [
            'content' => $contents,
            'length' => strlen($contents),
        ];
        $location = $this->applyPathPrefix($path);
        $res = $this->aliyunClient->upload_file_by_content($this->bucket, $location, $options);
        if (is_resource($resource)) {
            fclose($resource);
        }

        if ($res->isOK()) {
            return $res->header;
        } else {
            $this->ifErrorMessageThenThrow($res);
            return false;
        }
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        $options = [
            'content' => $contents,
            'length' => strlen($contents),
        ];

        $location = $this->applyPathPrefix($path);
        $res = $this->aliyunClient->upload_file_by_content($this->bucket, $location, $options);
        if ($res->isOK()) {
            return $res->header;
        } else {
            $this->ifErrorMessageThenThrow($res);
            return false;
        }
    }

    /**
     * Update a file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        $contents = stream_get_contents($resource);
        $options = [
            'content' => $contents,
            'length' => strlen($contents),
        ];

        $location = $this->applyPathPrefix($path);
        $res = $this->aliyunClient->upload_file_by_content($this->bucket, $location, $options);
        if ($res->isOK()) {
            return $res->header;
        } else {
            $this->ifErrorMessageThenThrow($res);
            return false;
        }
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function rename($path, $newPath)
    {
        $options = [
        ];

        $location = $this->applyPathPrefix($path);
        $newLocation = $this->applyPathPrefix($newPath);
        $this->aliyunClient->copy_object($this->bucket, $location, $this->bucket, $newLocation, $options);
        $this->aliyunClient->delete_object($this->bucket, $location);
        return true;
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function copy($path, $newPath)
    {
        $options = [
        ];

        $location = $this->applyPathPrefix($path);
        $newLocation = $this->applyPathPrefix($newPath);
        $this->aliyunClient->copy_object($this->bucket, $location, $this->bucket, $newLocation, $options);
        return true;
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        $location = $this->applyPathPrefix($path);
        $this->aliyunClient->delete_object($this->bucket, $location);
        return true;
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return false;
    }

    /**
     * Create a directory.
     *
     * @param string $dirName directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirName, Config $config)
    {
        $location = $this->applyPathPrefix($dirName);
        $this->aliyunClient->create_object_dir($this->bucket, $location);
        return true;
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        return false;
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        $location = $this->applyPathPrefix($path);
        $response = $this->aliyunClient->is_object_exist($this->bucket, $location);
        return $response->status === 200;
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        $options = [];
        $location = $this->applyPathPrefix($path);
        $res = $this->aliyunClient->get_object($this->bucket, $location, $options);
        return [
            'contents' => $res->body,
        ];
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        $options = [];
        $location = $this->applyPathPrefix($path);
        $res = $this->aliyunClient->get_object($this->bucket, $location, $options);
        $url = $res->header['oss-request-url'];
        $handle = fopen($url, 'r');
        return [
            'stream' => $handle,
        ];
    }

    /**
     * parse the response body.
     *
     * @param $body
     *
     * @return array
     */
    private function getContents($body)
    {
        $xml = new \SimpleXMLElement($body);
        $paths = [];
        foreach ($xml->Contents as $content) {
            $filePath = (string)$content->Key;
            $type = (substr($filePath, -1) == '/') ? 'dir' : 'file';
            if ($type == 'dir') {
                $paths[] = [
                    'type' => $type,
                    'path' => $filePath,
                ];
            } else {
                $paths[] = [
                    'type' => $type,
                    'path' => $filePath,
                    'timestamp' => strtotime($content->LastModified),
                    'size' => (int)$content->Size,
                ];
            }
        }
        foreach ($xml->CommonPrefixes as $content) {
            $paths[] = [
                'type' => 'dir',
                'path' => (string)$content->Prefix,
            ];
        }
        return $paths;
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        if ($recursive) {
            $delimiter = '';
        } else {
            $delimiter = '/';
        }
        $prefix = $this->applyPathPrefix($directory) . '/';
        $next_marker = '';
        $maxkeys = 100;
        $options = [
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $next_marker,
        ];
        $res = $this->aliyunClient->list_object($this->bucket, $options);
        if ($res->isOK()) {
            $body = $res->body;
            return $this->getContents($body);
        }
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        $location = $this->applyPathPrefix($path);
        $response = $this->getObjectMetaHeader($location);
        return $response;
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        $location = $this->applyPathPrefix($path);
        $response = $this->getObjectMetaHeader($location);
        return [
            'size' => $response['content-length'],
        ];
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        $location = $this->applyPathPrefix($path);
        $response = $this->aliyunClient->get_object_meta($this->bucket, $location);
        return [
            'mimetype' => $response->header['_info']['content_type'],
        ];
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        $location = $this->applyPathPrefix($path);
        $response = $this->getObjectMetaHeader($location);
        return [
            'timestamp' => $response['last-modified'],
        ];
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        //$location = $this->applyPathPrefix($path);
        return [
            'visibility' => $this->acl,
        ];
    }

    private function ifErrorMessageThenThrow($res)
    {
        $parsed = \OSSUtil::parse_response($res);
        if (isset($parsed['body']['Error']['Message'])) {
            throw new \RuntimeException('OSS Error Message: ' . $parsed['body']['Error']['Message']);
        }
    }
}
