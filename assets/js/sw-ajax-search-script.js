jQuery(function ($) {
  var loader = $("#loader_img"),
    searchInput = $("input#search"),
    response = $("#response"),
    clearSearchButton = $("button#clearSearch");

  loader.hide();

  searchInput.on("change, input", function () {
    // height: 250px;
    var inputVal = $(this).val().trim(); // Get the current value of the input field.
   
    if (inputVal.length >= 1) {
      // Check if the length of the input is exactly 3.
      var filter = $("#filter");
      $.ajax({
        url: sw_ajax_search_params.ajax_url,
        data: filter.serialize() + "&security=" + sw_ajax_search_params.nonce + "&action=" + sw_ajax_search_params.action, // form data
        type: filter.attr("method"), // POST
        beforeSend: function (xhr) {
          loader.show(); // changing the button label
          response.hide();
          clearSearchButton.hide();
          jQuery("#response_wrap").css({'height':'unset'});
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
              const paragraph = data[index].paragraph;
              let para = '';
              if (paragraph != null) {
                para = `<br><span style="font-size: 10px;">${paragraph}<span>`;
              }
              html += `<li><a href="${url}" rel="bookmark" target="_self">${title} ${para}</a></li>`;
            }
            html += "</ul>";
            response.html(html); // insert data
            jQuery("#response_wrap").css({'height':'250px'});
            // 			  START

            const listItems = $('#slider-id li');
            let selectedIndex = -1;

            $(document).keydown(function (event) {
              const key = event.key;
              if (key === 'ArrowUp' || key === 'ArrowDown') {
                event.preventDefault();
                if (key === 'ArrowUp') {
                  selectedIndex = (selectedIndex - 1 + listItems.length) % listItems.length;
                } else {
                  selectedIndex = (selectedIndex + 1) % listItems.length;
                }
                highlightItem(selectedIndex);
              } else if (key === 'Enter' && selectedIndex !== -1) {
                clickAnchor(selectedIndex);
              }
              else if (key === 'Escape') {
                $("input#search").val('');
              }
            });
            // 			  END
          } else {
            jQuery("#response_wrap").css({'height':'unset'});
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
    jQuery("#response_wrap").css({'height':'unset'});
    // jQuery("#search").val(""); // Clear the search input
    jQuery("div#response").empty();
    jQuery("#search").focus(); // Optionally, bring focus back to the search input
  });

  var $slider = $("#slider");
  var $toggleButton = $("#toggle-button");
  var $icon = $toggleButton.find("i"); // Find the <i> inside $toggleButton

  $toggleButton.click(function () {
    jQuery("#response_wrap").css({'height':'unset'});
    console.log($slider.attr("class"));
    if ($slider.hasClass("show")) {
      // Remove any existing classes and then add fas and fa-search
      $icon.attr("class", "fas fa-search"); // Set class to fas fa-search
    } else {
      // Remove any existing classes and then add fa-window-close
      $icon.attr("class", "fa fa-window-close"); // Set class to fa fa-window-close
    }
    $slider.toggleClass("show");
  });

  // Listen for keydown event
  $(document).on('keydown', function (event) {
    // Check if Ctrl is pressed and the key is 'K'
    if (event.ctrlKey && event.which === 75) {
      // Prevent the default action to avoid any browser shortcut conflicts
      event.preventDefault();

      // Trigger the button click
      $toggleButton.click();
      // Focus the search input field
      $('#search').focus();
    }
  });

  function highlightItem(index) {
    $('#slider-id li').removeClass('selected').eq(index).addClass('selected');
    $("input#search").val($('#slider-id li').eq(index).text());
  }

  function clickAnchor(index) {
    const anchor = $('#slider-id li').eq(index).find('a');
    if (anchor.length) {
      anchor[0].click();
    }
  }



  //Make the DIV element draggagle:
  dragElement(document.getElementById("mydiv"));

  function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
      /* if present, the header is where you move the DIV from:*/
      document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
      /* otherwise, move the DIV from anywhere inside the DIV:*/
      elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
      e = e || window.event;
      e.preventDefault();
      // get the mouse cursor position at startup:
      pos3 = e.clientX;
      pos4 = e.clientY;
      document.onmouseup = closeDragElement;
      // call a function whenever the cursor moves:
      document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
      e = e || window.event;
      e.preventDefault();
      // calculate the new cursor position:
      pos1 = pos3 - e.clientX;
      pos2 = pos4 - e.clientY;
      pos3 = e.clientX;
      pos4 = e.clientY;
      // set the element's new position:
      elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
      elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
      /* stop moving when mouse button is released:*/
      document.onmouseup = null;
      document.onmousemove = null;
    }
  }
});
