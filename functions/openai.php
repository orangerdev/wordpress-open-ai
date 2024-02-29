<?php

/**
 * Create request to OpenAI API
 * @param string $role
 * @param string $prompt
 * @return array|WP_Error
 */
function orangerdev_openai_request($role, $prompt)
{

  $superio_options = get_option("superio_theme_options");

  $enable = boolval($superio_options["openai_api_enabled"]);

  if (defined('ORANGERDEV_OPENAI_AI_MODE') && ORANGERDEV_OPENAI_AI_MODE === 'dummy') :
    // return dummy content

    return [
      "choices" => [
        0 => [
          "message" => [
            "role" => "assistant",
            "content" => json_encode([
              "summary" => "This is a summary",
              "roles&responsibilites" => "These are the roles and responsibilities",
              "requirements" => "These are the requirements",
              "shift_timings" => "These are the shift timings",
              "why_join_us" => "This is why you should join us",
            ])
          ]
        ]
      ]
    ];
  endif;

  if ($enable !== true)
    return new WP_Error("openai_api_disabled", "OpenAI API is disabled");

  $apiKey = $superio_options['openai_api_key'];
  $model = $superio_options['openai_api_model'];
  $temperature = $superio_options['openai_api_temperature'];
  $maxTokens = $superio_options['openai_api_max_tokens'];
  $url = "https://api.openai.com/v1/chat/completions";

  $response = wp_remote_post($url, [
    'timeout' => 120,
    "headers" => [
      "Content-Type" => "application/json",
      "Authorization" => "Bearer $apiKey",
      "OpenAI-Beta" => "assistants=v1"
    ],
    "body" => json_encode([
      "model" => $model,
      "messages" => [
        $role,
        [
          "role" => "user",
          "content" => $prompt
        ]
      ]

    ])
  ]);

  // check if response return not 200
  if (is_wp_error($response))
    return $response;

  $response_body = wp_remote_retrieve_body($response);

  return json_decode($response_body, true);
}
