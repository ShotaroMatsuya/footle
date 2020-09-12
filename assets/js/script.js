var timer;

$(document).ready(function () {
  $(".result").on("click", function () {
    var id = $(this).attr("data-linkId");
    var url = $(this).attr("href");

    if (!id) {
      alert("data-linkId attribute not found");
    }
    increaseLinkClicks(id, url);

    return false;
  });
  var grid = $(".imageResults");

  grid.on("layoutComplete", function () {
    //layoutCompleteはすべて計算し終えたときに実行されるイベントリスナー
    $(".gridItem img").css("visibility", "visible");
    console.log("done!");
  });
  grid.masonry({
    itemSelector: ".gridItem",
    columnWidth: 200,
    gutter: 5, //border space
    // transitionDuration: "0.8s",
    isInitLayout: false, //load時にmasonryLayoutを無効化->jsで読み込ませる
  });

  $("[data-fancybox]").fancybox({
    caption: function (instance, item) {
      var caption = $(this).data("caption") || "";
      var siteUrl = $(this).data("siteurl") || "";

      if (item.type === "image") {
        caption =
          (caption.length ? caption + "<br />" : "") +
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
      increaseImageClicks(item.src);
    },
  });
});
function loadImage(src, className) {
  var image = $("<img>");
  image.on("load", function () {
    //srcが存在した場合
    $("." + className + " a").append(image);
    clearTimeout(timer);
    timer = setTimeout(function () {
      //whileループでmasonryメソッドが30回も呼び出されるのでsetTimeoutでDOMの表示が終わってから一回だけ呼び出されるようにする
      $(".imageResults").masonry();
    }, 500);
  });
  image.on("error", function () {
    //srcが存在せず画像が表示されなかった場合
    $("." + className).remove();
    $.post("ajax/setBroken.php", { src: src }); //DBでの処理
  });
  image.attr("src", src);
}

function increaseLinkClicks(linkId, url) {
  $.post("ajax/updateLinkCount.php", { linkId: linkId }).done(function (
    result
  ) {
    if (result != "") {
      alert(result);
      return;
    }

    window.location.href = url;
  });
}
function increaseImageClicks(imageUrl) {
  $.post("ajax/updateImageCount.php", { imageUrl: imageUrl }).done(function (
    result
  ) {
    if (result != "") {
      alert(result);
      return;
    }
  });
}
