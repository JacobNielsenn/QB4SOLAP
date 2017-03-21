/**
 * Created by Jacob on 03-11-2016.
 */

//Tied up to the button id QueryButton.
$(document).ready(function(){
    $('#QueryButton').click( function () {
        var ele = document.getElementById('GeneratedQuery');
        var actualquery = prefixes + "\n" + ele.value;
        $.post('http://localhost:8890/sparql', {query: actualquery, format:'text/html'}, function(data) {
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
        $.post('http://localhost:8890/sparql', {query: actualquery, format:'text/html'}, function(data) {
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