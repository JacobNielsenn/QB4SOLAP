interact('.tap-target').on(
	'tap', function (event) {
		if(selector != null){ //Use To Deselected Query Component (only one component can be active at a time)
			selector.currentTarget.classList.toggle('switch-color');
		}
		if((selector == null ? "" : selector.currentTarget.getAttribute('id')) == event.currentTarget.getAttribute('id')){ //Program will crash if you try to getAttribute from a null. This is the reason for the less desirable code.
			selector = null;
			ListOperations(LastOpr);
		}else{
			OprSelect(event);
		}
	}
);

interact('.build-target').on(
	'tap', function (event) {
		var selected = event.currentTarget.getAttribute('name');
		//console.log('selected:', selected);
		var Obj = traverse(QueryPointer, selected);
		var element;
		switch (Obj.name){
			case "SDice":
				fSDice(event);
				break;
			case 'SSlice':
				SSlice_within(event);
				break;
			case 'SRU':
				fSRU(event);
				break;
			default:
				alert("Need to define case for element");
				break;
		}
		ListOperations(selected);
		print();
		event.preventDefault();
	}
);

function UpdateMenuName(ele){
	var a = document.getElementById('dim');
	var b = document.getElementById('dimName');
	var c = document.getElementById('lvlName');

	a.innerHTML = 'Dim; ' + b.innerHTML + ' Lvl; ' + c.innerHTML + ' Atr; ' + ele.innerHTML;
}
function MoreInput(ele){
	var name = ele.name;
	var value1 = ele.value.split(',')[0];
	var value2 = ele.value.split(',')[1];
	if (!isNaN(value1) && !isNaN(value2) && !isEmpty(value1) && !isEmpty(value2)){
		if (ele.nextSibling == null){
			insertAfter(InsertInput('', 75, 'MoreInput(this)', null), ele);
		}
	}
}
function FilterUpdate(element){
	var old = element.parentNode;
	while (old.nextSibling != null){
		console.log(old.nextSibling);
		if (old.nextSibling.tagName == 'P' && ['SSlice', 'SDice'].indexOf(old.nextSibling.getAttribute('name').replace(/[0-9]/g, '')) >= 0 ){
			break;
		}
		else{
			old.nextSibling.remove();
		}
	}
	var p;
	switch (element.value){
		case 'Within':
			p = InsertP('Within');
			insertAfter(p, GetClosestP(element));
			p.appendChild(InsertTextBox('Object<sub>1</sub>:'));
			p.appendChild(InsertInput('', 75, null));
			p.appendChild(InsertTextBox('Object<sub>2</sub>:'));
			p.appendChild(InsertInput('', 75, null));
			fRUPath(p);
			break;
		case 'Distance':
			p = InsertP('Distance');
			insertAfter(p, GetClosestP(element));
			p.appendChild(InsertTextBox('Number:'));
			p.appendChild(InsertInput(''));
			p.appendChild(InsertDropMenu(RelationalOperators, null, 'RelationalOperators', 150));
			fRUPath(p);
			break;
		case 'Point':
			p = InsertP('Point');
			insertAfter(p, GetClosestP(element));
			p.appendChild(InsertTextBox('Point:'));
			p.appendChild(InsertInput('', 75, null));
			fRUPath(p);
			break;
		case 'Polygon':
			p = InsertP('Polygon');
			insertAfter(p, GetClosestP(element));
			p.appendChild(InsertTextBox('Polygon:'));
			p.appendChild(InsertInput('', 75, 'MoreInput(this)'));
			fRUPath(p);
			break;
		case 'Multi Polygon':
			p = InsertP('Polygon');
			insertAfter(p, GetClosestP(element));
			p.appendChild(InsertTextBox('Polygon 1:'));
			p.appendChild(InsertInput('', 75, 'MoreInput(this), MorePolyon(this)'));
			fRUPath(p);
			break;
		case 'Function':
			p = InsertP('Function');
			insertAfter(p, GetClosestP(element));
			fRUPath(GetClosestP(element));
			p.appendChild(InsertTextBox('Inner Select:'));
			p.appendChild(InsertTextBox('Spatial<sub>f</sub> :'));
			p.appendChild(InsertDropMenu(AGG, null, 'AGG', 150));
			p.appendChild(InsertDropMenu(NumericOperations, null, 'NumericOperations', 150));
			fRUPath(p);
			break;
		default:
			alert('Not IMPLMENTED yet');
	}

}
function MorePolyon(ele){
	var p = GetClosestP(ele);
	if (p.nextSibling == null || p.nextSibling.getAttribute('name').replace(/[0-9]/g, '') != 'Polygon'){
		var p = InsertP('Polygon');
		var closetP = GetClosestP(ele);
		var number = parseInt(closetP.innerHTML.split('Polygon ')[1].split(':')[0])+1;
		insertAfter(p, closetP);
		p.appendChild(InsertTextBox('Polygon ' + number + ':'));
		p.appendChild(InsertInput('', 75, 'MoreInput(this), MorePolyon(this)'));
	}
}
function MoreBaseLevel(ele){
	console.log(ele);
	var p = GetClosestP(ele);
	var number = parseInt(ele.previousSibling.innerHTML.split('b<sub>')[1].split('<')[0])+1;
	if(ele.value != defaultvalue && ele.nextSibling.nextSibling.getAttribute('name').replace(/[0-9]/g, '') != 'baseLevel'){
		var text = InsertTextBox('Level<sub>b<sub>' + number + '</sub></sub>');
		var menu = InsertDropMenu(DataStructureDefinition.dimension, '.name', 'baseLevel', 100, ', MoreBaseLevel(this), SpatialOptions(this)');
		insertAfter(text, ele);
		insertAfter(menu, text);
	}
}
function NumericOptions(element){
	var p = GetClosestP(element);
	var old = HtmlSearch(p, 'NumericOptions');
	if (old != null){
		old.remove();
	}
	if(element.value == 'Distance'){
		var newp = InsertP('NumericOptions');
		newp.appendChild(InsertTextBox('Level:'));
		newp.appendChild(InsertDropMenu(DataStructureDefinition.levelProperty, '.levelProperty', 'levelOne', 100, ', AttributeOptions(this)'));
		newp.appendChild(InsertTextBox('Attribute:'));
		newp.appendChild(InsertDropMenu(null, null, 'levelOneAtr', 100, null));
		newp.appendChild(InsertBR());
		newp.appendChild(InsertTextBox('Level:'));
		newp.appendChild(InsertDropMenu(DataStructureDefinition.levelProperty, '.levelProperty', 'levelTwo', 100, ', AttributeOptions(this)'));
		newp.appendChild(InsertTextBox('Attribute:'));
		newp.appendChild(InsertDropMenu(null, null, 'levelTwoAtr', 100, null));
		p.appendChild(newp);
	}
}
function UpdateSelectOptions(element){
	var p = GetClosestP(element);
	console.log(element, p);
	if(element.name.replace(/[0-9]/g, '') == 'baseLevel'){
		var ele = HtmlSearch(p, 'spatialLevel');
		var objlist = traverse(DataStructureDefinition.dimension, element.value+'Dim');
		ListOptions(ele, objlist.hasHierarchy[0].hierarchy.hasLevel);
	}
	else if(element.name.replace(/[0-9]/g, '') == 'spatialLevel'){
		var ele = HtmlSearch(p, 'attribute');
		var objlist = traverse(DataStructureDefinition.levelProperty, element.value, 'levelProperty');
		ListOptions(ele, ListPropertyFromArrayofObject(objlist.levelAttribute, 'label'));
	}
}
function fRUPath(){
	var p = InsertP('RUPath');
	var RUPathObj = createMenuObj(Dimensions, 'Dimensions', true, structureLevel.Dimenasion);
	var RUPathEle = InsertMultiMenu(RUPathObj, '100', 'RUPathAttribute(this)');
	p.appendChild(RUPathEle);
	return p;
}
function polygon(){
}
function multiPolygon(){

}


// - Building Structure Variables //
var QueryPointer;
var _QueryOptions, QueryOptions, _Query, Query;
var _SDiceOptions, SDiceOptions, _SDice, SDice;
var _SSliceOptions, SSliceOptions, _SSlice, SSlice;
var _SRUOptions, SRUOptions, _SRU, SRU;

var QueryTextString;
var UniqueID = 0;

var selector = null;
var LastOpr = "null";
var currentP;
var tab = '   ';
var defaultvalue = '--Select--';

var Gfilter = "aa";

var treeRoot = {
	parent: null,
	child: null,
	lastAdded: null
};

var list = new DLL.DoublyLinkedList();