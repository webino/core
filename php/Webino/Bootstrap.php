<?php
/**
 * Webino
 *
 * PHP version 5.3
 *
 * LICENSE: This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL: http://www.webino.org/license/
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to license@webino.org
 * so we can send you a copy immediately.
 *
 * @category   Webino
 * @package    Core
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

/**
 * Webino application bootstrap for Zend Framework
 *
 * At this time it only supports requiring files via config.
 *
 * @category   Webino
 * @package    Core
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_Bootstrap
    extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Configuration key name of requrie option
     */
    const REQUIRE_KEYNAME = 'require';

    /**
     * Require files defined in config
     *
     * example of options:
     *
     * require.anyFile = Webino/file.php
     *
     * Of course that files must be in include path.
     *
     * @param array $options
     */
    protected function _initRequire()
    {
        if (!$this->hasOption(self::REQUIRE_KEYNAME)) {
            
            return;
        }

        foreach ($this->getOption(self::REQUIRE_KEYNAME) as $filepath) {
            require $filepath;
        }
    }
}
