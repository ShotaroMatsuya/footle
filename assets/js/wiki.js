// wiki

var input = document.getElementById('search'),
  resultsContainer = document.querySelector('.siteResults'),
  aboutResults = document.querySelector('.resultsCount'),
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
      result.className = 'resultContainer';
      var h3 = document.createElement('h3');
      h3.classList.add('title');
      
      // result.innerHTML = globalData.query.search[i].snippet;
      var aTag = document.createElement('a');
      aTag.href =
      'https://ja.wikipedia.org/wiki/' +
        globalData.query.search[i].title.replace(/\s/g, '_');
        aTag.innerHTML = globalData.query.search[i].title;
        h3.appendChild(aTag);
        var urlSpan = document.createElement('span');
        urlSpan.classList.add("url");
        urlSpan.innerHTML = 'https://ja.wikipedia.org/wiki/' +
        globalData.query.search[i].title.replace(/\s/g, '_');
        
        console.log(globalData.query.search[i].snippet.replace(/<.*>/g, ''));
        var desSpan = document.createElement('span');
        desSpan.classList.add('description');
        desSpan.innerHTML = globalData.query.search[i].snippet.replace(/<.*>/g, '');
      result.appendChild(h3);
      result.appendChild(urlSpan);
      result.appendChild(desSpan)
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
      result.className = 'resultContainer';
      var h3 = document.createElement('h3');
      h3.classList.add('title');
      
      // result.innerHTML = globalData.query.search[i].snippet;
      var aTag = document.createElement('a');
      aTag.href =
      'https://ja.wikipedia.org/wiki/' +
        globalData.query.search[i].title.replace(/\s/g, '_');
        aTag.innerHTML = globalData.query.search[i].title;
        h3.appendChild(aTag);
        var urlSpan = document.createElement('span');
        urlSpan.classList.add("url");
        urlSpan.innerHTML = 'https://ja.wikipedia.org/wiki/' +
        globalData.query.search[i].title.replace(/\s/g, '_');
        
        console.log(globalData.query.search[i].snippet.replace(/<.*>/g, ''));
        var desSpan = document.createElement('span');
        desSpan.classList.add('description');
        desSpan.innerHTML = globalData.query.search[i].snippet.replace(/<.*>/g, '');
      result.appendChild(h3);
      result.appendChild(urlSpan);
      result.appendChild(desSpan)
      resultsContainer.appendChild(result);
      }
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
  var urlQuery = new URL(location);
  urlQuery.searchParams.set("term", input.value);
                    console.log(urlQuery);
  

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
      result.className = 'resultContainer';
      var h3 = document.createElement('h3');
      h3.classList.add('title');
      
      // result.innerHTML = globalData.query.search[i].snippet;
      var aTag = document.createElement('a');
      aTag.href =
      'https://ja.wikipedia.org/wiki/' +
        globalData.query.search[i].title.replace(/\s/g, '_');
        aTag.innerHTML = globalData.query.search[i].title;
        h3.appendChild(aTag);
        var urlSpan = document.createElement('span');
        urlSpan.classList.add("url");
        urlSpan.innerHTML = 'https://ja.wikipedia.org/wiki/' +
        globalData.query.search[i].title.replace(/\s/g, '_');
        
  
        var desSpan = document.createElement('span');
        desSpan.classList.add('description');
        desSpan.innerHTML = globalData.query.search[i].snippet.replace(/<.*>/g, '');
      result.appendChild(h3);
      result.appendChild(urlSpan);
      result.appendChild(desSpan)
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
