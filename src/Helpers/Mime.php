<?php
namespace Midnite81\ReportEmailer\Helpers;

class Mime
{
    /**
     * Get mime type
     *
     * @param $extension
     * @return mixed|null
     */
    public static function getMimeType($extension)
    {
        if (in_array($extension, self::dictionary())) {
            return self::dictionary()[$extension];
        }
        return null;
    }

    /**
     * Dictionary of mime types
     *
     * @return array
     */
    public static function dictionary()
    {
        return [
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'pdf' => 'application/pdf',
        ];
    }
}