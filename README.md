# Host Wordpress plugins

## Installation

Install the code in a directory on your web server that is accessible, e.g. http://www.example.com/api.
Create a 'plugins' directory on your web server. This directory will hold your plugins readme.txt files.
Create a 'downloads' directory on your web server that is not directly accessible via the net, i.e. below your www root. 
This folder will hold your zipped plugins.

## Configuration

Create a config.php file and place it in the root directory. In the config file, define the following constants:

define('BASE_URL','.../'); // Base URL of installation
define('PLUGINS_DIR','/path/to/plugins/'); // Plugins folder
define('DOWNLOAD_DIR','.../'); //Download folder

## Usage

Copy the file wp-plugin-update.php to your plugin folder and include the file at startup:

require(dirname(__FILE__).'/wp-plugin-update.php');

Open the file and edit the definition at the top of the file and set 'HOST_WP_PLUGINS_BASE_URL' to the same value as BASE_URL defined here above.

Do a search and replace of 'my_hosted_plugin' with the name of your plugin.
Do a search and replace of 'my_hosted_plugin_filename' with the name of your main plugin file. In most cases this will be the same as your plugin name.
Upload a zipped plugin to the 'downloads' folder and upload the readme.txt file to the 'plugins' folder. Note that in practice these folders 
could be in the same location.
