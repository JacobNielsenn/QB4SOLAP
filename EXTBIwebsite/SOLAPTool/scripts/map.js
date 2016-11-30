/**
 * Created by Jacob on 24-11-2016.
 */
var popup;
var maps = [];
function onMapClick(e) {
    popup = L.popup();
    popup
        .setLatLng(e.latlng)
        .setContent("You clicked the map at: " + e.latlng.toString().replace('LatLng(', '').replace(')',''))
        .openOn(findMap(e.target._container.id));
    UpdateObject(e);
}
function initializeMap(mapID){
    var mymap = L.map(mapID).setView([57.04745, 9.91928], 13);
    maps.push([mymap, mapID]);
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
        '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="http://mapbox.com">Mapbox</a>',
        id: 'mapbox.streets'
    }).addTo(mymap);
    mymap.on('click', onMapClick);
}
function findMap(mapID){
    for (var map in maps){
        if (maps[map][0]._container.id == mapID){
            return maps[map][0];
        }
    }
}
function UpdateObject(e){
    var element = document.getElementById(e.target._container.id);
    var operator = searchOperator(element);
    var obj = findOperatorInList(operator.id);
    addProperty(element, 'userInput', 'Point');
    if (element.previousElementSibling.getAttribute('name').indexOf('1') != -1){
        addProperty(element, 'first', (e.latlng.lat + ", " + e.latlng.lng));
    }
    else if (element.previousElementSibling.getAttribute('name').indexOf('2') != -1){
        addProperty(element, 'second', (e.latlng.lat + ", " + e.latlng.lng));
    }
    PComplete();
}