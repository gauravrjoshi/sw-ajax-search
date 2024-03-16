jQuery(document).ready(function () {
  jQuery("i.fa.fa-search").click(function (e) {
    e.preventDefault();
    jQuery([document.documentElement, document.body]).animate(
      {
        scrollTop: jQuery(".sw_ajax_search_form_wrap").offset().top - 100,
      },
      500
    );
  });

  jQuery(".sw_ajax_search_form_wrap *").focus(function () {
    jQuery(".sw_ajax_search_form_wrap form#filter").attr(
      "style",
      "margin-bottom: 0px;"
    );
    jQuery(".sw_ajax_search_form_wrap div#response_wrap").show();
  });

  jQuery(".sw_ajax_search_form_wrap form").submit(function (e) {
    e.preventDefault();
  });

  /*Menu Text Hide*/
  jQuery("#main-navigation-toggle").text("");

  jQuery("h2.comm_skill_heading").click(function () {
    jQuery(".comm_skill_video").hide();
    jQuery(this).next().toggle();
  });


  // Accordian
  var action = "click";
  var speed = "500";

  // Question handler
  jQuery("li.q").on(action, function () {
    // Get next element
    jQuery(this)
      .next()
      .slideToggle(speed)
      // Select all other answers
      .siblings("li.a")
      .slideUp();
  });
});

jQuery(function ($) {
  $("#loder_img").hide();
  $('select[name="categoryfilter"], input[name="date"], input#search').on(
    "change, input",
    function () {
      var filter = $("#filter");
      $.ajax({
        url: filter.attr("action"),
        data: filter.serialize(), // form data
        type: filter.attr("method"), // POST
        beforeSend: function (xhr) {
          $("#loder_img").show(); // changing the button label
          $("#response").hide();
        },
        success: function (data) {
          $("#loder_img").hide(); // changing the button label back
          $("#response").show();
          $("#response").html(data); // insert data
          jQuery(".send_post_request").text(jQuery("input#search").val());
          jQuery(".send_post_request_button").click(function () {
            $.ajax({
              url: filter.attr("action"),
              data: {
                action: "statelyworld_post_request",
                post_title: jQuery("input#search").val(),
              }, // form data
              type: filter.attr("method"), // POST
              beforeSend: function (xhr) {
                jQuery(".send_post_request_button").text("Sending...");
              },
              success: function (data) {
                jQuery(".send_post_request_button").text(data);
                jQuery(".send_post_request_button").attr(
                  "style",
                  "margin: 10px auto;display: block;background: green;color: #ffffff;padding: 10px;border-radius: 10px;"
                );
                jQuery("button.send_post_request_button").attr(
                  "disabled",
                  true
                );
              },
            });
          });
        },
      });
      return false;
    }
  );

  if (jQuery('select[name="categoryfilter"]').val() == "") {
    var filter = $("#filter");
    $.ajax({
      url: filter.attr("action"),
      data: filter.serialize(), // form data
      type: filter.attr("method"), // POST
      beforeSend: function (xhr) {
        $("#loder_img").show(); // changing the button label
      },
      success: function (data) {
        $("#loder_img").hide(); // changing the button label back
        $("#response").html(data); // insert data
      },
    });
    return false;
  }
});

function copyToClipboard(text, id) {
  // Get the text field
  var dummy = document.createElement("input");
  document.body.appendChild(dummy);
  dummy.value = text;
  // Select the text field
  dummy.select();
  document.execCommand("copy");
  document.body.removeChild(dummy);
  // Alert the copied text
  jQuery(`[data_copy_id='${id}'`)
    .text(" Copied to clipboard")
    .attr("style", "color:green")
    .show()
    .delay(1000)
    .fadeOut();
  setTimeout(() => {
    jQuery(`[data_copy_id='${id}'`).text("").show();
  }, 2000);
}


// 
document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('search').value = ''; // Clear the search input
    document.getElementById('search').focus(); // Optionally, bring focus back to the search input
});



