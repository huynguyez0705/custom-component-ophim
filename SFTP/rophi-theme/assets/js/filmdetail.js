//--Film Info
jQuery(document).ready(function () {
  // rating
  var filmId = jQuery('#film_id').val()

  function scorehint(score) {
    var text = ''
    switch (parseInt(score)) {
      case 1:
        text = '1/10'
        break
      case 2:
        text = '1/10'
        break
      case 3:
        text = '3/10'
        break
      case 4:
        text = '4/10'
        break
      case 5:
        text = '5/10'
        break
      case 6:
        text = '6/10'
        break
      case 7:
        text = '7/10'
        break
      case 8:
        text = '8/10'
        break
      case 9:
        text = '9/10'
        break
      default:
        text = '10/10'
    }
    return text
  }
  jQuery('#star').raty({
    half: false,
    noRatedMsg: 'You have already rated this movies',
    score: function () {
      return jQuery(this).attr('data-score')
    },

    mouseover: function (score, evt) {
      jQuery('#hint').html(scorehint(score))
    },
    mouseout: function (score, evt) {
      jQuery('#hint').html('')
    },
    click: function (score, evt) {
      jQuery
        .ajax({
          url: URL_POST_RATING,
          type: 'POST',
          data: {
            action: 'ratemovie',
            rating: score,
            postid: postid
          }
        })
        .done(function (data) {
          if (data.status) {
            if (typeof data.rating_star != 'undefined') {
              jQuery('.box-rating .average').html(data.rating_star)
              jQuery('.box-rating #rate_count').html(data.rating_count)
              jQuery('.box-rating #average').html(data.rating_star)
              jQuery('.box-rating #div_average').show()
              $('#star').raty('score', data.rating_star)
              jQuery('#hint').html('')
              $('#star').raty('readOnly', true)
            }
          } else {
            $('#star').raty('readOnly', true)
          }
        })
    }
  })

  jQuery('.tab-version').on('click', function () {
    jQuery('.tab-version').removeClass('active');
    jQuery('.episode-block').removeClass('active');
    jQuery(this).addClass('active');
    jQuery('#' + jQuery(this).data('server')).addClass('active');

    // Show toast notification
    var serverName = jQuery(this).text().trim();
    jQuery('.toast-server').html('Bạn đã chọn Server <span class="server-name">' + serverName + '</span>').addClass('show');

    // Auto-hide toast after 3 seconds
    setTimeout(function () {
      jQuery('.toast-server').removeClass('show').fadeOut();
    }, 5000);
  });
  $('#btn_lightbulb').click(() => {
    $('#light-overlay').toggle()
    $('#btn_lightbulb').toggleClass('off')
  })
  $('#btn_autonext').click(() => {
    $('#btn_autonext').toggleClass('active');
    const msg = $('#btn_autonext').hasClass('active') ? 'Tự chuyển tập: Bật' : 'Tự chuyển tập: Tắt';
    $('<div class="toast">' + msg + '</div>').appendTo('body').fadeIn().delay(1000).fadeOut(() => $(this).remove());
  });

  if ($('#player').length) {
    player.on('complete', () => {
      if ($('#btn_autonext').hasClass('active') && "<?= nextEpisodeUrl() ?>") {
        window.location.href = "<?= nextEpisodeUrl() ?>";
      }
      player.on('waiting', () => {
        loadingContainer.style.display = 'block';
      });
      player.on('loaded', () => {
        loadingContainer.style.display = 'none';
      });
      player.on('playing', () => {
        loadingContainer.style.display = 'none';
      });
    });
  }
})
