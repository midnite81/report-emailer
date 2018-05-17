<?php
namespace Midnite81\ReportEmailer;

use Illuminate\Bus\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\FormatIdentifier;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Monolog\Handler\FleepHookHandler;

abstract class ReportEmailer
{
    /**
     * Array of sheet methods to process
     *
     * @var array
     */
    protected $sheets;

    /**
     * Array of sheet data to send
     *
     * @var array
     */
    protected $sheetData;

    /**
     * The maximum number of sheets which can be emailed
     *
     * @var int
     */
    protected $maxSheets = 10;

    /**
     * Data you want to be passed through to page one of the excel spreadsheet
     * (you can sequentially create sheet2, sheet3
     *
     * @return Collection|array|object
     */
    abstract public function sheet1();

    /**
     * The body of the email you will be sending with the report
     *
     * @return string
     */
    abstract public function body();

    /**
     * Email address(es) of the sender
     *
     * @return string
     */
    abstract public function from();

    /**
     * Email address(es) of To recipients
     *
     * @return array|string
     */
    abstract public function to();

    /**
     * Email address(es) of CC recipients
     *
     * @return array|string|void
     */
    abstract public function cc();

    /**
     * Email address(es) of BCC recipients
     *
     * @return array|string|void
     */
    abstract public function bcc();

    /**
     * Fully qualified save path
     *
     * @return mixed
     */
    abstract public function saveTo();

    /**
     *
     *
     * @return mixed
     */
    abstract public function extension();

    /**
     * Run the report and email it out.
     */
    public function run()
    {
        $temporaryFileName = 'ascascion';

        $excel = new Excel(new PHPExcel(),
                            new LaravelExcelReader(new Filesystem(), new FormatIdentifier(new Filesystem()), new Dispatcher(new Container())),
                            new LaravelExcelWriter(new \Illuminate\Support\Facades\Response(), new Filesystem(), new FormatIdentifier(new Filesystem())));

        for($i = 1; $i <= $this->maxSheets; $i++) {
            if (method_exists($this, 'sheet' . $i)) {
                $this->sheets[] = 'sheet' . $i;
            } else {
                break;
            }
        }

        foreach($this->sheets as $sheet) {
            $this->sheetData[] = $this->{$sheet};
        }

        $savedFile = $excel->create($temporaryFileName, function($excel) {

            foreach($this->sheetData as $key=>$sheetDatum) {
                $excel->sheet('Sheet ' . ($key+1), function(LaravelExcelWriter $sheet) use ($sheetDatum) {
                    if ($sheetDatum instanceof \Illuminate\Database\Eloquent\Model) {
                        $sheet->fromModel($sheetDatum);
                    } else {
                        $sheet->fromArray($sheetDatum);
                    }
                });
            }


        })->export($this->extension());


//        $this->mail($savedFile);

    }

    protected function mail($savedFile)
    {
    }
}