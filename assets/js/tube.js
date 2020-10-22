
const APIkey = "";
var resultSection = $(".mainResultsSection");
function search() {
	//Clear any previous results
	$('.siteResults').html('');
	$('.pageButtons').html('');	
	$('.resultsCount').html('');
	
	//Get form input
	var q = $('#query').val();
	
	//Run GET Request 	on API
	$.get(
		"https://www.googleapis.com/youtube/v3/search",{
			part: 'snippet,id',
			q: q,
            type: 'video',
            maxResults:5,
			key: APIkey}, 
			function(data) {
				var nextPageToken = data.nextPageToken;
				var prevPageToken = data.prevPageToken;
				var pageInfo = data.pageInfo;
				var resultsCount = pageInfo.totalResults;
				
				//Log data
				console.log(data);	
				$('.mainResultsSection').prepend('<p class="resultsCount">'+ resultsCount +' results found</p>');
				
				$.each(data.items, function(i, item) {
						//Get output
						var output = getOutput(item);
						
						//Display results
						$('.siteResults').append(output);	
				});
				
				var buttons = getButtons(prevPageToken,nextPageToken, pageInfo);
				
				//Display buttons
				$('.pageButtons').prepend(buttons);
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
		var url = "https://www.youtube.com/watch?v=" + videoId;
		
		//Build Output String
		var output = '<div class="resultContainer row">' + 
		'<div class="col-sm-2">' +	
			'<a data-fancybox href="https://www.youtube.com/watch?v=' + videoId + '"><img class="img-fluid" src="' + thumb + '"></a>' +
		'</div>' +
		'<div class="col-sm-10 d-flex flex-column justify-content-around">' + 
			'<h3 class="title"><a class="result" data-fancybox href="https://www.youtube.com/watch?v=' + videoId + '">' + title + '</a></h3>' +'<span class="url">'+ url +'</span>'+
			'<small>By <span class="cTitle">' + channelTitle + '</span> on '+ videoDate + '</small>' +
			'<span class="description">' + description + '</span>' +
		'</div>' +
		'</div>';
		return output;
			
	}

//Build the Buttons
	function getButtons(prevPageToken,nextPageToken,pageInfo) {
		
		$('.pageButtons').html('');	
		var btnoutput;
		var q = $('#query').val();
		if(!prevPageToken) {
			btnoutput = '<div id="prev-button" class="pageNumberContainer" data-token="' + 	prevPageToken + '" data-query="' + q +'"' +
			'>&lt;Prev<img src="assets/images/pageStart.png"></div>' +'<div class="pageNumberContainer"><img src="assets/images/pageSelected.png"></div><div class="pageNumberContainer"><img src="assets/images/page.png"></div>' +
			'<button><div id="next-button" class="pageNumberContainer" data-token="' + 	nextPageToken + '" data-query="' + q +'"' +
			'onclick="nextPage();"><img src="assets/images/pageEnd.png" alt="次へ">Next&gt;</div></button>';
			console.log(nextPageToken);
		} else {
			console.log(nextPageToken);
			btnoutput = '<button><div id="prev-button" class="pageNumberContainer" data-token="' + 	prevPageToken + '" data-query="' + q +'"' +
			'onclick="prevPage();">&lt;Prev<img src="assets/images/pageStart.png" alt="前へ"></div></button>' +'<div class="pageNumberContainer"><img src="assets/images/pageSelected.png"></div><div class="pageNumberContainer"><img src="assets/images/page.png"></div>' +
			'<button><div id="next-button" class="pageNumberContainer" data-token="' + 	nextPageToken + '" data-query="' + q +'"' +
			'onclick="nextPage();"><img src="assets/images/pageEnd.png" alt="次へ">Next&gt;</div></button>';
		}
		return btnoutput;
	}

function nextPage() {
		
		var token = $('#next-button').data('token');
		var q = $('#next-button').data('query');
		//Clear any previous results 
		$('.siteResults').html('');
	$('.pageButtons').html('');	
	
	//Get form input
	q = $('#query').val();
	
	//Run GET Request 	on API
	$.get(
		"https://www.googleapis.com/youtube/v3/search",{
			part: 'snippet, id',
			q: q,
			pageToken: token,
            type: 'video',
            maxResults:5,
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
						$('.siteResults').append(output);	
				});
				
				var buttons = getButtons(prevPageToken,nextPageToken,pageInfo);
				
				//Display buttons
				$('.pageButtons').prepend(buttons);
			}
		);
			
}

function prevPage() {
		
		var token = $('#prev-button').data('token');
		var q = $('#prev-button').data('query');
		//Clear any previous results 
		$('.siteResults').html('');
	$('.pageButtons').html('');	
	
	//Get form input
	q = $('#query').val();
	
	//Run GET Request 	on API
	$.get(
		"https://www.googleapis.com/youtube/v3/search",{
			part: 'snippet,id',
			q: q,
			pageToken: token,
            type: 'video',
            maxResults:5,
			key: APIkey
		}, 
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
						$('.siteResults').append(output);	
				});
				
				var buttons = getButtons(prevPageToken,nextPageToken,pageInfo);
				
				//Display buttons
				$('.pageButtons').prepend(buttons);
			}
		);
			
}