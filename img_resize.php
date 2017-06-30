<?php

/**
 * PhpThumb Library Example File
 * 
 * This file contains example usage for the PHP Thumb Library
 * 
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 * 
 * Author(s): Ian Selby <ian@gen-x-design.com>
 * 
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright (c) 2009 Gen X Design
 * @link http://phpthumb.gxdlabs.com
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @subpackage Examples
 * @filesource
 */
require_once ('./application/classes/class.ThumbLib.php');

$_GET['width'] = isset($_GET['width']) ? (int)$_GET['width'] : 100 ;
$_GET['height'] = isset($_GET['height']) ? (int)$_GET['height'] : 75 ;

$thumb = PhpThumbFactory::create($_GET['img']);

if (isset($_GET['width']) and isset($_GET['height'])) {
    $thumb->adaptiveResize($_GET['width'], $_GET['height']);
}
else {
    $thumb->adaptiveResize(100, 75);
}

if (isset($_GET['reflection'])) {
	$thumb->createReflection(40, 30, 40, false, '#a4a4a4');
}

$thumb->show();