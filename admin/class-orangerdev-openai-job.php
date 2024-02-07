<?php

namespace OrangerDevOpenAI\Admin;

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
   * The role of AI
   *
   * @since    1.0.0
   * @access   private
   * @var      array    $role    The role of this plugin.
   */
  protected $system_role;

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
    $this->system_role = [
      "role" => "system",
      "content" => 'You are an SEO writer working on a Job Portal for Healthcare Professionals. You are helping improve job descriptions posted by employers looking to these Healthcare Professionals.  You have a background in medicine and understand the roles and responsibilities that healthcare professionals undertake in their jobs.

Your daily tasks are:

1. Write job posts optimized for SEO-impact, that include relevant keywords related to job hunting, the employer posting the job and the location of the job
2. Write job posts that contain enough detail to inform the Healthcare Professional on their roles and responsibilities in the job, the qualifications required and why they should apply 
3. Write job descriptions in a specific format that includes 5 sections - a "Job Summary", "roles & responsibilities", "qualifications", "shift timings" and "why apply?"
4. Translate the post to any language that we need

Your output should always be a job description that is optimized for SEO, is informative to the user considering applying, and follows the format as described above.'
    ];
  }

  /**
   * Register setting to OpenAI theme setting
   * Hookedi via orangerdev/openai/settings_fields, priority 10
   */
  public function register_setting(array $fields)
  {

    return array_merge($fields, [
      [
        'id' => 'openai_job_improvement_enable',
        'type' => 'switch',
        'title' => 'Enable Job Description Improvement',
        'default' => false,
      ]
    ]);
  }

  /**
   * Main prompt
   * @return string
   */
  private function main_prompt()
  {
    return "Read the provided job description written by {employer_name} an {employer_type} (Employer Type) in {country} and help me optimize this Job Description to provide more context to the relevant healthcare professional and so that it ranks well on SEO.

    {job_content}

When optimizing this JD follow the following instructions -

Understand the relevant target audience
-This job is posted for DOKTER UMUM (Aesthetic Doctor), and is meant to be advertised to a specific specialty of HCP, please optimize the job description for the specific specialty and job title provided.

Improve the roles and responsibilities section
-Whenever there is key information missing that would highlight the key roles and responsibilities expected of the HCP, please extrapolate the responsibilities that a doctor within the relevant specialty would conduct and add the new details to the job description. For example, if the role is for an internal medicine consultant - their duties will include conducting rounds, if the role is for an emergency physician their duties - a key responsibility would be to conduct the triage of patients. Similar to the examples, please add key details to the roles and responsibilities section by extrapolating the information provided in the existing JD and the Job Title.
-Contextualize the employer that is posting this job, you can find the profile details of the employer here below.

{employer_content}

-The job description should be optimized with the relevant information of the employer considered. For Example, If the the employer is a clinic - then the HCP will not have to conduct rounds, if the employer is a Dialysis center - the employees roles and responsibilities will cover the administering of Dialysis treatments. Similar to the examples provided, the job description's roles & responsibilities should be contextual to the employer based on the information provided above.

Improve the JD so that it ranks better on search engines
-Come up a with a few recommended SEO keywords before generating the copy that could help this job description rank well on Search Engines. The keywords should be location specific and highlight the fact that this is a job opening for a {job_title}.

Reorganize the JD into a specific format as outlined below, pleases consider the key requirements within each section -
-Summary: Write a 2 sentence summary of the role and the employer
-Roles & responsibilities: Include the header, Write the roles & responsibilities in bullet point format, after they have been optimized on the basis of the instructions provided above
-Requirements: Include the header, write the requirements of the ideal candidate in bullet point format
-Shift/Timings: If the shift and/or timings is not provided in the job description, please add this as a heading and leave this section blank
-Why Join?: 1 sentence summary of why the candidate should apply to this job based on the description of the employer provided above

As the output please only provide the optimized JD with the improvements and in the format as described above {translate_to}

Make sure :
- your respond ONLY in save JSON object with format data: {summary: string, roles&responsibilities: string, requirements: string, shift_timings: string, why_join: string}
- write any value in HTML-safe, don't put any symbol or special character.
- Do not put any HTML tags since we will show the value in textarea and later users with no-techinal background will see the value.
- separate new line with \\n
- if you need to write list, use this simbol &bull; to make a bullet point, DO NOT USE other simbols

DO NOT PUT ANY COMMENT from YOU, WE DON'T NEED THAT!!!";
  }

  protected function generate_prompt($job_post)
  {
    $superio_options = get_option('superio_theme_options');
    $job_content = $job_post->post_content;

    // get employer data from job
    $employer_id = get_post_meta($job_post->ID, '_job_employer_posted_by', true);

    if (!$employer_id)
      throw new \Exception('Employer not found');

    // get employer post
    $employer_post = get_post($employer_id);


    $employer_name = $employer_post->post_title;
    $employer_content = $employer_post->post_content;
    // get employer type from term employer_type
    $employer_type = wp_get_post_terms($employer_post->ID, 'employer_type', ['fields' => 'names']);

    // convert new line to \n
    $job_content = str_replace("\n", "\\n", strip_tags($job_content));
    $employer_content = str_replace("\n", "\\n", strip_tags($employer_content));

    return str_replace([
      '{employer_name}',
      '{employer_type}',
      '{country}',
      '{job_content}',
      '{employer_content}',
      '{job_title}',
      '{translate_to}'
    ], [
      $employer_name,
      $employer_type[0],
      $superio_options['openai_location'],
      $job_content,
      $employer_content,
      $job_post->post_title,
      $superio_options['openai_language'] === 'en' ? '' : ' and translate it to ' . $superio_options['openai_language']
    ], $this->main_prompt());
  }

  /**
   * Decode emoticons
   * @param string $string
   * @return string
   */
  protected function decodeEmoticons($string)
  {
    if (!function_exists('mb_convert_encoding') || !function_exists('json_decode'))
      return $string;

    $replaced = preg_replace("/\\\\u([0-9A-F]{1,4})/i", "&#x$1;", $string);
    $string = mb_convert_encoding($replaced, "UTF-16", "HTML-ENTITIES");
    $string = mb_convert_encoding($string, 'utf-8', 'utf-16');
    return $string;
  }

  /**
   * Save improved job detail
   * @param WP_Post $job
   * @param array $improved
   * @return void
   */
  protected function update_job_detail(\WP_Post $job, array $improved)
  {
    $description_array = [];

    $improved = wp_parse_args($improved, [
      'summary' => '',
      'roles&responsibilities' => '',
      'requirements' => '',
      'shift_timings' => '',
      'why_join' => ''
    ]);

    if (!empty($improved['summary']))
      update_post_meta($job->ID, '_job_summary', $improved['summary']);

    $summary = $improved['summary'];

    unset($improved['summary']);

    foreach ($improved as $key => $value) :

      switch ($key):
        case 'roles&responsibilities':
          $title = __('Roles & Responsibilities', 'orangerdev-openai');
          break;

        case 'requirements':
          $title = __('Requirements', 'orangerdev-openai');
          break;

        case 'shift_timings':
          $title = __('Shift/Timings', 'orangerdev-openai');
          break;

        case 'why_join':
          $title = __('Why Join?', 'orangerdev-openai');
          break;
      endswitch;

      if (!empty($value))
        $description_array[] = $title . "\n\n" . $this->decodeEmoticons($value);
    endforeach;

    if (count($description_array) > 0) :
      $description = implode("\n\n", $description_array);

      $old_job_description = get_post_meta($job->ID, '_job_description', true);

      update_post_meta($job->ID, '_old_job_description', $old_job_description);

      wp_update_post([
        'ID' => $job->ID,
        'post_content' => $description
      ]);

      update_post_meta($job->ID, '_job_description', $description);
    endif;

    return [
      'summary' => $summary,
      'description' => $description
    ];
  }

  /**
   * Improve job description
   * Hooked via orangerdev/openai/improve-job-description
   * @since   1.0.0
   */
  public function improve_job_description()
  {
    $response = [
      'success' => false,
      'message' => '',
      'data' => []
    ];

    try {
      $postdata = wp_parse_args($_POST, [
        'postID' => 0,
        'nonce' => ''
      ]);

      if (!wp_verify_nonce($postdata['nonce'], 'orangerdev/openai/improve-job-description'))
        throw new \Exception('Invalid nonce');

      $post = get_post($postdata['postID']);

      if (!is_a($post, 'WP_Post') || $post->post_type !== 'job_listing')
        throw new \Exception('Invalid job post');

      $prompt = $this->generate_prompt($post);

      update_post_meta($post->ID, '_openai_job_improvement', true);

      $ai_response =  orangerdev_openai_request($this->system_role, $prompt);

      if (is_wp_error($ai_response))
        throw new \Exception($ai_response->get_error_message());

      if (!isset($ai_response['choices'][0]['message']['content']))
        throw new \Exception('Invalid response');

      $data = json_decode($ai_response['choices'][0]['message']['content'], true);

      do_action("inspect", [
        "job_improvement",
        [
          $data
        ]
      ]);

      $improved = $this->update_job_detail($post, $data);

      if (empty($improved['description']))
        throw new \Exception('Job description not improved');

      $improved['description'] = apply_filters('the_content', $improved['description']);

      $response['success'] = true;
      $response['message'] = __("Job description improved successfully", "orangerdev-openai");

      $response['data'] = $improved;
    } catch (\Exception $e) {
      $response['message'] = $e->getMessage();
    } finally {
      wp_send_json($response);
      exit;
    }
  }
}
