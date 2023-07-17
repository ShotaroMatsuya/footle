var timer;

$(document).ready(function () {
  $('.result').on('click', function () {
    var id = $(this).attr('data-linkId');
    var url = $(this).attr('href');

    if (!id) {
      alert('data-linkId attribute not found');
    }
    increaseLinkClicks(id, url);

    return false;
  });
  var grid = $('.imageResults');

  grid.on('layoutComplete', function () {
    //layoutCompleteはすべて計算し終えたときに実行されるイベントリスナー
    $('.gridItem img').css('visibility', 'visible');
    console.log('done!');
  });
  grid.masonry({
    itemSelector: '.gridItem',
    columnWidth: 200,
    gutter: 5, //border space
    // transitionDuration: "0.8s",
    isInitLayout: false, //load時にmasonryLayoutを無効化->jsで読み込ませる
  });

  $('[data-fancybox]').fancybox({
    buttons: ['zoom', 'slideShow', 'fullScreen', 'thumbs', 'close'],
    thumbs: {
      autoStart: false,
    },

    caption: function (instance, item) {
      var caption = $(this).data('caption') || '';
      var siteUrl = $(this).data('siteurl') || '';

      if (item.type === 'image') {
        caption =
          (caption.length ? caption + '<br />' : '') +
          '<a href="' +
          item.src +
          '">View image</a><br>' +
          '<a href="' +
          siteUrl +
          '">Visit page</a>';
      }

      return caption;
    },
    afterShow: function (instance, item) {
      //画像がクリックされたあとに行う処理をここに書く
      // var left = $(window).width();
      //  left= left / 2 - 240 ;
      $('.fancybox-slide').css('position', 'relative');
      $('.fancybox-content')
        .height('100%')
        .width('100%')
        .css('display', 'flex')
        .css('justify-content', 'center')
        .css('transform', 'translate(0px,0px)');
      $('.fancybox-image').width('auto').height('100%');

      increaseImageClicks(item.src);
    },
  });

  var searchArr = location.search ? location.search.substr(1).split('&') : [];
  console.log(searchArr);

  $("#random-toggle").on("click", function () {
    if (!$(this).hasClass("active")) {
      $(this).addClass("active");
      var urlParams = setParam(searchArr, "order", "random");
      window.location.href = "/search.php?" + urlParams;
    } else {
      $(this).removeClass("active");
      var urlParams = setParam(searchArr, "order", "latest");
      window.location.href = "/search.php?" + urlParams;
    }
  });
  $("#clicks-toggle").on("click", function () {
    if (!$(this).hasClass("active")) {
      $(this).addClass("active");
      var urlParams = setParam(searchArr, "order", "clicks");
      window.location.href = "/search.php?" + urlParams;
    } else {
      $(this).removeClass("active");
      var urlParams = setParam(searchArr, "order", "latest");
      window.location.href = "/search.php?" + urlParams;
    }
  });
  var num = '';
  $('#per-page').on('click', function () {
    num = $('[name=num]').val();
    var urlParams = setParam(searchArr, 'num', num);
    window.location.href = '/search.php?' + urlParams;
  });
});

function setParam(search, key, value) {
  var params = search.reduce(function (acc, q) {
    var key = q.split('=')[0];
    var value = q.split('=')[1] || '';
    acc[key] = decodeURIComponent(value);
    return acc;
  }, {});
  for (param in params) {
    if (param === key) {
      // 上書き
      params[param] = value;
    } else {
      // 追加
      params[key] = value;
    }
  }
  var query = Object.keys(params)
    .map(function (key) {
      var value = encodeURIComponent(params[key]);
      return key + '=' + value;
    })
    .join('&');
  return query;
}

function loadImage(src, className) {
  var image = $('<img>');
  image.on('load', function () {
    //srcが存在した場合
    $('.' + className + ' a').append(image);
    clearTimeout(timer);
    timer = setTimeout(function () {
      //whileループでmasonryメソッドが30回も呼び出されるのでsetTimeoutでDOMの表示が終わってから一回だけ呼び出されるようにする
      $('.imageResults').masonry();
    }, 500);
  });
  image.on('error', function () {
    //srcが存在せず画像が表示されなかった場合
    $('.' + className).remove();
    $.post('ajax/setBroken.php', { src: src }); //DBでの処理
  });
  image.attr('src', src);
}

function increaseLinkClicks(linkId, url) {
  $.post('ajax/updateLinkCount.php', { linkId: linkId }).done(function (
    result
  ) {
    if (result != '') {
      alert(result);
      return;
    }

    window.location.href = url;
  });
}
function increaseImageClicks(imageUrl) {
  $.post('ajax/updateImageCount.php', { imageUrl: imageUrl }).done(function (
    result
  ) {
    if (result != '') {
      alert(result);
      return;
    }
  });
}
