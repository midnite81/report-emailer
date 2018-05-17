<?php


class Dave extends \Midnite81\ReportEmailer\ReportEmailer
{

    /**
     * Data you want to be passed through to page one of the excel spreadsheet
     * (you can sequentially create sheet2, sheet3
     *
     * @return \Illuminate\Support\Collection|array|object
     */
    public function sheet1()
    {
        return ['a', 'b', 'c', 'd', 'e'];
        // Customers::all();
    }

    /**
     * The body of the email you will be sending with the report
     *
     * @return string
     */
    public function body()
    {
        return 'hello';
    }

    /**
     * Email address(es) of the sender
     *
     * @return string
     */
    public function from()
    {
        return 'simon@example.com';
    }

    /**
     * Email address(es) of To recipients
     *
     * @return array|string
     */
    public function to()
    {
        return 'dave@example.com';
    }

    /**
     * Email address(es) of CC recipients
     *
     * @return array|string|void
     */
    public function cc()
    {
        // TODO: Implement cc() method.
    }

    /**
     * Email address(es) of BCC recipients
     *
     * @return array|string|void
     */
    public function bcc()
    {
        // TODO: Implement bcc() method.
    }

    /**
     * Fully qualified save path
     *
     * @return mixed
     */
    public function saveTo()
    {
        return __DIR__;
    }

    /**
     *
     *
     * @return mixed
     */
    public function extension()
    {
        return 'xls';
    }
}