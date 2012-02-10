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
 * @subpackage Tool
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

/**
 * Version tool provider
 *
 * @category   Webino
 * @package    Core
 * @subpackage Tool
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_Tool_Version
    extends Zend_Tool_Framework_System_Provider_Version
{
    /**
     * Major version key
     */
    const MODE_MAJOR = 'major';

    /**
     * Minor version key
     */
    const MODE_MINOR = 'minor';

    /**
     * Bugfix version key
     */
    const MODE_MINI  = 'mini';

    /**
     * Version numbers separator
     */
    const SEPARATOR = '.';

    /**
     * Tool specialities
     *
     * @var array
     */
    protected $_specialties = array('MajorPart', 'MinorPart', 'MiniPart');

    /**
     * Returns provider name
     *
     * @return string
     */
    public function getName()
    {
        return 'version';
    }

    /**
     * Shows Webino version
     *
     * @param string $mode
     * @param bool   $nameIncluded
     */
    public function show($mode = null, $nameIncluded = true)
    {
        $versionInfo = $this->_splitVersion();

        switch($mode) {
            case self::MODE_MINOR:
                unset($versionInfo[self::MODE_MINI]);
                break;
            case self::MODE_MAJOR:
                unset($versionInfo[self::MODE_MINI]);
                unset($versionInfo[self::MODE_MINOR]);
                break;
        }

        $output = join(self::SEPARATOR, $versionInfo);

        if ($nameIncluded) {
            $output = 'Webino Version: ' . $output;
        }

        $this->_registry->response->appendContent($output);
    }

    /**
     * Shows Webino version major part
     *
     * @param bool $nameIncluded
     */
    public function showMajorPart($nameIncluded = true)
    {
        $this->_registry->response->appendContent(
            $this->_output(
                $nameIncluded, 'Webino Major Version: ', self::MODE_MAJOR
            )
        );
    }

    /**
     * Shows Webino version minor part
     *
     * @param bool $nameIncluded
     */
    public function showMinorPart($nameIncluded = true)
    {
        $this->_registry->response->appendContent(
            $this->_output(
                $nameIncluded, 'Webino Minor Version: ', self::MODE_MINOR
            )
        );
    }

    /**
     * Shows Webino version mini part
     *
     * @param bool $nameIncluded
     */
    public function showMiniPart($nameIncluded = true)
    {
        $this->_registry->response->appendContent(
            $this->_output(
                $nameIncluded, 'Webino Mini Version: ', self::MODE_MINI
            )
        );
    }

    /**
     * Returns version numbers exploded by separator in array
     *
     * @return array
     */
    protected function _splitVersion()
    {
        list($major, $minor, $mini) = explode(
            self::SEPARATOR, Webino_Version::VERSION
        );

        return array(
            self::MODE_MAJOR => $major,
            self::MODE_MINOR => $minor,
            self::MODE_MINI  => $mini
        );
    }

    /**
     * Returns version number with name if nameIncluded is true
     *
     * @param string $nameIncluded
     * @param string $name
     * @param string $mode
     *
     * @return string
     */
    private function _output($nameIncluded, $name, $mode)
    {
        $versionNumbers = $this->_splitVersion();

        if ($nameIncluded) {
            
            return $name . $versionNumbers[$mode];
        }

        return $versionNumbers[$mode];
    }
}
