<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
/**
 * Class EEmailAutoloader
 * 
 * @since  0.9.0
 */
class EEsenderAutoloader {

    /**
     * An instance of this autoloader
     *
     * @var   EEsenderAutoloader
     */
    public static $autoloader = null;

    /**
     * The path to the EEmail root directory
     *
     * @var   string
     */
    public static $eesenderPath = null;

    /**
     * Initialise this autoloader
     *
     * @return  EEsenderAutoloader
     */
    public static function init() {
        if (self::$autoloader == null) {
            self::$autoloader = new self;
        }
        return self::$autoloader;
    }

    /**
     * Public constructor. Registers the autoloader with PHP.
     */
    public function __construct() {
        self::$eesenderPath = realpath(__DIR__ . '/');
        spl_autoload_register(array($this, 'autoload_libraries'));
    }

    /**
     * The actual autoloader
     * @param   string  $class_name  The name of the class to load
     * @return  void
     */
    public function autoload_libraries($class_name) {
        // Make sure the class has a EEmail prefix
        if (substr(strtolower($class_name), 0, 8) != 'eesender') {
            return;
        }
        $path = self::$eesenderPath . '/' . $class_name . '.php';
      
        if (@file_exists($path)) {
            include_once $path;
        }
    }
}
