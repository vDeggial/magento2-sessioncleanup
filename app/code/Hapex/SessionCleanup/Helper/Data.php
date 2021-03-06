<?php

namespace Hapex\SessionCleanup\Helper;

use Hapex\Core\Helper\DataHelper;

class Data extends DataHelper
{
    protected const XML_PATH_CONFIG_ENABLED = "hapex_sessioncleanup/general/enable";
    protected const XML_PATH_CONFIG_COOKIE_LIFETIME = "web/cookie/cookie_lifetime";
    protected const FILE_PATH_LOG = "hapex_session_cleanup";

    public function isEnabled()
    {
        return $this->getConfigFlag(self::XML_PATH_CONFIG_ENABLED);
    }

    public function getCookieLifetime()
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_COOKIE_LIFETIME);
    }

    public function log($message)
    {
        $this->helperLog->printLog(self::FILE_PATH_LOG, $message);
    }
}
