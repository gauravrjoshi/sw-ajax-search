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
  $("input#search").on("change, input", function () {
    var filter = $("#filter");
    console.log(filter.serialize());
    $.ajax({
      url: filter.attr("action"),
      data: filter.serialize() + "&security=" + sw_ajax_search_params.nonce, // form data
      type: filter.attr("method"), // POST
      beforeSend: function (xhr) {
        $("#loder_img").show(); // changing the button label
        $("#response").hide();
      },
      success: function (data) {
        console.log(data);
        $("#loder_img").hide(); // changing the button label back
        $("#response").show();
        if (data.length > 0) {
          let html = `<ul id="slider-id" class="slider-class">`;
          for (let index = 0; index < data.length; index++) {
            const date = data[index].date;
            const status = data[index].status;
            const title = data[index].title;
            const url = data[index].url;
            console.table([date, status, title, url]);
            html += `<li><a href="${url}" rel="bookmark" target="_blank">${title} <span style="color: #ef7f1a;">(${date})</span></a></li>`;
          }
          html += "</ul>";
          $("#response").html(html); // insert data
        } else {
          $("#response").html(`<h2 style='color:#ffffff;'>Nothing Found</h2>`); // insert data
        }

        jQuery(".send_post_request").text(jQuery("input#search").val());
      },
    });
    return false;
  });

  jQuery("#clearSearch").on("click", function () {
    jQuery("#search").val(""); // Clear the search input
    jQuery("div#response").empty();
    jQuery("#search").focus(); // Optionally, bring focus back to the search input
  });
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
