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

function RemoveRegionFromDataStructureDefinition(data){
	data.dimension[7].dimensionProperty.hasHierarchy[0].hierarchy.hasLevel.splice(3,1);
	data.dimension[8].dimensionProperty.hasHierarchy[0].hierarchy.hasLevel.splice(3,1);
	data.dimension[0].dimensionProperty.hasHierarchy[1].hierarchy.hasLevel.splice(3,1);
	data.levelProperty.splice(3,1);
}