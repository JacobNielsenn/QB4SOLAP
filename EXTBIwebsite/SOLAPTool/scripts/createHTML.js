// HTML Operator //
// HEADER //
function Insertheader(name){
	var header = InsertDiv();
	var title = InsertDiv();
	var fadeover =InsertDiv();
	fadeover.setAttribute('class', 'grad');
	fadeover.setAttribute('style', 'height:5px; margin:0px 0px 0px 0px;');
	title.setAttribute('style', 'background: grey; color: White; text-align: center; margin:0px 0px 0px 0px;');
	title.appendChild(bMoveUp());
	title.appendChild(bMoveDown());
	title.appendChild(InsertTextBox(name));
	title.appendChild(bDelete());
	header.appendChild(title);
	header.appendChild(fadeover);
	return header;
}
function bMoveUp(){
	var b = document.createElement('button');
	b.setAttribute('name', 'up');
	b.setAttribute('onclick', 'MoveUp(this), buttonstyleupdate(this)');
	b.setAttribute('style', 'border:none; font-size: 125%;  background: none; color: white; float:left');
	b.innerHTML = '&uarr;';
	return b;
}
function bMoveDown(){
	var b = document.createElement('button');
	b.setAttribute('name', 'down');
	b.setAttribute('onclick', 'MoveDown(this), buttonstyleupdate(this)');
	b.setAttribute('style', 'border:none; font-size: 125%;  background: none; color: white; float:left');
	b.innerHTML = '&darr;';
	return b;
}
function bDelete(){

	var b = document.createElement('button');
	b.setAttribute('name', 'delete');
	b.setAttribute('onclick', 'Delete(this)');
	b.setAttribute('style', 'border:none; font-size: 150%;  background: none; color: #FA5858; float:Right');
	b.innerHTML = 'X';
	return b;
}
// HEADER UTILITY //
function Delete(element){
	//console.log(queryOfOperators);
	var operator = searchOperator(element);
	deleteOperatorInList(operator.id);
	GetClosestP(element).remove();
	PComplete();
}
function MoveDown(element){
	var test = GetClosestP(element);
	swapOperatorInList(test, test.nextSibling);
	swapElements(test, test.nextSibling);
	PComplete();
}
function MoveUp(element){
	var test = GetClosestP(element);
	swapOperatorInList(test, test.previousSibling);
	swapElements(test, test.previousSibling);
	PComplete();
}
function Reset(element){
	//reset.png is taken from endless icons which is free to use.
}
function swapElements(obj1, obj2) {
	console.log(obj1, obj2.constructor);
	// save the location of obj2
	if(obj2.constructor != HTMLParagraphElement){
		return;
	}
	var parent2 = obj2.parentNode;
	var next2 = obj2.nextSibling;
	// special case for obj1 is the next sibling of obj2
	if (next2 === obj1) {
		// just put obj1 before obj2
		parent2.insertBefore(obj1, obj2);
	} else {
		// insert obj2 right before obj1
		obj1.parentNode.insertBefore(obj2, obj1);

		// now insert obj1 where obj2 was
		if (next2) {
			// if there was an element after obj2, then insert obj1 right before that
			parent2.insertBefore(obj1, next2);
		} else {
			// otherwise, just append as last child
			parent2.appendChild(obj1);
		}
	}
}
function buttonstyleupdate(element){
	var test = GetClosestP(element);
	if (test.nextSibling != null){
		console.log("This can't got further down", test);
	}
}
// BODY //
function SSlice_within(event){
	var p = InsertP('Operator', 'border: 1px solid black;');
	var header = Insertheader('S-Slice - within');
	var body = InsertP('body');
	body.setAttribute('style', 'min-height: 50px; margin:0px 0px 0px 0px; background: lightgrey;');
	var p1 = InsertP('select1', 25, 100);
	var p2 = InsertP('select2', 25, 100);
	var textWithin1 = InsertTextBox('Select Geometry: ');
	textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	var pick1 = createMenuObj(['User Input', 'Spatial Level'], 'Geometry#1 from');
	var inputpick1 = InsertSingleMenu(pick1, 125, "clickedMenu(this)", "name");
	var textWithin2 = InsertTextBox('Select Geometry: ');
	textWithin2.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	var pick2 = createMenuObj(['User Input', 'Spatial Level'], 'Geometry#2 from');
	var inputpick2 = InsertSingleMenu(pick2, 125, "clickedMenu(this)", "name");
    var distance = InsertP('distance');
    distance.appendChild(InsertTextBox('Input precision value: '));
    distance.appendChild(InsertInput('', null, "clickedAttribute(this)"));

	p1.appendChild(textWithin1);
	p1.appendChild(inputpick1);
	p2.appendChild(textWithin2);
	p2.appendChild(inputpick2);
	body.appendChild(p1);
	body.appendChild(p2);
    body.appendChild(distance);
	body.appendChild(InsertBR());
	p.appendChild(header);
	p.appendChild(body);
	addOperator(p, 'SSlice');
	addProperty(p, 'spatialOperator', 'within');
	QueryStatment.appendChild(p);
}
function SDice(event){
	var p = InsertP('Operator', 'border: 1px solid black;');
	var header = Insertheader('S-Dice');
	var body = InsertP('body');
	body.setAttribute('style', 'min-height: 50px; margin:0px 0px 0px 0px; background: lightgrey;');
	body.appendChild(SpatialLevel('Select Spatial Attribute#1 from: ', 'SpatialLevel1'));
	body.appendChild(SpatialLevel('Select Spatial Attribute#2 from: ', 'SpatialLevel2'));
	var distance = InsertP('distance');
	distance.appendChild(InsertTextBox('Input precision value: '));
	distance.appendChild(InsertInput('', null, "clickedAttribute(this)"));
	body.appendChild(distance);
	p.appendChild(header);
	p.appendChild(body);
	addOperator(p, 'SDice');
	QueryStatment.appendChild(p);
}
function SRU(event){
    var p = InsertP('Operator', 'border: 1px solid black;');
    var header = Insertheader('S-Roll-up');
    var body = InsertP('body');
    body.setAttribute('style', 'min-height: 50px; margin:0px 0px 0px 0px; background: lightgrey;');
    body.appendChild(SpatialLevel('Select Spatial Attribute#1 from: ', 'SpatialLevel1'));
    body.appendChild(SpatialLevel('Select Spatial Attribute#2 from: ', 'SpatialLevel2'));
	var test = createMenuObj(DataStructureDefinition.measure, "test", false,  structureLevel.Attribute);
	body.appendChild(measureLevel('Select Measure:'));
	body.appendChild(groupBy('Select Aggregation Level: '))
	body.appendChild(InsertTextBox('Inner Select:'))
	body.appendChild(SpatialLevel('Select: ', 'innerSpatialLevel1'));
	body.appendChild(SpatialLevel('Select: ', 'innerSpatialLevel2'));
    p.appendChild(header);
    p.appendChild(body);
    addOperator(p, 'SRU');
    QueryStatment.appendChild(p);
}
// BODY UTILITY //
function SpatialLevel(optionalName, optionalID){
	var p;
	if(optionalID == null){
		p = InsertP('SpatialLevel', 25, 200);
	}
	else{
		p = InsertP(optionalID, 25, 200);
	}
	var textWithin1;
	if (optionalName != null){
		textWithin1 = InsertTextBox(optionalName);
	}
	else{
		textWithin1 = InsertTextBox('Spatial levels:');
	}
	var spatialLevelObj = createMenuObj(SpatialDimensions, 'spatial levels', true, structureLevel.Dimenasion, spatialMode.On);
	var spatialMenu = InsertMultiMenu(spatialLevelObj, 100, "SpatialLevelHelper(this)", "name");
	textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	p.appendChild(textWithin1);
	p.appendChild(spatialMenu);
	return p;
}
function measureLevel(name, optionalID){
	var p;
	var ID;
	if(optionalID == null){
		p = InsertP('measureLevel', 25, 200);
	}
	else{
		p = InsertP(optionalID, 25, 200);
	}
	var textWithin1 = InsertTextBox(name);
	var measureObj = createMenuObj(DataStructureDefinition.measure, 'spatial levels', true, structureLevel.Attribute);
	var finalobj = {name: "measure", list: []};
	for (var i in measureObj.list){
		finalobj.list.push(measureObj.list[i].name);
	}
	var measureMenu = InsertSingleMenu(finalobj, 100, "SpatialLevelHelper(this)", "label");
	textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	p.appendChild(textWithin1);
	p.appendChild(measureMenu);
	return p;
}

function groupBy(name, optionalID){
	var p;
	if(optionalID == null){
		p = InsertP('groupBY', 25, 200);
	}
	else{
		p = InsertP(optionalID, 25, 200);
	}
	var textWithin1 = InsertTextBox(name);
	var spatialLevelObj = createMenuObj(SpatialDimensions, 'Group by', true, structureLevel.Dimenasion, spatialMode.On);
	var spatialMenu = InsertMultiMenuLevel(spatialLevelObj, 100, "SpatialLevelHelper(this)", "name");
	textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	p.appendChild(textWithin1);
	p.appendChild(spatialMenu);
	return p;
}
// Operator //
function within(){
	var elements = [];
	var p1 = InsertP('select1', 25, 100);
	var p2 = InsertP('select2', 25, 100);
	var textWithin1 = InsertTextBox('Select:');
	textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	var pick1 = createMenuObj(['User Input', 'Spatial Level'], 'Geometry#1 from');
	var inputpick1 = InsertSingleMenu(pick1, 125, "clickedMenu(this)");
	var textWithin2 = InsertTextBox('Select:');
	textWithin2.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	var pick2 = createMenuObj(['User Input', 'Spatial Level'], 'Geometry#2 from');
	var inputpick2 = InsertSingleMenu(pick2, 125, "clickedMenu(this)");
	p1.appendChild(textWithin1);
	p1.appendChild(inputpick1);
	p2.appendChild(textWithin2);
	p2.appendChild(inputpick2);
	elements.push(p1);
	elements.push(p2);
	return elements;
}
// Helper //
function UserInput(){
	var div = document.createElement('div');
	var name = "mapid" + UpdateID('mapid');
	div.setAttribute('id', name);
	div.setAttribute('style', "width:90%; height: 300px; margin: 2% 2%");
	return [div, name];
	/*var p = InsertP('UserInput', 25, 200);
	var textWithin1 = InsertTextBox('Select one: ');
	var DatatypesObj = createMenuObj(DataTypes, 'DataTypes');
	textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
	p.appendChild(textWithin1);
	p.appendChild(InsertSingleMenu(DatatypesObj, 100, "clickedMenu(this)"));
	return p;*/
}
function ChangeMenuName(newName, elementInMenu){
	var mainMenuElement = elementInMenu;
	while (mainMenuElement.id != 'menu'){
		mainMenuElement = mainMenuElement.parentNode;
	}
	mainMenuElement.childNodes[0].childNodes[0].innerHTML = newName;
}
function SpatialLevelHelper(element){
	clickedMultiMenu(element);
	clickedAttribute(element);
}





			