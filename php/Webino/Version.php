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
 * Class to store and retrieve the version of Webino
 *
 * @category   Webino
 * @package    Core
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
final class Webino_Version
{
    /**
     * Webino version identification - see compareVersion()
     */
    const VERSION = '0.1.0alpha';

    /**
     * URL to get the latest version of Webino
     *
     * @var string
     */
    public static $latestVersionUrl = 'http://pear.webino.org/core/version';
    
    /**
     * The latest stable version Webino available
     *
     * @var string
     */
    private static $_latestVersion;

    /**
     * Compare the specified Webino version string $version
     * with the current Webino_Version::VERSION of Webino.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * 
     * @return int -1 if the $version is older,
     *             0 if they are the same,
     *             and +1 if $version is newer.
     *
     */
    public static function compareVersion($version)
    {
        return version_compare(strtolower($version), strtolower(self::VERSION));
    }

    /**
     * Fetches the version of the latest stable release
     *
     * @return string
     */
    public static function getLatest()
    {
        if (null === self::$_latestVersion) {
            self::$_latestVersion = 'not available';

            $handle = fopen(self::$latestVersionUrl, 'r');
            if (false !== $handle) {
                self::$_latestVersion = stream_get_contents($handle);
                fclose($handle);
            }
        }

        return self::$_latestVersion;
    }
}
