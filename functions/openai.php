<?php

function orangerdev_openai_request($role, $prompt)
{

  $superio_options = get_option("superio_theme_options");

  $enable = boolval($superio_options["openai_api_enabled"]);

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
