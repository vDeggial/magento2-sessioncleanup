<?php

namespace Hapex\SessionCleanup\Cron;
use Hapex\Core\Cron\BaseCron;
use Hapex\Core\Helper\LogHelper;
use Magento\Framework\App\ResourceConnection;
use Hapex\SessionCleanup\Helper\Data as DataHelper;

class Cleanup extends BaseCron
{
    protected $resource;
    private $tableSession;

    public function __construct(DataHelper $helperData, LogHelper $helperLog, ResourceConnection $resource)
    {
        parent::__construct($helperData, $helperLog);
        $this->resource = $resource;
        $this->tableSession = $this->resource->getTableName("session");
    }

    public function cleanSessions()
    {
        switch ($this->helperData->isEnabled()) {
            case true:
                try {
                    $this->helperData->log("");
                    $this->helperData->log("Starting Session Cleanup");
                    $this->deleteSessions();
                    $this->optimizeSessionTable();
                    $this->helperData->log("Ending Session Cleanup");
                } catch (\Exception $e) {
                    $this->helperData->errorLog(__METHOD__, $e->getMessage());
                } finally {
                    return $this;
                }
        }
    }

    private function deleteSessions()
    {
        try {
            $connection = $this->resource->getConnection();
            $table = $this->tableSession;
            $cookieLifetime = $this->helperData->getCookieLifetime();
            $cookieLifetime = !empty($cookieLifetime) ? $cookieLifetime : 3600;
            $this->helperData->log("- Looking for sessions expired within $cookieLifetime seconds");
            $sql = "DELETE FROM $table WHERE session_expires < UNIX_TIMESTAMP() - $cookieLifetime;";
            $result = $connection->query($sql);
            $count = $result->rowCount();
            $this->helperData->log("- Cleaned $count expired sessions");
        } catch (\Exception $e) {
            $this->helperData->errorLog(__METHOD__, $e->getMessage());
        }
    }
    private function optimizeSessionTable()
    {
        try {
            $connection = $this->resource->getConnection();
            $table = $this->tableSession;
            $sql = "OPTIMIZE TABLE $table;";
            $this->helperData->log("- Optimizing sessions table");
            $connection->query($sql);
            $this->helperData->log("- Sessions table optimized");
        } catch (\Exception $e) {
            $this->helperData->errorLog(__METHOD__, $e->getMessage());
        }
    }
}
