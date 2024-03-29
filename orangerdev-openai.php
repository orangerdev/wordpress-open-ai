<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ridwan-arifandi.com
 * @since             1.0.0
 * @package           Orangerdev_Openai
 *
 * @wordpress-plugin
 * Plugin Name:       OrangerDev OpenAI
 * Plugin URI:        https://ridwan-arifandi.com
 * Description:       Integrate Doctorjobs.today with OpenAI
 * Version:           1.0.0
 * Author:            Ridwan Arifandi
 * Author URI:        https://ridwan-arifandi.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       orangerdev-openai
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ORANGERDEV_OPENAI_VERSION', '1.0.0');
define('ORANGERDEV_OPENAI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ORANGERDEV_OPENAI_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-orangerdev-openai-activator.php
 */
function activate_orangerdev_openai()
{
  require_once plugin_dir_path(__FILE__) . 'includes/class-orangerdev-openai-activator.php';
  Orangerdev_Openai_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-orangerdev-openai-deactivator.php
 */
function deactivate_orangerdev_openai()
{
  require_once plugin_dir_path(__FILE__) . 'includes/class-orangerdev-openai-deactivator.php';
  Orangerdev_Openai_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_orangerdev_openai');
register_deactivation_hook(__FILE__, 'deactivate_orangerdev_openai');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-orangerdev-openai.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_orangerdev_openai()
{

  $plugin = new Orangerdev_Openai();
  $plugin->run();
}
run_orangerdev_openai();
