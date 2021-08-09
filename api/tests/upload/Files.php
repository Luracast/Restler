<?php


use Luracast\Restler\StaticProperties;
use Luracast\Restler\UI\FormStyles;
use Psr\Http\Message\UploadedFileInterface;

class Files
{
    public function __construct(StaticProperties $forms)
    {
        $forms->style = FormStyles::$bootstrap3;
    }

    /**
     * @response-format Html
     */
    public function get(): string
    {
        return '';
    }

    /**
     * @param string $process {@choice on,off}
     * @param UploadedFileInterface $uploadedFile {@label file}
     * @return array {@type associative} {@label Upload}
     * @request-format Upload
     */
    function post(string $process, UploadedFileInterface $uploadedFile)
    {
        return [
            'process'=>$process,
            'file'=>$uploadedFile->getClientFilename(),
        ];
    }

    /**
     * @param UploadedFileInterface $uploadedFile
     * @request-format Upload
     * @return UploadedFileInterface
     */
    function put(UploadedFileInterface $uploadedFile)
    {
        return $uploadedFile;
    }

    /**
     * @param UploadedFileInterface $uploadedFile
     * @request-format Upload
     * @return UploadedFileInterface
     */
    function patch(UploadedFileInterface $uploadedFile)
    {
        return $uploadedFile;
    }


}
