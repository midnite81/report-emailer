<?php
namespace Midnite81\ReportEmailer;

use App\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Classes\FormatIdentifier;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Midnite81\ReportEmailer\Helpers\Mime;
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
     * The subject of the email
     *
     * @return string
     */
    abstract public function subject();

    /**
     * The filename of the Report (excluding extension)
     *
     * @return string
     */
    abstract public function fileName();

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
        $temporaryFileName = uuid();

        $excel = $this->newExcel();

        $this->addSheetsToArray();

        $this->addDataToArray();

        $savedFile = $this->createExcelFile($excel, $temporaryFileName);

        $createdFile = $savedFile->storagePath . DIRECTORY_SEPARATOR . $savedFile->getFileName() . '.' . $savedFile->ext;

        $this->mail($createdFile);

        $this->destroyTemporaryFile($createdFile);
    }

    /**
     * Return results of a SQL query
     *
     * @param $sql
     * @return mixed
     */
    protected function sqlQuery($sql)
    {
        /** @var Connection $db */
        $db = app(Connection::class);

        $results = $db->select($sql);
        $output = [];

        $count = 0;
        if (! empty($results)) {
            foreach($results as $key=>$value) {
               foreach($value as $childKey=>$childValue) {
                   $output[$count][$childKey] = $childValue;
               }
                $count++;
            }
        }

        return collect($output);
    }

    /**
     * Emails the report to the user
     *
     * @param $savedFile
     */
    protected function mail($createdFile)
    {
        /** @var Mailer $mailer */
        $mailer = app(Mailer::class);

        $mailer->send([], [], function(Message $message) use ($createdFile) {
            $message->subject($this->subject());
            $message->setBody($this->body());
            // if html then 'text/html' needs to be set.
            $message->from($this->from());
            $message->to($this->to());
            if (! empty($this->cc())) {
                $message->cc($this->cc());
            }
            if (! empty($this->bcc())) {
                $message->bcc($this->bcc());
            }
            $message->attach($createdFile , [
                'as' => $this->fileName() . '.' . $this->extension(),
                'mime' => Mime::getMimeType($this->extension())
            ]);
        });
    }

    /**
     * Deletes the temporary file
     */
    protected function destroyTemporaryFile($createdFile)
    {
        unlink($createdFile);
    }

    /**
     * Return instance of Excel
     *
     * @return Excel
     */
    protected function newExcel() {
        return new Excel(new PHPExcel(),
            new LaravelExcelReader(new Filesystem(), new FormatIdentifier(new Filesystem()), new Dispatcher(new Container())),
            new LaravelExcelWriter(new \Illuminate\Support\Facades\Response(), new Filesystem(), new FormatIdentifier(new Filesystem())));
    }

    protected function addSheetsToArray()
    {
        for ($i = 1; $i <= $this->maxSheets; $i++) {
            if (method_exists($this, 'sheet' . $i)) {
                $this->sheets[] = 'sheet' . $i;
            } else {
                break;
            }
        }
    }

    protected function addDataToArray()
    {
        foreach ($this->sheets as $sheet) {
            $this->sheetData[] = $this->{$sheet}();
        }
    }

    /**
     * @param $excel
     * @param $temporaryFileName
     * @return LaravelExcelWriter
     */
    protected function createExcelFile($excel, $temporaryFileName)
    {
        /**
         * @var LaravelExcelWriter $savedFile
         */
        $savedFile = $excel->create($temporaryFileName, function (LaravelExcelWriter $excel) {
            foreach ($this->sheetData as $key => $sheetDatum) {
                $excel->sheet('Sheet ' . ($key + 1), function (LaravelExcelWorksheet $sheet) use ($sheetDatum) {
                    if ($sheetDatum instanceof \Illuminate\Database\Eloquent\Model) {
                        $sheet->fromModel($sheetDatum);
                    } else {
                        $sheet->fromArray($sheetDatum);
                    }
                });
            }


        })->store($this->extension(), $this->saveTo());
        return $savedFile;
    }
}