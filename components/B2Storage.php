<?php

namespace app\components;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use yii\base\Component;

/**
 * Компонент для загрузки файлов в Backblaze B2 (S3-совместимый API)
 *
 * Использование (после регистрации в config/web.php):
 *   $url = Yii::$app->b2->uploadFile($tmpFilePath, 'uploads/photo.jpg');
 */
class B2Storage extends Component
{
    public string $keyId;
    public string $appKey;
    public string $endpoint;
    public string $region;
    public string $bucket;

    private ?S3Client $client = null;

    public function init()
    {
        parent::init();

        // dd($this->endpoint);
        // dd($this->appKey);
        // dd($this->keyId);
        
        // \Yii::error('B2 keyId raw: [' . $this->keyId . ']', __METHOD__);

        // \Yii::error('B2 init: keyId=' . substr($this->keyId, 0, 4) . '... appKey_len=' . strlen($this->appKey), __METHOD__);

        $this->client = new S3Client([
            'version'     => 'latest',
            'region'      => $this->region,
            'endpoint'    => $this->endpoint,
            'credentials' => [
                'key'    => $this->keyId,
                'secret' => $this->appKey,
            ],
        ]);
    }

    /**
     * Загружает файл в B2 и возвращает публичный URL
     *
     * @param string $localFilePath Путь к локальному файлу (например, $file->tempName из UploadedFile)
     * @param string $remoteKey     Ключ (путь) файла в бакете, например "uploads/abc123.jpg"
     * @return string|null
     */
    public function uploadFile(string $localFilePath, string $remoteKey): ?string
    {
        try {
            $result = $this->client->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $remoteKey,
                'SourceFile'  => $localFilePath,
                // 'ACL'         => 'public-read',
                'ContentType' => mime_content_type($localFilePath) ?: 'application/octet-stream',
            ]);

            return (string) $result['ObjectURL'];
        } catch (AwsException $e) {
            // \Yii::error('B2 upload error: ' . $e->getMessage(), __METHOD__);

            dd($e->getMessage());
            dd($e->getStatusCode());
            dd($e->getAwsErrorCode());
            // dd('AAA');
            \Yii::error(
                'B2 upload error: ' . $e->getMessage()
                . ' | code=' . $e->getAwsErrorCode()
                . ' | type=' . $e->getAwsErrorType()
                . ' | status=' . $e->getStatusCode(),
                __METHOD__
            );

            return null;
        }
    }

    /**
     * Удаляет файл из B2
     */
    public function deleteFile(string $remoteKey): bool
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $remoteKey,
            ]);
            return true;
        } catch (AwsException $e) {
            // \Yii::error('B2 delete error: ' . $e->getMessage(), __METHOD__);

            \Yii::error(
                'B2 upload error: ' . $e->getMessage()
                . ' | code=' . $e->getAwsErrorCode()
                . ' | type=' . $e->getAwsErrorType()
                . ' | status=' . $e->getStatusCode(),
                __METHOD__
            );

            return false;
        }
    }
}