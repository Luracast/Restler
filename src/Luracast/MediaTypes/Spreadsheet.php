<?php


namespace Luracast\Restler\MediaTypes;


use avadim\FastExcelWriter\Excel;
use Luracast\Restler\Contracts\DownloadableFileMediaTypeInterface;
use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\ResponseHeaders;
use Luracast\Restler\Utils\Convert;


class Spreadsheet extends Dependent implements DownloadableFileMediaTypeInterface
{
    public const MIME = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    public const EXTENSION = 'xlsx';

    public bool $firstRowHasFormats = false;
    public string $title = 'Sheet 1';
    public string $filename = 'Spreadsheet';

    public static array $headerStyle = [
        'font' => 'bold',
        'text-align' => 'center',
        'vertical-align' => 'center',
        'border' => 'thin',
        'fill' => '#E6E6E6',
        'format' => '@'
    ];

    public function __construct(Convert $convert)
    {
        parent::__construct($convert);
    }

    /**
     * @return array {@format associative}
     *               CLASS_NAME => vendor/project:version
     */
    public static function dependencies(): array
    {
        return ['avadim\FastExcelWriter\Excel' => 'avadim/fast-excel-writer:^2.3'];
    }

    /**
     * @inheritDoc
     * @param $data
     * @param ResponseHeaders $responseHeaders
     * @param bool $humanReadable
     * @return false|string
     * @throws HttpException
     */
    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false): string
    {
        $data = $this->convert->toArray($data);
        if (!is_writable(Defaults::$cacheDirectory)) {
            throw new HttpException(
                500,
                'Spreadsheet needs Defaults::$cacheDirectory to be writable.'
            );
        }
        if (is_array($data) && array_values($data) == $data) {
            $file = Defaults::$cacheDirectory . '/spreadsheet' . microtime() . '.' . $this->extension();
            $excel = Excel::create([$this->title]);
            $sheet = $excel->getSheet();
            $row = array_shift($data);
            if ($this->firstRowHasFormats) {
                $sheet->writeHeader($row, static::$headerStyle);
            } elseif (array_values($row) == $row) {
                //write header with first row values
                $sheet->writeHeader($row, static::$headerStyle);
            } else {
                //write header with keys
                $sheet->writeHeader(array_keys($row), static::$headerStyle);
                $sheet->writeRow(array_values($row));
            }
            foreach ($data as $row) {
                $sheet->writeRow(array_values($row));
            }
            $excel->save($file);
            $export = file_get_contents($file);
            unlink($file);
            if (!isset($responseHeaders['Content-Disposition'])) {
                $responseHeaders['Content-Disposition'] = 'attachment; filename='
                    . $this->filename . '.' . static::EXTENSION;
            }
            return $export;
        }
        throw new HttpException(500, 'Unsupported data for ' . strtoupper($this->extension()) . ' MediaType');
    }
}
