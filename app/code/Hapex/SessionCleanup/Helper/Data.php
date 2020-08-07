<?php
namespace Hapex\SessionCleanup\Helper;

use Hapex\Core\Helper\DataHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class Data extends DataHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
    }

    public function isEnabled()
    {
        return $this->getConfigFlag('hapex_sessioncleanup/general/enable');
    }

    public function getCookieLifetime()
    {
        return $this->getConfigValue('web/cookie/cookie_lifetime');
    }

    public function log($message)
    {
        $this->helperLog->printLog("hapex_session_cleanup", $message);
    }
}
