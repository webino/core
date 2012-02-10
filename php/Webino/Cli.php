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

use Webino_Tool_Framework_Client_Console as Console;

/**
 * Provides information about Webino version
 */
require_once 'Webino/Version.php';

/**
 * Class to handle CLI
 *
 * @category   Webino
 * @package    Core
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/core/
 */
class Webino_Cli
{
    /**
     * Tool classes namespace
     */
    const TOOL_NAMESPACE = 'Webino_Tool_';

    /**
     * $_SERVER arguments key
     */
    const ARGS_KEYNAME = 'argv';

    /**
     * Key for note line
     */
    const NOTE_KEY = 'Command Line Console Tool';

    /**
     * Bold Webino title
     */
    const WEBINO_POWER = "\033[1mWebino\033[0m";

    /**
     * Key for error line
     */
    const ERROR_KEY = 'An Error Has Occurred';

    /**
     * Key for provider required line
     */
    const PROVIDER_KEY = 'A provider is required.';

    /**
     * Zf in color match pattern
     */
    const ZF_COLORMATCH = "\033[36mzf\033[m";

    /**
     * Zf match pattern
     */
    const ZF_MATCH = 'zf ';

    /**
     * Help argument
     */
    const HELP_ARG = '?';

    /**
     * Action name of show
     */
    const SHOW_ACTIONNAME = 'show';

    /**
     * Provider name of version
     */
    const VERSION_PROVIDERNAME = 'version';

    /**
     * Console client
     *
     * @var Console
     */
    private $_console;

    /**
     * Classes of tools
     * 
     * @var array
     */
    private $_classesToLoad;

    /**
     * CLI note
     *
     * @var string
     */
    private $_note;

    /**
     * To string content
     *
     * @var string
     */
    private $_asString;

    /**
     * Construct Webino Cli object
     *
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->_console = $console;
    }

    /**
     * Load tools from filepaths to classes
     *
     * @param array $files Paths to tool classes
     *
     * @return Webino_Cli
     */
    public function loadTool($files)
    {
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->_classesToLoad[$name] = self::TOOL_NAMESPACE . $name;
        }

        return $this;
    }

    /**
     * Return true if command has arguments
     *
     * @return bool
     */
    private function _hasArguments()
    {
        if ((1 == count($_SERVER[self::ARGS_KEYNAME]))
            || (self::HELP_ARG == $_SERVER[self::ARGS_KEYNAME][1])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Dispatch console
     *
     * @return Webino_Cli 
     */
    public function dispatch()
    {
        if (empty($this->_classesToLoad)) {
            throw new UnexpectedValueException(
                "There are no tools for Webino!"
            );
        }

        $this->_console->setClassesToLoad($this->_classesToLoad);

        if ($this->_hasArguments()) {
            $this->_asString = $this->_formatOutput(
                $this->_consoleDispatch($this->_console)
            );
        } else {
            foreach (array_keys($this->_classesToLoad) as $toolName) {
                $_SERVER[self::ARGS_KEYNAME] = array(
                    self::HELP_ARG, $toolName,
                );

                $this->_asString.= $this->_formatOutput(
                    $this->_consoleDispatch($this->_console)
                );
            }
        }

        return $this;
    }

    /**
     * Formats output from dispatch
     *
     * @param string $output
     *
     * @return string
     */
    private function _formatOutput($output)
    {
        $lines = explode(PHP_EOL, $output);

        foreach ($lines as $index=>&$line) {
            if (strstr($line, self::NOTE_KEY)) {
                $this->_setNote($line);
                unset($lines[$index]);

            } elseif (
                strstr($line, self::ERROR_KEY)
                && isset($lines[$index+1])
                && !strstr($lines[$index+1], self::PROVIDER_KEY)
            ) {
                $lines = array($line, $lines[$index+1]);
                break;
            }
        }

        return trim(join(PHP_EOL, $lines)) . str_repeat(PHP_EOL, 2);
    }

    /**
     * Dispatch console and catch output
     *
     * @param Console $console
     *
     * @return Webino_Cli
     */
    private function _consoleDispatch(Console $console)
    {
        ob_start();
        $console->dispatch();
        
        return ob_get_clean();
    }

    /**
     * Set CLI note
     *
     * @param string $note
     * 
     * @return Webino_Cli 
     */
    private function _setNote($note)
    {
        $this->_note = sprintf(
            self::WEBINO_POWER . " CLI v%s powered by %s",
            Webino_Version::VERSION, trim($note)
        ) . PHP_EOL . PHP_EOL;

        return $this;
    }

    /**
     * Return CLI output
     *
     * @return string
     */
    public function __toString()
    {
        $this->_asString = str_replace(
            self::ZF_COLORMATCH, "\033[36mwebino\033[m",
            str_replace(
                self::ZF_MATCH, 'webino ', trim($this->_asString)
            )
        );

        return PHP_EOL . $this->_note . $this->_asString . PHP_EOL . PHP_EOL;
    }
}
