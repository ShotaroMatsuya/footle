
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