<?php

$success_message = __("We have improved your Job Description and created a Job Summary based on best practices to ensure you receive the maximum number of applicants. Click on <strong>Submit Job</strong> to use this improved Job Description or click on edit to make further changes.", "orangerdev-openai");
?>

<div style="display: none;">
  <textarea id="original-job-description"><?php echo $job_post->post_content; ?></textarea>
  <textarea id="ai-job-description"></textarea>
</div>

<div style="display: none;">
  <div id="edit-job-description">
    <h3><?php _e("Edit Job Description", "orangerdev-openai"); ?></h3>
    <div class="close-button-holder">
      <button type="button" class="mfp-close">
        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M6.90595 0.0940528C7.03135 0.219456 7.03135 0.422776 6.90595 0.548179L0.548179 6.90595C0.422775 7.03135 0.219456 7.03135 0.0940525 6.90595C-0.0313509 6.78054 -0.0313508 6.57722 0.0940527 6.45182L6.45182 0.0940526C6.57722 -0.031351 6.78054 -0.0313508 6.90595 0.0940528Z" fill="#737171" />
          <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0940525 0.0940528C0.219456 -0.0313508 0.422775 -0.031351 0.548179 0.0940526L6.90595 6.45182C7.03135 6.57722 7.03135 6.78054 6.90595 6.90595C6.78054 7.03135 6.57722 7.03135 6.45182 6.90595L0.0940527 0.548179C-0.0313508 0.422776 -0.0313509 0.219456 0.0940525 0.0940528Z" fill="#737171" />
        </svg>
      </button>
    </div>
    <textarea id="edited-job-description"><?php $job_post->post_content; ?></textarea>
    <div class="footer">
      <div class="restore-status">
        <span class="restore-status restoring"><?php _e("Restored", "orangerdev-openai"); ?></span>
        <span class="restore-status saved"><?php _e("We have restored your 'original' job description", "orangerdev-openai"); ?></span>
      </div>
      <div class="button-holder">
        <button class="button btn" id="restore-description"><?php _e("Restore", "orangerdev-openai"); ?></button>
        <button class="button btn" id="save-description"><?php _e("Save", "orangerdev-openai"); ?></button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  const postID = "<?= $job_post->ID; ?>";

  function blockElementWithSpinner(selector) {
    // Create a div for the spinner
    var spinner = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    spinner.setAttribute('class', 'spinner');
    spinner.setAttribute('width', '32px');
    spinner.setAttribute('height', '32px');

    var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    circle.setAttribute('cx', '16px');
    circle.setAttribute('cy', '16px');
    circle.setAttribute('r', '14px');
    circle.setAttribute('stroke', 'currentColor');
    circle.setAttribute('stroke-width', '2px');
    circle.setAttribute('fill', 'transparent');
    circle.setAttribute('stroke-dasharray', '80, 200');
    circle.setAttribute('stroke-dashoffset', '0');
    circle.setAttribute('transform', 'rotate(0deg)');

    spinner.appendChild(circle);

    // Create a div for the text
    var text = document.createElement('div');
    text.className = 'text';
    text.textContent = '<?php _e("Hang tight! We are working our magic on your request.", "doctor-jobs-today"); ?>';
    // Position the text next to the spinner
    text.style.marginLeft = '10px';
    text.style.alignSelf = 'center';
    text.style.fontSize = '14px';

    spinner.style.animation = 'rotateSpinner 1s linear infinite';

    // Create a container for the spinner and the text
    var container = document.createElement('div');
    container.className = 'spinner-container';
    container.style.display = 'flex';
    container.style.alignItems = 'center';
    container.style.justifyContent = 'center';
    container.appendChild(spinner);
    container.appendChild(text);

    // Find the selected element and block it
    var element = document.querySelector(selector);
    var overlay = document.createElement('div');
    overlay.className = 'overlay';
    overlay.style.backgroundColor = '#fff';
    overlay.style.opacity = '1';
    overlay.style.cursor = 'wait';
    overlay.style.position = 'absolute';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'flex-start';
    overlay.style.justifyContent = 'center';
    overlay.style.paddingTop = "30vh";
    overlay.appendChild(container);

    element.appendChild(overlay);
  }

  function unblockElement(selector) {
    var element = document.querySelector(selector);
    var overlay = element.querySelector('.overlay');
    element.removeChild(overlay);
  }

  (function($) {
    $(document).ready(function() {

      $("#action-preview-holder").hide();
      $.ajax({
        url: "<?php echo admin_url("admin-ajax.php?action=orangerdev/openai/improve-job-description"); ?>",
        method: "POST",
        timeout: 0,
        data: {
          postID: postID,
          nonce: "<?php echo wp_create_nonce('orangerdev/openai/improve-job-description'); ?>"
        },
        beforeSend: function() {
          blockElementWithSpinner('.job-submission-preview-form-wrapper');
        },
        success: function(response) {
          if (response.success) {
            if (response.data.description !== "") {
              $("#action-preview-holder .text-info").html("<?php echo $success_message; ?>");
              $(".content-job-detail .list-content-job .the-content").html(response.data.description);
            }
          }
        },
        complete: function() {
          $("#action-preview-holder").show();
          unblockElement('.job-submission-preview-form-wrapper');
        },
      })
    })


  })(jQuery)
</script>