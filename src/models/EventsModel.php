<?php

class EventsModel extends DB
{
    protected $eventDateTime;
    protected $eventAction;
    protected $callRef;
    protected $eventValue;
    protected $currencyCode;
    protected $table = "events";

    public function __construct()
    {
        $this->db = self::getInstance()->getConnection();
        $this->createEventsTable();
    }

    public function setDateTime($dateTime)
    {
        if (!$this->validateDate($dateTime)) {
            throw new Exception("eventDate is wrong format");
        }

        $this->eventDateTime = $dateTime;
    }

    public function setEventAction($action)
    {
        $length = strlen($action);
        if ($length < 1 || $length > 20) {
            throw new Exception("eventAction is required to be between 1-20 chars");
        }

        $this->eventAction = $action;
    }

    public function setCallRef($callRef)
    {
        if (!is_numeric($callRef)) {
            throw new Exception("callRef is numeric only");
        }

        $this->callRef = $callRef;
    }

    public function setEventValue($eventValue)
    {
        if ($eventValue && !is_numeric($eventValue)) {
        // if ($eventValue && !is_float($eventValue)) {
            throw new Exception("eventValue is wrong format"); 
        }

        $this->eventValue = $eventValue;
    }

    public function setCurrencyCode($currency)
    {
        if ($currency && strlen($currency) <> 3) {
            throw new Exception("currencyCode is wrong format");
        }

        $this->currencyCode = $currency;
    }

    public function create()
    {
        $stmt = $this->db->prepare(
            "INSERT INTO ".$this->table."(`dateTime`, `action`, callref, value, currency) "
            ."VALUES(:datetime, :action, :callref, :value, :currency)");
        $stmt->execute([
            ':datetime' => $this->eventDateTime,
            ':action' => $this->eventAction,
            ':callref' => $this->callRef,
            ':value' => $this->eventValue,
            ':currency' => $this->currencyCode,
        ]);

        return $this->db->lastInsertId();
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function createEventsTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT NOT NULL,
                `dateTime` DATETIME NOT NULL,
                `action` VARCHAR(100) NOT NULL,
                `callref` INT NOT NULL,
                `value` DECIMAL(8,2) DEFAULT NULL,
                `currency` VARCHAR(5) DEFAULT NULL,
                PRIMARY KEY(id))";
            $q = $this->db->query($sql);

            return true;
        } catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
