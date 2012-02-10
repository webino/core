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
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

/**
 * Firebug writer for profiler resource
 *
 * @category   Webino
 * @package    Core
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_Resource_Profiler_Writer_Firebug
    implements Webino_Resource_Profiler_Writer_Interface
{
    /**
     * Write profile log
     *
     * @param array $options
     * @param mixed $callback
     *
     * @return mixed False if callback hasn't method
     */
    public function write(
        array $options, $callback = 'Zend_Wildfire_Plugin_FirePhp'
    )
    {
        $method = $options[0];

        if (!method_exists($callback, $method)) {

            return false;
        }

        array_shift($options);

        switch ($method) {
            case 'group':
                $options[4] = array('Collapsed' => true);
                break;
        }

        call_user_func_array(
            array($callback, $method), $options
        );
    }
}
