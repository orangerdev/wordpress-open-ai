<?php

$success_message = __("We have improved your Job Description and created a Job Summary based on best practices to ensure you receive the maximum number of applicants. Click on <strong>Submit Job</strong> to use this improved Job Description or click on edit to make further changes.", "orangerdev-openai");
?>

<div style="display: none">
  <div id="edit-job-description">
    <h3><?php _e("Edit Job Description", "orangerdev-openai"); ?></h3>
    <div class="close-button-holder">
      <button type="button" class="mfp-close">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M18.5303 5.46967C18.8232 5.76256 18.8232 6.23744 18.5303 6.53033L6.53033 18.5303C6.23744 18.8232 5.76256 18.8232 5.46967 18.5303C5.17678 18.2374 5.17678 17.7626 5.46967 17.4697L17.4697 5.46967C17.7626 5.17678 18.2374 5.17678 18.5303 5.46967Z" fill="#1D1919" />
          <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46967 5.46967C5.17678 5.76256 5.17678 6.23744 5.46967 6.53033L17.4697 18.5303C17.7626 18.8232 18.2374 18.8232 18.5303 18.5303C18.8232 18.2374 18.8232 17.7626 18.5303 17.4697L6.53033 5.46967C6.23744 5.17678 5.76256 5.17678 5.46967 5.46967Z" fill="#1D1919" />
        </svg>

      </button>
    </div>
    <textarea id="original-job-description" name="original-job-description" class="edit-description-on-fly original" style="display: none;" rows="10"><?php echo esc_textarea($job_post->post_content); ?></textarea>
    <textarea id="ai-job-description" name="ai-job-description" class="edit-description-on-fly ai" rows="10" cols="100">
      &nbsp;
    </textarea>
    <input type="hidden" id="content-type" value="ai">
    <div class="footer">
      <div class="restore-status">
        <span class="restore-message restoring"><?php _e("Switching...", "orangerdev-openai"); ?></span>
        <span class="restore-message saved"><?php _e("We have switched to your original job description", "orangerdev-openai"); ?></span>
      </div>
      <div class="button-holder">
        <button class="button btn" id="restore-description"><?php _e("Switch to original", "orangerdev-openai"); ?></button>
        <button class="button btn primary-button" id="save-description"><?php _e("Save", "orangerdev-openai"); ?></button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  const postID = "<?= $job_post->ID; ?>";
  const buttonText = {
    ai: "<?php _e("Switch to improved", "orangerdev-openai"); ?>",
    original: "<?php _e("Switch to original", "orangerdev-openai"); ?>"
  }

  const restoreText = {
    ai: "We have switched to the improved job description",
    original: "We have switched to your original job description"
  }

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
    text.textContent = '<?php _e("Hang tight! We are working our magic on your request.", "orangerdev-openai"); ?>';
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
              $("textarea#ai-job-description").val(response.data.esc_description);
            }
          }
        },
        complete: function() {
          $("#action-preview-holder").show();
          unblockElement('.job-submission-preview-form-wrapper');
        },
      })

      $(document).on("click", "#continue-edit-job", function(e) {
        e.preventDefault();

        // open #edit-job-description
        $.magnificPopup.open({
          items: {
            src: "#edit-job-description",
            type: "inline"
          }
        });
      })

      $(document).on("click", ".mfp-close", function() {
        $.magnificPopup.close();
      });

      $(document).on("click", "#restore-description", function() {
        const currentType = $("#content-type").val();

        $("#edit-job-description").find(".restore-message").removeClass("show");
        $("#edit-job-description").find(".restore-message.restoring").addClass("show");
        $("#edit-job-description").find("button").prop("disabled", true);

        setTimeout(function() {

          $("#edit-job-description").find(".restore-message.restoring").removeClass("show");
          $(".edit-description-on-fly").show();

          if (currentType === 'ai') {
            $("#edit-job-description .restore-message.saved").html(restoreText.original);
            $("#restore-description").html(buttonText.ai);
            $(".edit-description-on-fly.ai").hide();
            $("#content-type").val("original");
          } else {
            $("#edit-job-description .restore-message.saved").html(restoreText.ai);
            $("#restore-description").html(buttonText.original);
            $(".edit-description-on-fly.original").hide();
            $("#content-type").val("ai");
          }
          $("#edit-job-description").find("button").prop("disabled", false);
          $("#edit-job-description").find(".restore-message.saved").addClass("show");

        }, 1000)
      })

      $(document).on("click", "#save-description", function() {

        $.ajax({
          url: "<?php echo admin_url("admin-ajax.php?action=orangerdev/openai/save-job-description"); ?>",
          method: "POST",
          timeout: 0,
          data: {
            postID,
            improvedDesc: $("textarea#ai-job-description").val(),
            originalDesc: $("textarea#original-job-description").val(),
            contentType: $("#content-type").val(),
            nonce: "<?php echo wp_create_nonce('orangerdev/openai/save-job-description'); ?>"
          },
          beforeSend: function() {
            $.magnificPopup.close();
            blockElementWithSpinner('.job-submission-preview-form-wrapper');
          },
          success: function(response) {
            if (response.success) {
              if (response.data.description !== "") {
                $(".content-job-detail .list-content-job .the-content").html(response.data.description);
              }
            }
          },
          complete: function() {
            $("#action-preview-holder").show();
            unblockElement('.job-submission-preview-form-wrapper');
          },
        })
      });
    })


  })(jQuery)
</script>