<?php

/**
 * Define como constante todos os diret�rios do sistema
 * 
 * @author Niury Martins Pereira 
 */

define(SERVER, 'http://localhost/teleapae');

//diret�rios dos arquivos p�blicos
define(JS, SERVER . '/public/js');
define(CSS, SERVER . '/public/css');
define(IMG, SERVER . '/public/img');
define(FLASH, SERVER . '/public/flash');

//diret�rios da aplica��o
define(APPLICATION, './application');
define(CONF, APPLICATION . '/conf');
define(CLASSES, APPLICATION . '/classes');
define(MODULES, APPLICATION . '/modules');
define(INCLUDES, APPLICATION . '/includes');