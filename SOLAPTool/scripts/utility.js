//Used to place an html element in front of another element.
function insertAfter(newNode, referenceNode) {
	referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}
//Close Menu after element has been clicked
$('.menu a').click(function (e) {
	$('.menu').collapse('toggle');
});
//Used to toggle prefix text in the query area.
function prefixText(ele){
	if (ele.classList.contains('show')){
		$('#prefix').hide();
		ele.className = 'hide';
	}
	else {
		$('#prefix').show();
		ele.className = 'show';
	}
}
//Used to make sure all id's and names on html elements as well
//as making sure that no variable name in the query is the same.
function UpdateID(name){
	for (var i in ID){
		if (i == name){
			ID[i] += 1;
			return ID[i];
		}
	}
	ID[name] = 1;
	return ID[name];
}
function Mes(ele){
    if (ele.classList.contains('hide')){
        ele.innerHTML = "Disaggregate"
        $("#mes").addClass("show");
        $("#mes").removeClass("hide");
        additionalQuery = true;
        Q.aggregate = true;
        Q.printQuery();
        runQuery();
    }
    else {
        ele.innerHTML = "Aggregate"
        $("#mes").addClass("hide");
        $("#mes").removeClass("show");
        additionalQuery = false;
        Q.aggregate = false;
        Q.printQuery();
        runQuery();
    }
}
function Cls(){
    $("#ResultFromQuery").empty();
}
//Tied up to the button id QueryButton.
$(document).ready(function(){
    $('#QueryButton').click( function () {
        var ele = document.getElementById('GeneratedQuery');
        var actualquery = prefixes + "\n" + ele.value;
        $.post('http://lod.cs.aau.dk:8890/sparql', {query: actualquery, format:'text/html'}, function(data) {
            var d = new Date,
                dformat = [d.getMonth()+1,
                        d.getDate(),
                        d.getFullYear()].join('/')+' '+
                    [d.getHours(),
                        d.getMinutes(),
                        d.getSeconds()].join(':');
            var CompleteQueryString = '<p class="notify" style="float: left">Table creation time: ' + dformat + '</p><br><br><br>' + data;
            $('#ResultFromQuery').html(CompleteQueryString);
        });
    });
});
function runQuery(){
    var ele = document.getElementById('GeneratedQuery');
    var actualquery = prefixes + "\n" + ele.value;
    $.post('http://lod.cs.aau.dk:8890/sparql', {query: actualquery, format:'text/html'}, function(data) {
        var d = new Date,
            dformat = [d.getMonth()+1,
                    d.getDate(),
                    d.getFullYear()].join('/')+' '+
                [d.getHours(),
                    d.getMinutes(),
                    d.getSeconds()].join(':');
        var CompleteQueryString = '<p class="notify" style="float: left">Table creation time:' + dformat + "</p><br><br><br>" + data;
        $('#ResultFromQuery').html(CompleteQueryString);
    }).fail(function(response){
        $('#ResultFromQuery').html(response.responseText);
    });
}
/* When the user clicks on the button,
 toggle between hiding and showing the dropdown content */
// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}