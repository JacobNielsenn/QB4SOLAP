/**
 * Created by Jacob on 03-11-2016.
 */
//Tied up to the button id QueryButton.
$(document).ready(function(){
    $('#QueryButton').click( function () {
        var ele = document.getElementById('GeneratedQuery');
        var actualquery = prefixes + "\n" + ele.value;
        console.log(actualquery);
        $.post('http://localhost:8890/sparql', {query: actualquery, format:'text/html'}, function(data) {
            console.log(data);
            $('#ResultFromQuery').html(data);
        });
    });
});

function runQuery(){
    var ele = document.getElementById('GeneratedQuery');
    var actualquery = prefixes + "\n" + ele.value;
    console.log(actualquery);
    $.post('http://localhost:8890/sparql', {query: actualquery, format:'text/html'}, function(data) {
        console.log(data);
        $('#ResultFromQuery').html(data);
    });
}