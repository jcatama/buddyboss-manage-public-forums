(function($) {
  $(document).ready(function() {
    const parentDir = '#forum-subscriptions-parent'
    $(`${parentDir} .fs-toggle`).click(function(e) {
      let card = $(this)
      let pcard = card.parent().parent()
      const pid = card.data('pid');
      pcard.toggleClass('fs-open')
      scard = pcard.find('.sub-check[data-parent="'+pid+'"]')
      scard.toggle()
    })

    $(`${parentDir} .fs-toggle > input[type="checkbox"]`).click(function(e) {
      e.stopPropagation();
      let cardchk = $(this)
      let childchk = cardchk.parent().parent().parent().find('.sub-check input')
      if(cardchk.is(':checked')) {
        childchk.attr('checked', 'checked')
      } else {
        childchk.removeAttr('checked')
      }
    });

    $(`${parentDir} #save-forums`).click(function(e) {
      let savebtn = $(this)
      let idSelector = function() { return this.id; };
      const forumList = `${parentDir} .forum-subscriptions-list`
      let chkforums_arr = $(`${forumList} :checkbox:checked`).map(idSelector).get();
      let unchkforums_arr = $(`${forumList} :checkbox:not(:checked)`).map(idSelector).get();
      $.ajax({
        type : "POST",
        url : bbmpfapi.ajaxurl,
        dataType : "json",
        data: { 
          action: 'bbmpf_subscribe_to_forum', 
          subs_forum_ids: chkforums_arr.join()+'',
          unsubs_forum_ids: unchkforums_arr.join()+''
        },
        complete: function(response) {
          const responseData = response.responseJSON;
          if(responseData.status == true && response.status == 200) {
            savebtn.html('Changes saved!')
            setTimeout(() => {
              savebtn.html('Save')
            }, 2000);
          } else {
            alert(responseData.message)
            setTimeout(() => {
              location.reload()
            }, 2000);
          }
        }
      });
    });

    let toggle_all = false
    $(`${parentDir} #toggle-all-forums`).click(function(e) {
      toggle_all = !toggle_all
      $(`${parentDir} .forum-subscriptions`).find('input[type="checkbox"]').attr('checked', toggle_all)
    });

  })
})( jQuery );