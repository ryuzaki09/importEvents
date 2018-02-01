<?php 

class SysLogger extends DB
{
    private $db;
    protected $logTable = "sysLog";
    protected $cronTable = "cronTasks";
    protected $taskName;

    public function __construct()
    {
        $db = self::getInstance();        
        $this->db = $db->getConnection();
        $this->createTables();
    }

    public function setTaskName($taskName)
    {
        $this->taskName = (string) $taskName;
    }

    public function checkIfTaskRunning()
    {
        $stmt = $this->db->prepare("SELECT id FROM ".$this->cronTable." WHERE `desc`=? AND endtime IS NULL ORDER BY id desc");
        $stmt->execute(array($this->taskName));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function start()
    {
        if (!$this->taskName) {
            throw new Exception("task name not set");
        }

        $stmt = $this->db->prepare("INSERT INTO ".$this->cronTable."(`desc`, `starttime`) VALUES(:desc, NOW())");
        $stmt->execute(array(':desc' => $this->taskName));
        return $this->db->lastInsertId();
    }

    public function info($msg)
    {
        $stmt = $this->db->prepare("INSERT INTO ".$this->logTable."(`desc`, `createdDate`) VALUES(:desc, NOW())");
        $stmt->execute(array(':desc' => $msg));

        $affectedrows = $stmt->rowCount();
        return $affectedrows;
    }

    private function createTables()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS sysLog (
                id INT AUTO_INCREMENT NOT NULL,
                `desc` LONGTEXT NOT NULL,
                `createdDate` DATETIME NOT NULL,
                PRIMARY KEY(id))";
            $q = $this->db->query($sql);

            $taskTable = "CREATE TABLE IF NOT EXISTS cronTasks (
                id INT AUTO_INCREMENT NOT NULL,
                `desc` TEXT NOT NULL,
                `starttime` DATETIME,
                `endtime` DATETIME,
                PRIMARY KEY(id))";
            $q = $this->db->query($taskTable);

            return true;
        } catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    
    public function endJob($taskId)
    {
        $stmt = $this->db->prepare("UPDATE ".$this->cronTable." SET endtime=NOW() WHERE id=?");
        $stmt->execute(array($taskId));

        return $stmt->rowCount();
    }

}
