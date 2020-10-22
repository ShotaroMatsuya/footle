const deleteBtn = document.getElementById("deleteBtn");
const crawlBtn1 = document.getElementById('crawlBtn1');
const crawlBtn2 = document.getElementById('crawlBtn2');
const myForm1 = document.getElementById("myForm1");
const myForm2 = document.getElementById("myForm2");


deleteBtn.addEventListener('click' ,()=>{
    if(confirm('消去してもよろしいですか？')){
        deleteRow();
        deleteBtn.disabled = true;
    }else{
        deleteBtn.disabled = true;
        crawlBtn1.disabled = false;
        crawlBtn2.disabled = false;

    }
});

function deleteRow() {
    deleteBtn.innerHTML ='<span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>Loading...';
    $.post("ajax/deleteRow.php", { mode: "delete" }).done(function (
      result
    ) {
      if (result != "") {
          if(result == "success!"){
              alert("データベースをリフレッシュしました！");
              deleteBtn.innerHTML = "Delete!";
              crawlBtn1.disabled = false;
              crawlBtn2.disabled = false;
              
              return;
          }else{
              alert('データベースの初期化に失敗しました');
              deleteBtn.innerHTML = "Try Again!";
          }
        return;
      }
    });
  }
   
  function resetCookie(){
      console.log('test');
     document.cookie = "PHPSESSID=;Path=/";
     location.href = "/doodle";
  }
  myForm1.addEventListener('submit',()=>{
    if(crawlBtn1.disabled === true){
        
        return ;
    }else{
        console.log("false");
        crawlBtn1.innerHTML ='<span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>Crawling...';
        crawlBtn1.disabled = true;
        crawlBtn2.disabled = true;
    }
  });
  myForm2.addEventListener('submit',()=>{
    if(crawlBtn2.disabled === true){
        
        return ;
    }else{
        console.log("false");
        crawlBtn2.innerHTML ='<span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>Crawling...';
        crawlBtn1.disabled = true;
        crawlBtn2.disabled = true;
    }
  });