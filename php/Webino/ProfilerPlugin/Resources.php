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

use Zend_Application_Bootstrap_Bootstrapper as Bootstrapper;
use Webino_Resource_Profiler_Interface      as Profiler;

/**
 * Resources plugin for profiler
 *
 * Add resources table to profiler log.
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
class Webino_ProfilerPlugin_Resources
    implements Webino_ProfilerPlugin_Interface
{
    /**
     * Application bootstrapper
     *
     * @var Bootstrapper
     */
    private $_bootstrap;

    /**
     * Inject bootstrapper
     *
     * @param Bootstrapper $bootstrap
     *
     * @return Webino_ProfilerPlugin_Resources
     */
    public function setBootstrap($bootstrap)
    {
        $this->_bootstrap = $bootstrap;

        return $this;
    }

    /**
     * Initialize profiler plugin
     *
     * @param Profiler $profiler
     * @param array    $options
     */
    public function init(Profiler $profiler, array $options)
    {
        $plugins = $this->_bootstrap->getPluginResources();

        $pluginsTable = array(
            array('Resource', 'Plugin Resource Class', 'Resource Class')
        );

        foreach ($plugins as $resourceName => $object) {
            
            $resourceClass = null;

            if ($this->_bootstrap->hasResource($resourceName)) {
               $resourceClass = get_class(
                   $this->_bootstrap->getResource($resourceName)
               );
            }

            $pluginsTable[] = array(
                $resourceName, 
                get_class($this->_bootstrap->getPluginResource($resourceName)),
                $resourceClass
            );
        }

        $profiler->write(
            'send', $pluginsTable, sprintf(
                'Resources (%s):', count($plugins)
            ), 'TABLE'
        );
    }
}
