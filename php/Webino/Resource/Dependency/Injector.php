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
 * @subpackage Dependency
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/core/
 */

use Webino_Resource_Dependency_Injection_Interface as Injection;
use Webino_Resource_Dependency_Injector_Exception  as InjectorException;

/**
 * Dependency injector for dependency resource
 *
 * @category   Webino
 * @package    Core
 * @subpackage Dependency
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_Resource_Dependency_Injector 
    implements Webino_Resource_Dependency_Injector_Interface
{
    /**
     * List of injections objects
     *
     * @var array
     */
    private $_injections = array();

    /**
     * Add injection object
     *
     * @param string    $key       Injection ID
     * @param Injection $injection
     *
     * @return Webino_Resource_Dependency_Injector 
     */
    public function addInjection($key, Injection $injection)
    {
        $this->_injections[$key] = $injection;

        return $this;
    }

    /**
     * Return params from giver by injection and options
     *
     * example of options:
     *
     * - bootstrap.resource.frontController = frontController
     *
     * as pattern: (injection).(method).(paramkey) = (paramvalue)
     *
     * @param object $giver   Object to get params
     * @param array  $options Instructions
     *
     * @return array Params to inject
     */
    public function injection($giver, $options)
    {
        $injectionParams = array();
        
        foreach ($options as $injectionName=>$methods) {

            if (!isset($this->_injections[$injectionName])) {
                throw new InjectorException(
                    sprintf(
                        'Dependency Injection "%s" is not defined.',
                        $injectionName
                    )
                );
            }

            $this->_injections[$injectionName]->setGiver($giver);
            
            foreach ($methods as $methodName=>$methodOptions) {

                if (is_string($methodOptions)) {
                    $methodOptions = array($methodOptions);
                }

                foreach ($methodOptions as $params) {

                    if (!is_array($params)) {
                        $params = array($params);

                    }

                    $injectionParams[] = call_user_func_array(
                        array($this->_injections[$injectionName], $methodName),
                        $params
                    );
                }
            }
        }
        
        return $injectionParams;
    }

    /**
     * Perform injection
     *
     * example of options:
     *
     * - bootstrap.resource.frontController = frontController
     *
     * as pattern: (getter).(method).(setter) = (params)
     *
     * @param object $consumer
     * @param object $provider
     * @param array  $injectionSettings
     *
     * @return Webino_Resource_Dependency_Injector
     */
    public function inject($consumer, $provider, array $injectionSettings)
    {
        foreach ($injectionSettings as $getter=>$methods) {

            $getterFc = 'get' . ucfirst($getter);

            ksort($methods);

            foreach ($methods as $methodName=>$options) {
                foreach ($options as $setter=>$params) {

                    $params = $this->injection(
                        $provider->$getterFc($methodName), array(
                            $getter => array(
                                 $methodName => array(
                                    $setter => $params,
                                ),
                            ),
                        )
                    );

                    $setterFc = 'set' . ucfirst($setter);

                    $consumer->$setterFc(
                        current($params)
                    );
                }
            }

        }
        
        return $this;
    }
}
