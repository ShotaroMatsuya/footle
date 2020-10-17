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


$(function() {
	$('#search-form').submit(function(e) {
		e.preventDefault();
	})
});
const APIkey = "";
function search() {
	//Clear any previous results 
	$('#results').html('');
	$('#btn-cnt').html('');	
	
	//Get form input
	var q = $('#query').val();
	
	//Run GET Request 	on API
	$.get(
		"https://www.googleapis.com/youtube/v3/search",{
			part: 'snippet, id',
			q: q,
            type: 'video',
            maxResults:20,
			key: APIkey}, 
			function(data) {
				var nextPageToken = data.nextPageToken;
				var prevPageToken = data.prevPageToken;
				var pageInfo = data.pageInfo;
				
				//Log data
				console.log(data);	
				
				$.each(data.items, function(i, item) {
						//Get output
						var output = getOutput(item);
						
						//Display results
						$('#results').append(output);	
				});
				
				var buttons = getButtons(prevPageToken,nextPageToken, pageInfo);
				
				//Display buttons
				$('#results').prepend(buttons);
				$('#btn-cnt').append(buttons);
			}
	);	
}

//Build Output
	function getOutput(item) {
		var videoId = item.id.videoId;
		var title = item.snippet.title;
		var description = item.snippet.description;
		var thumb = item.snippet.thumbnails.high.url;
		var channelTitle = item.snippet.channelTitle;
		var videoDate = item.snippet.publishedAt;
		
		//Build Output String
		var output = '<div class="row">' + 
		'<div class="col-sm-2">' +	
			'<a data-fancybox href="https://www.youtube.com/watch?v=' + videoId + '"><img class="img-fluid" src="' + thumb + '"></a>' +
		'</div>' +
		'<div class="col-sm-10">' + 
			'<h4><a data-fancybox href="https://www.youtube.com/watch?v=' + videoId + '">' + title + '</a></h4>' +
			'<small>By <span class="cTitle">' + channelTitle + '</span> on '+ videoDate + '</small>' +
			'<p>' + description + '</p>' +
		'</div>' +
		'</div>';
		return output;
			
	}

//Build the Buttons
	function getButtons(prevPageToken,nextPageToken,pageInfo) {
		
		$('#btn-cnt').html('');	
		var btnoutput;
		var q = $('#query').val();
		if(!prevPageToken) {
			btnoutput = '<div class="button-container">' + 
			'<span class="total-results"><label>Total Results :</label>' + pageInfo.totalResults + '</span>' +
			'<button id="next-button" class="btn animated-button thar-three" data-token="' + 	nextPageToken + '" data-query="' + q +'"' +
			'onclick="nextPage();">Next Page</button><div class="clearfix"></div></div>';
			console.log(nextPageToken);
		} else {
			console.log(nextPageToken);
			btnoutput = '<div class="button-container">' +
			'<span class="total-results"><label>Total Results :</label>' + pageInfo.totalResults + '</span>' +
			'<button id="next-button" class="btn  animated-button thar-three" data-token="' + 	nextPageToken + '" data-query="' + q +'"' +
			'onclick="nextPage();">Next Page</button>' +
			'<button id="prev-button" class="btn  animated-button thar-four" data-token="' + 	prevPageToken + '" data-query="' + q +'"' +
			'onclick="prevPage();">Previous Page</button>' +
			'<div class="clearfix"></div></div>';
		}
		return btnoutput;
	}

function nextPage() {
		
		var token = $('#next-button').data('token');
		var q = $('#next-button').data('query');
		//Clear any previous results 
	$('#results').html('');
	$('#btn-cnt').html('');	
	
	//Get form input
	q = $('#query').val();
	
	//Run GET Request 	on API
	$.get(
		"https://www.googleapis.com/youtube/v3/search",{
			part: 'snippet, id',
			q: q,
			pageToken: token,
            type: 'video',
            maxResults:20,
			key: APIkey}, 
			function(data) {
				var nextPageToken = data.nextPageToken;
				var prevPageToken = data.prevPageToken;
				var pageInfo = data.pageInfo;
				//Log data
				console.log(data);	
				
				$.each(data.items, function(i, item) {
						//Get output
						var output = getOutput(item);
						
						//Display results
						$('#results').append(output);	
				});
				
				var buttons = getButtons(prevPageToken,nextPageToken,pageInfo);
				
				//Display buttons
				$('#results').prepend(buttons);
				$('#btn-cnt').append(buttons);
			}
		);
			
}

function prevPage() {
		
		var token = $('#prev-button').data('token');
		var q = $('#prev-button').data('query');
		//Clear any previous results 
	$('#results').html('');
	$('#btn-cnt').html('');	
	
	//Get form input
	q = $('#query').val();
	
	//Run GET Request 	on API
	$.get(
		"https://www.googleapis.com/youtube/v3/search",{
			part: 'snippet, id',
			q: q,
			pageToken: token,
            type: 'video',
            maxResults:20,
			key: APIkey}, 
			function(data) {
				var nextPageToken = data.nextPageToken;
				var prevPageToken = data.prevPageToken;
				var pageInfo = data.pageInfo;
				//Log data
				console.log(data);	
				
				$.each(data.items, function(i, item) {
						//Get output
						var output = getOutput(item);
						
						//Display results
						$('#results').append(output);	
				});
				
				var buttons = getButtons(prevPageToken,nextPageToken,pageInfo);
				
				//Display buttons
				$('#results').prepend(buttons);
				$('#btn-cnt').append(buttons);
			}
		);
			
}

// wiki
const EnterKeyCode = 13;
var input = document.getElementById('search'),
  resultsContainer = document.getElementById('container-results'),
  aboutResults = document.getElementById('about-results'),
  globalData,
  nextBtn = document.getElementById('next'),
  previousBtn = document.getElementById('previous'),
  totalhits = 0,
  globalTotalhits,
  currentPage = 1,
  sroffset = 0,
  searchBtn = document.getElementById('searchBtn');

function resetValues() {
  currentPage = 1;
  sroffset = 0;
  aboutResults.innerHTML = '';
}

function callback(data) {
  globalData = data;
}
// /w/api.php?action=query&format=json&list=random&rnlimit=20
nextBtn.addEventListener('click', nextBtnClickHandler);

function nextBtnClickHandler(e) {
  if (totalhits - 20 > 0) {
    if (nextBtn.disabled) {
      nextBtn.disabled = false;
    }
    clearOldResults();
    sroffset += 20;
    currentPage++;
    JSONP(
      'https://ja.wikipedia.org/w/api.php?action=query&list=search&srsearch=' +
        encodeURIComponent(term) +
        '&utf8=&format=json&srprop=score|size|snippet&sroffset=' +
        sroffset +
        '&callback=callback&srlimit=20'
    );
    whenAvailable('globalData', function () {
      resultsContainer.style.display = 'inline-block';
      aboutResults.innerHTML =
        'found ' +
        totalhits +
        ' results ' +
        'current page:' +
        currentPage +
        ' of ' +
        Math.round(globalTotalhits / 20);
      if (totalhits > 20) {
        nextBtn.style.display = 'inline-block';
        previousBtn.style.display = 'inline-block';
      }
      for (var i = 0; i < 20; i++) {
        var result = document.createElement('div');
        result.className = 'result';
        // result.innerHTML = globalData.query.search[i].snippet;
        var aTag = document.createElement('a');
        aTag.href =
          'https://ja.wikipedia.org/wiki/' +
          globalData.query.search[i].title.replace(/\s/g, '_');
        aTag.innerHTML = globalData.query.search[i].title;
        result.appendChild(aTag);
        var textNode = document.createTextNode(
          ' ' + globalData.query.search[i].snippet.replace(/<.*>/g, '')
        );
        result.appendChild(textNode);
        resultsContainer.appendChild(result);
      }
    });
  } else {
    nextBtn.disabled = true;
  }
}
previousBtn.addEventListener('click', previousBtnClickHandler);

function previousBtnClickHandler(e) {
  if (currentPage > 1) {
    // console.log("curret page:"+currentPage+" sroffset:"+sroffset);
    sroffset -= 20;
    // console.log("curret page:"+currentPage+" sroffset:"+sroffset);
    currentPage--;
    clearOldResults();
    JSONP(
      'https://ja.wikipedia.org/w/api.php?action=query&list=search&srsearch=' +
        encodeURIComponent(term) +
        '&utf8=&format=json&srprop=score|size|snippet&sroffset=' +
        sroffset +
        '&callback=callback&srlimit=20'
    );
    whenAvailable('globalData', function () {
      resultsContainer.style.display = 'inline-block';
      aboutResults.innerHTML =
        'results ' +
        'current page:' +
        currentPage +
        ' of ' +
        Math.round(globalTotalhits / 20);
      if (totalhits > 20) {
        nextBtn.style.display = 'inline-block';
        previousBtn.style.display = 'inline-block';
      }
      for (var i = 0; i < 20; i++) {
        var result = document.createElement('div');
        result.className = 'result';
        // result.innerHTML = globalData.query.search[i].snippet;
        var aTag = document.createElement('a');
        aTag.href =
          'https://ja.wikipedia.org/wiki/' +
          globalData.query.search[i].title.replace(/\s/g, '_');
        aTag.innerHTML = globalData.query.search[i].title;
        result.appendChild(aTag);
        var textNode = document.createTextNode(
          ' ' + globalData.query.search[i].snippet.replace(/<.*>/g, '')
        );
        result.appendChild(textNode);
        resultsContainer.appendChild(result);
      }
    });
  }
}
input.addEventListener('input', inputInputHandler);

function inputInputHandler(e) {
  if (e.target.value === '') {
    clearOldResults();
    resetValues();
    aboutResults.innerHTML = '';
    next.style.display = 'none';
    previous.style.display = 'none';
  }
}
input.addEventListener('keyup', inputKeyupHandler);
function inputKeyupHandler(e) {
  resetValues();
  if (e.keyCode === EnterKeyCode) {
    clearOldResults();
    term = e.target.value;
    if (!term) {
      clearOldResults();
    }
    resultsContainer.style.display = 'block';
    console.log('first sroffset:' + sroffset);
    JSONP(
      'https://ja.wikipedia.org/w/api.php?action=query&list=search&srsearch=' +
        encodeURIComponent(term) +
        '&utf8=&format=json&srprop=score|size|snippet&sroffset=' +
        sroffset +
        '&callback=callback&srlimit=20'
    );
    whenAvailable('globalData', function (t) {
      // do something
      // console.log(globalData.query);
      globalTotalhits = globalData.query.searchinfo.totalhits;
      totalhits = globalTotalhits;
      resultsContainer.style.display = 'inline-block';
      aboutResults.innerHTML =
        'found ' +
        totalhits +
        ' results ' +
        'current page:' +
        currentPage +
        ' of ' +
        Math.round(totalhits / 20);
      if (totalhits > 20) {
        nextBtn.style.display = 'inline-block';
        previousBtn.style.display = 'inline-block';
      }
      for (var i = 0; i < 20; i++) {
        var result = document.createElement('div');
        result.className = 'result';
        // result.innerHTML = globalData.query.search[i].snippet;
        var aTag = document.createElement('a');
        aTag.href =
          'https://ja.wikipedia.org/wiki/' +
          globalData.query.search[i].title.replace(' ', '_');
        aTag.innerHTML = globalData.query.search[i].title;
        result.appendChild(aTag);
        var textNode = document.createTextNode(
          ' ' + globalData.query.search[i].snippet.replace(/<.*>/g, '')
        );
        result.appendChild(textNode);
        resultsContainer.appendChild(result);
      }
      globalData = 'undefined';
    });
  }
}
searchBtn.addEventListener('click', searchBtnClickHandler);
function searchBtnClickHandler() {
  resetValues();

  clearOldResults();
  term = input.value;
  if (!term) {
    clearOldResults();
  }
  resultsContainer.style.display = 'block';
  console.log('first sroffset:' + sroffset);
  JSONP(
    'https://ja.wikipedia.org/w/api.php?action=query&list=search&srsearch=' +
      encodeURIComponent(term) +
      '&utf8=&format=json&srprop=score|size|snippet&sroffset=' +
      sroffset +
      '&callback=callback&srlimit=20'
  );
  whenAvailable('globalData', function (t) {
    // do something
    // console.log(globalData.query);
    globalTotalhits = globalData.query.searchinfo.totalhits;
    totalhits = globalTotalhits;
    resultsContainer.style.display = 'inline-block';
    aboutResults.innerHTML =
      'found ' +
      totalhits +
      ' results ' +
      'current page:' +
      currentPage +
      ' of ' +
      Math.round(totalhits / 20);
    if (totalhits > 20) {
      nextBtn.style.display = 'inline-block';
      previousBtn.style.display = 'inline-block';
    }
    for (var i = 0; i < 20; i++) {
      var result = document.createElement('div');
      result.className = 'result';
      // result.innerHTML = globalData.query.search[i].snippet;
      var aTag = document.createElement('a');
      aTag.href =
        'https://ja.wikipedia.org/wiki/' +
        globalData.query.search[i].title.replace(' ', '_');
      aTag.innerHTML = globalData.query.search[i].title;
      result.appendChild(aTag);
      var textNode = document.createTextNode(
        ' ' + globalData.query.search[i].snippet.replace(/<.*>/g, '')
      );
      result.appendChild(textNode);
      resultsContainer.appendChild(result);
    }
    globalData = 'undefined';
  });
}

function JSONP(url) {
  var scriptTag = document.createElement('script');
  scriptTag.type = 'text/javascript';
  scriptTag.src = url;
  var appendedChild = document.head.appendChild(scriptTag);
  appendedChild.remove();
}

function clearOldResults() {
  aboutResults.innerHTML = '';
  while (resultsContainer.firstChild) {
    resultsContainer.removeChild(resultsContainer.firstChild);
  }
}

function whenAvailable(name, callback) {
  var interval = 10; // ms
  window.setTimeout(function () {
    if (typeof window[name] === 'object') {
      callback(window[name]);
    } else {
      window.setTimeout(arguments.callee, interval);
    }
  }, interval);
}
