//alert("loaded");
var xhr = new XMLHttpRequest();
function RunQuery(test) {  
  var url = "http://172.25.21.152:8080/RestfulService_war_exploded/" + test;
  xhr.open('GET', url, true);
  //xhr.open('GET', "http://ipinfo.io/json", true);
  xhr.send();
  xhr.onreadystatechange = processRequest;
}
//alert("response: " + xhr.response + ".")
function processRequest(e) {
  if (xhr.status == 200 && xhr.readyState == 4){
  var response = xhr.responseText;
  //alert("statusText: " + xhr.statusText + ".");
  document.getElementById("QueryData").value = response;
  }
}