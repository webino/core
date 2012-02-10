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
 * @subpackage ProfilerPlugin
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Webino_Resource_Profiler_Interface as Profiler;

/**
 * Abstract class for profiler plugins
 *
 * @category   Webino
 * @package    Core
 * @subpackage ProfilerPlugin
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
abstract class Webino_ProfilerPlugin_Abstract
    implements Webino_ProfilerPlugin_Interface
{
    /**
     * Profiler object
     *
     * @var object
     */
    protected $_profiler;

    /**
     * Inject profiler object
     *
     * @param object $profiler
     * 
     * @return Webino_ProfilerPlugin_Abstract
     */
    protected function _setProfiler($profiler)
    {
        $this->_profiler = $profiler;

        return $this;
    }

    /**
     * Returns group title
     *
     * @param string $message
     *
     * @return string
     */
    protected function _groupTitle($message)
    {
        return $message;
    }

    /**
     * Initialize profiler plugin
     *
     * @param Profiler $profiler Resource profiler
     * @param array    $options
     */
    public function init(Profiler $profiler, array $options)
    {
        $this->writeProfile($profiler);
    }

    /**
     * Write profile log to profiler
     *
     * @param Profiler $profiler Profiler resource
     */
    public function writeProfile(Profiler $profiler)
    {
        $lastGroup = null;

        $profile = $this->_profiler->getProfile();

        $profilesCount = count($profile);

        foreach ($profile as $groupKey => $group) {

            if (!$lastGroup && 'default' != $groupKey) {
                $msg = sprintf($groupKey, count($profile[$groupKey]));
                $profiler->write('group', $this->_groupTitle($msg));
            }

            if ($lastGroup && $lastGroup != $groupKey) {
                $profiler->write('groupEnd');
                $lastGroup = null;
            } else {
                if (!$lastGroup) {
                    $lastGroup = $groupKey;
                }
            }

            $this->_profileGroup($profiler, $group);
        }

        if (1 == $profilesCount) {
            $profiler->write('groupEnd');
            $lastGroup = null;
        }
    }

    /**
     * Write group to profile
     *
     * @param Profiler $profiler
     * @param array    $group
     *
     * @return Webino_ProfilerPlugin_Abstract
     */
    private function _profileGroup(Profiler $profiler, array $group)
    {
        foreach ($group as $params) {

            $type = key($params);

            switch ($type) {

                case 'group':
                case 'send':
                    $params = current($params);
                    foreach ($params as &$param) {
                        if (is_object($param)
                            && method_exists($param, '__toString')
                        ) {
                            $param = (string) $param;
                        }
                    }
                    call_user_func_array(
                        array($profiler, 'write'),
                        array_merge(array($type), $params)
                    );
                    break;
                case 'groupEnd':
                    $profiler->write($type);
                    break;

            }
        }

        return $this;
    }
}
