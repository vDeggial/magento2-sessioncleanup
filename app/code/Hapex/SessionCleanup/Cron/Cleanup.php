<?php
namespace Hapex\SessionCleanup\Cron;

use Hapex\SessionCleanup\Helper\Data as DataHelper;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class Cleanup
{
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    private $helperData;

    public function __construct(DataHelper $helperData, ResourceConnection $resource, LoggerInterface $logger)
    {
        $this->helperData = $helperData;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    public function cleanSessions()
    {
        switch ($this->helperData->isEnabled()) {
            case true:
                try {
                    $this->helperData->log("");
                    $this->helperData->log("Starting Session Cleanup");
                    $this->deleteSessions();
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
            $table = $this->resource->getTableName("session");
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
}
