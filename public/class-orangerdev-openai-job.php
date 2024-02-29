<?php

namespace OrangerDevOpenAI\Front;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Orangerdev_Openai
 * @subpackage Orangerdev_Openai/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Orangerdev_Openai
 * @subpackage Orangerdev_Openai/public
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Job
{

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of the plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * Add JS script to job preview form
   * Hooked via djt/job-submit-preview-form/after, priority 10
   * @author  Ridwan Arifandi
   * @since   1.0.0
   * @param   WP_Post           $job_post
   * @param   WP_Job_Board_Form $form_object
   */
  public function add_enhancement_preview_form($job_post, $form_object)
  {
    $superio_options = get_option('superio_theme_options');

    if ($superio_options['openai_api_key'] != true)
      return;

    if ($superio_options['openai_job_improvement_enable'] != true)
      return;

    $is_improved = get_post_meta($job_post->ID, '_openai_job_improvement', true);

    // if ($is_improved)
    //   return;

    require_once plugin_dir_path(__FILE__) . 'partials/after-job-preview.php';
  }
}
