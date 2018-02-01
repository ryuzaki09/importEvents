<?php

class FileProcessor
{
    private $file;
    private $row;
    private $headers = array("eventDatetime", "eventAction", "callRef", "eventValue", "eventCurrencyCode");

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function setRow($row)
    {
        $this->row = $row;
    }

    public function checkFileExtension()
    {
        $file_parts = pathinfo($this->file);
        //If file is not csv then skip to next
        if ($file_parts['extension'] != "csv") {
            return false;
        }
        
        return true;
    }



    public function checkHeaders()
    {
        foreach ($this->headers as $header) {
            if (!in_array($header, $this->row)) {
                throw new Exception("Header: ".$header." is missing");
            }
        }
    }

    public function importData()
    {
        try {
            echo "row: \n";
            if (empty($this->row[0])) {
                echo "empty row \n";
                throw new Exception("Empty row");
            }

            $eventModel = new EventsModel;
            $eventModel->setDateTime($this->row[0]);
            $eventModel->setEventAction($this->row[1]);
            $eventModel->setCallRef($this->row[2]);
            // $eventdate = $this->row[0];
            // $eventAction = $this->row[1];
            $eventValue = (isset($this->row[3]))
                ? $this->row[3]
                : null;
            $currency = ($eventValue && isset($this->row[4]))
                ? $this->row[4]
                : null;

            //if event value is not null and currency is empty, currency is required
            if (is_null($currency) && $eventValue) {
                throw new Exception("currency is required");
            }
            $eventModel->setEventValue($eventValue);
            $eventModel->setCurrencyCode($currency);
            return $eventModel->create();

        } catch (Exception $e) {
            throw $e;
        }
    }

}
