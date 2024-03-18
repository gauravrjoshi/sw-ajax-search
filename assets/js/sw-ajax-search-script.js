jQuery(function ($) {
  // Disable form submission on Enter key press
  $("#filter").on("keydown", function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      return false;
    }
  });

  var loader = $("#loader_img"),
    searchInput = $("input#search"),
    response = $("#response"),
    clearSearchButton = $("button#clearSearch");

  loader.hide();

  searchInput.on("change, input", function () {
    var inputVal = $(this).val().trim(); // Get the current value of the input field.
    if (inputVal.length >= 1) {
      // Check if the length of the input is exactly 3.
      var filter = $("#filter");
      $.ajax({
        url: sw_ajax_search_params.ajax_url,
        data: filter.serialize() + "&security=" + sw_ajax_search_params.nonce, // form data
        type: filter.attr("method"), // POST
        beforeSend: function (xhr) {
          loader.show(); // changing the button label
          response.hide();
          clearSearchButton.hide();
        },
        success: function (data) {
          loader.hide(); // changing the button label back
          response.show();
          clearSearchButton.show();
          if (data.length > 0) {
            let html = `<ul id="slider-id" class="slider-class">`;
            for (let index = 0; index < data.length; index++) {
              const date = data[index].date;
              const title = data[index].title;
              const url = data[index].url;
              html += `<li><a href="${url}" rel="bookmark" target="_blank">${title} <span style="color: #ef7f1a;">(${date})</span></a></li>`;
            }
            html += "</ul>";
            response.html(html); // insert data
          } else {
            response.html(`<p>No results were found.</p>`); // insert data
          }

          jQuery(".send_post_request").text(jQuery("input#search").val());
        },
      });
      return false;
    } else {
      response.empty();
    }
  });

  jQuery("#clearSearch").on("click", function (e) {
    e.preventDefault();
    jQuery("#search").val(""); // Clear the search input
    jQuery("div#response").empty();
    jQuery("#search").focus(); // Optionally, bring focus back to the search input

  });
});
