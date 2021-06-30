<?php
/*
* - ZS_RUN_ONCE_NODE - a Boolean flag stating whether the current node is
*   flagged to handle "Run Once" actions. In a cluster, this flag will only be set when
*   the script is executed on once cluster member, which will allow users to write
*   code that is only executed once per cluster for all different hook scripts. One example
*   for such code is setting up the database schema or modifying it. In a
*   single-server setup, this flag will always be set.
* - ZS_WEBSERVER_TYPE - will contain a code representing the web server type
*   ("IIS" or "APACHE")
* - ZS_WEBSERVER_VERSION - will contain the web server version
* - ZS_WEBSERVER_UID - will contain the web server user id
* - ZS_WEBSERVER_GID - will contain the web server user group id
* - ZS_PHP_VERSION - will contain the PHP version Zend Server uses
* - ZS_APPLICATION_BASE_DIR - will contain the directory to which the deployed
*   application is staged.
* - ZS_CURRENT_APP_VERSION - will contain the version number of the application
*   being installed, as it is specified in the package descriptor file
* - ZS_PREVIOUS_APP_VERSION - will contain the previous version of the application
*   being updated, if any. If this is a new installation, this variable will be
*   empty. This is useful to detect update scenarios and handle upgrades / downgrades
*   in hook scripts
* - ZS_<PARAMNAME> - will contain value of parameter defined in deployment.xml, as specified by
*   user during deployment.
*/

/***************************************************************************
 *	VARS
 **************************************************************************/
/** Deployment Hook Script Constants */
const ZS_CONSTANTS = [
	//custom constant defined in deployment.xml
	'ZS_APP_NAME',

	//constants set by Zendserver Deployment Daemon
	'ZS_APPLICATION_BASE_DIR',
	'ZS_RUN_ONCE_NODE',
	'ZS_CURRENT_APP_VERSION',
	'ZS_PHP_VERSION',
	'ZS_PREVIOUS_APP_VERSION',
	'ZS_PREVIOUS_APPLICATION_BASE_DIR',
	'ZS_WEBSERVER_GID',
	'ZS_WEBSERVER_TYPE',
	'ZS_WEBSERVER_UID',
	'ZS_WEBSERVER_VERSION',
	'ZS_BASE_URL',
];
