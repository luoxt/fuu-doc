<?php

namespace App\Base\Plugins\Upload;

use OSS\OssClient;
use OSS\Core\OssException;
use App\Exceptions\ApiException;

/**
 * Class Common
 *
 * 示例程序【Samples/*.php】 的Common类，用于获取OssClient实例和其他公用方法
 */
class Common
{
    /**
     * 根据Config配置，得到一个OssClient实例
     *
     * @return OssClient 一个OssClient实例
     */
    public static function getOssClient()
    {
        $endpoint = env('ENDPOINT');
        $accessKeyId = env('ACCESS_KEY_ID');
        $accessKeySecret = env('ACCESS_KEY_SECRET');

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
        } catch (OssException $e) {
            throw new ApiException($e->getMessage(), '4001');
        }
        return $ossClient;
    }

    public static function getBucketName()
    {
        $bucket = env('BUCKET');
        return $bucket;
    }

    /**
     * 工具方法，创建一个存储空间，如果发生异常直接exit
     */
    public static function createBucket()
    {
        $ossClient = self::getOssClient();
        if (is_null($ossClient)) exit(1);
        $bucket = self::getBucketName();
        $acl = OssClient::OSS_ACL_TYPE_PUBLIC_READ;
        try {
            $ossClient->createBucket($bucket, $acl);
        } catch (OssException $e) {

            $message = $e->getMessage();
            if (\OSS\Core\OssUtil::startsWith($message, 'http status: 403')) {
                throw new ApiException("Please Check your AccessKeyId and AccessKeySecret", '4001');
            } elseif (strpos($message, "BucketAlreadyExists") !== false) {
                throw new ApiException("Bucket already exists. Please check whether the bucket belongs to you, or it was visited with correct endpoint.", '4001');
            }

            throw new ApiException($e->getMessage(), '4001');
        }
    }

    public static function println($message)
    {
        if (!empty($message)) {
            echo strval($message) . "\n";
        }
    }
}

Common::createBucket();
