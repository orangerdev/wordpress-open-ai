<?php

namespace OrangerDevOpenAI;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Orangerdev_Openai
 * @subpackage Orangerdev_Openai/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Orangerdev_Openai
 * @subpackage Orangerdev_Openai/admin
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Admin
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
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * Add options to Doctor Jobs Today theme options
   * Hooked via filter superio_redux_framwork_configs, priority 9999
   */
  public function register_setting(array $sections)
  {

    $fields = apply_filters("orangerdev/openai/settings_fields", [
      [
        'id' => 'openai_api_enabled',
        'type' => 'switch',
        'title' => 'Enable OpenAI API',
        'default' => false,
      ],
      [
        'id' => 'openai_api_key',
        'type' => 'text',
        'title' => 'API Key',
        'default' => '',
      ],
      [
        'id' => 'openai_api_model',
        'type' => 'select',
        'title' => 'API Model',
        'options' => [
          'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
          'gpt-4' => 'GPT-4',
          'gpt-4-turbo-preview' => 'GPT-4 Turbo Preview',
          'gpt-4-0125-preview' => 'GPT-4 0.125 Preview',
          'gpt-4-0613' => 'GPT-4 0.613',

        ],
        'default' => 'gpt-4',
      ],
      [
        'id' => 'openai_api_temperature',
        'type' => 'text',
        'title' => 'API Temperature',
        'default' => 0.7,
      ],
      [
        'id' => 'openai_api_max_tokens',
        'type' => 'text',
        'title' => 'API Max Tokens',
        'default' => 100,
      ],
      [
        'id' => 'openai_language',
        'type' => 'select',
        'title' => 'Language',
        'options' => [
          'en' => 'English',
          'Bahasa Indonesia' => 'Bahasa Indonesia'
        ],
        'default' => 'en',
      ],
      [
        'id' => 'openai_location',
        'type' => 'text',
        'title' => 'Location',
        'default' => 'Indonesia',
      ]
    ]);

    $sections[] = [
      'title'   => esc_html__('OpenAI', 'doctor-jobs-today'),
      'fields'  => $fields
    ];

    return $sections;
  }
}
