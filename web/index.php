<?php
/**
* Rosetta Blog
*   Copyright © 2010 Theodore R. Smith <theodore@phpexperts.pro>
* 
* The following code is licensed under a modified BSD License.
* All of the terms and conditions of the BSD License apply with one
* exception:
*
* 1. Every one who has not been a registered student of the "PHPExperts
*    From Beginner To Pro" course (http://www.phpexperts.pro/) is forbidden
*    from modifing this code or using in an another project, either as a
*    deritvative work or stand-alone.
*
* BSD License: http://www.opensource.org/licenses/bsd-license.php
**/

/**
* MVC stands for the Model View Controller Design Pattern.
* 
* This is a very popular design pattern for websites because it 
*    * allows for far less cluttered and more organized code
*    * makes it far more easier to share code between pages and to add new pages
*    * separates XHTML, CSS and JavaScript (the Views), allowing for
*      XHTML editing by persons unfamiliar with PHP (important for businesses)
*    * separates business logic (Model) from the View
*    * acts as a defacto standard for organizing a site that professionals readily comprehend.
*/

/* This file, index.php, will be our Front-Controller.  This file's responsibility will be to
   make the MVC work by acting as the front glue that ties the various components together.
   
   Every page in our web app will be determined based on what view it is calling and what action
   it is performing.  Let's call our default view "home" and our default action "index".
*/
// 1. Initialize the web application.
// 1a. Set up the app path constant.
// realpath() will take any UNIX-style path, even relative ones, like ".." and
// return (if the file/directory exists) the auctual OS-styled path name.
define('ROSETTA_APP_PATH', realpath(dirname(__FILE__) . '/../'));

// 1b. Load the database abstraction library.
require ROSETTA_APP_PATH . '/lib/MyDB.inc.php';

// 1c. Load helper functions.
require ROSETTA_APP_PATH . '/lib/misc.inc.php';
/*
$config = new MyDBConfigStruct;
$config->hostname = 'localhost';
$config->username = 'xmule';
$config->password = 'VZvSLdbjMPCxBZX7';
$config->database = 'xmule_drupal';
MyDB::loadDB($config);
unset($config);
*/

// 2. Start up the SimpleConfig library.
require ROSETTA_APP_PATH . '/lib/SimpleConfig.php';
SimpleConfig::setConfig(ROSETTA_APP_PATH . '/config.php');

// 3. Setup the __autoload magic function to handle loading all of our
//    required files.
function __autoload($name)
{
	if (strpos($name, 'Controller') !== false)
	{
		require ROSETTA_APP_PATH . '/controllers/' . $name . '.inc.php';
	}
	else if (strpos($name, 'Manager') !== false)
	{
		require ROSETTA_APP_PATH . '/managers/' . $name . '.inc.php';
	}
	else if (($index = strpos($name, 'ThemeEngine')) !== false)
	{
		$platform = substr($name, 0, $index);
		require ROSETTA_APP_PATH . '/platforms/' . $platform . '/' . $name . '.inc.php';
	}
	else if (($index = strpos($name, 'ArticleEngine')) !== false)
	{
		if (file_exists(ROSETTA_APP_PATH . '/platforms/' . $name . '.inc.php'))
		{
			require ROSETTA_APP_PATH . '/platforms/' . $name . '.inc.php';
		}
		else
		{
			$platform = substr($name, 0, $index);
			require ROSETTA_APP_PATH . '/platforms/' . $platform . '/' . $name . '.inc.php';
		}
	}
	else if (file_exists(ROSETTA_APP_PATH . '/misc/' . $name . '.inc.php'))
	{
		require ROSETTA_APP_PATH . '/misc/' . $name . '.inc.php';
	}
	else
	{
		// Let's see if it's a model.
		$filename = ROSETTA_APP_PATH . '/models/' . $name . '.inc.php';
		if (file_exists($filename))
		{
			require $filename;
		}
	}

	// If all else fails...
	return false;
}

// 4. Load the Theme Manager.
$config = SimpleConfig::getInstance();
$themeEngine = new ThemeManager($config['theme_platform']);
$themeEngine->setTheme($config['theme']);

// 4. Load the appropriate view.
$viewController = new ViewController($themeEngine);

$viewController->displayView();

