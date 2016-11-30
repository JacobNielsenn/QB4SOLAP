/**
 * Created by Jacob on 28-11-2016.
 */

// function fSDice(event){
// 	var p = InsertP('SSlice', 'border: 1px solid #ccc; border-radius: 4px; background-color: #cce6ff;');
// 	var buttonP = InsertP('Buttons', 'margin-top: -3px;');
// 	var innerP = InsertP('Options');
// 	buttonP.appendChild(bMoveUp());
// 	buttonP.appendChild(bMoveDown());
// 	buttonP.appendChild(bDelete());
// 	var tmpList = DataTypes.slice();
// 	tmpList.push('Function');
// 	innerP.appendChild(InsertTextBox('SSlice'));
// 	innerP.appendChild(InsertDropMenu(tmpList, null, 'SSliceV_s', 100, ', FilterUpdate(this)'));
// 	p.appendChild(buttonP);
// 	p.appendChild(innerP);
// 	QueryStatment.appendChild(p);
// 	//QueryStatment.insertBefore(p, QueryStatment);
// }

// function fSRU(event){
// 	var p = InsertP('SRU', 'border: 1px solid #ccc; border-radius: 4px; background-color: #cce6ff;');
//
// 	var buttonP = InsertP('Buttons', 'margin-top: -3px;');
// 	buttonP.appendChild(bMoveUp());
// 	buttonP.appendChild(bMoveDown());
// 	buttonP.appendChild(bDelete());
//
// 	var nameP = InsertP('OprName');
// 	nameP.appendChild(InsertTextBox('SRU'));
//
// 	var RUPathP = InsertP('AdvRUPath');
// 	RUPathP.appendChild(InsertTextBox('Level<sub>b<sub>1</sub></sub>:'));
// 	RUPathP.appendChild(InsertDropMenu(DataStructureDefinition.dimension, '.name', 'baseLevel', 100, ', MoreBaseLevel(this), SpatialOptions(this)'));
// 	RUPathP.appendChild(InsertTextBox('Level<sub>s</sub>:'));
// 	RUPathP.appendChild(InsertDropMenu(null, null, 'spatialLevel', 100, ', UpdateSelectOptions(this)'));
// 	RUPathP.appendChild(InsertBR());
// 	RUPathP.appendChild(InsertTextBox('Attribute:'));
// 	RUPathP.appendChild(InsertDropMenu(null, null, 'attribute', 100, null));
// 	RUPathP.appendChild(InsertTextBox('Measure:'));
// 	RUPathP.appendChild(InsertDropMenu(DataStructureDefinition.measure, '.label', 'measure', 100, null));
//
// 	var fspatialP = InsertP('Function');
// 	fspatialP.appendChild(InsertTextBox('Spatial<sub>f</sub> :'));
// 	fspatialP.appendChild(InsertDropMenu(NumericOperations, null, 'NumericOperations', 150, ', NumericOptions(this)'));
//
// 	var aggregationP = InsertP('Aggregation');
// 	aggregationP.appendChild(InsertTextBox('Aggregation Method:'));
// 	aggregationP.appendChild(InsertDropMenu(AGG, null, 'AGG', 150));
//
// 	p.appendChild(buttonP);
// 	p.appendChild(nameP);
// 	p.appendChild(RUPathP);
// 	p.appendChild(aggregationP);
// 	p.appendChild(fspatialP);
// 	//fRUPath(p);
// 	//QueryStatment.appendChild(p);
// 	QueryStatment.insertBefore(p, QueryStatment.firstChild);
//
// }
//





//
// function debuging(element){
// 	var atr = parseInt(element.getAttribute('atr'));
// 	var stack = parseInt(element.getAttribute('stack'));
// 	if (stack == 1){
// 		console.log('Button clicked OFF');
// 		//console.log(element, atr, stack);
// 		element.setAttribute('stack', '0');
// 		var classNm = element.getAttribute('class');
// 		var classNmModified = classNm.replace('testing','');
// 		element.setAttribute('class', classNmModified);
// 		var node = element;
// 		while (node.getAttribute('id') != 'menu'){
// 			node = node.parentNode;
// 			if (node.tagName == 'LI'){
// 				var subStack = parseInt(node.getAttribute('stack'));
// 				subStack--;
// 				node.setAttribute('stack', subStack);
// 				menuToggle(node);
// 			}
// 		}
// 	}
// 	else{
// 		console.log('Button clicked ON');
// 		element.setAttribute('stack', '1');
// 		element.className += 'testing ';
// 		var node = element;
// 		while (node.getAttribute('id') != 'menu'){
// 			node = node.parentNode;
// 			if (node.tagName == 'LI'){
// 				var subStack = parseInt(node.getAttribute('stack'));
// 				subStack++;
// 				node.setAttribute('stack', subStack);
// 				menuToggle(node);
// 			}
// 		}
// 	}
// }
//
// function menuToggle(element){
// 	var stack = parseInt(element.getAttribute('stack'));
// 	console.log(element.getAttribute('class'));
// 	if (stack == 1){
// 			element.className += 'testing ';
// 		}
// 		else if (stack > 0){
// 			console.log('More then one attribute selected');
// 		}
// 		else {
// 			console.log('Remove green' ,element);
// 			var classNm = element.getAttribute('class');
// 			if (classNm != null){
// 				var classNmModified = classNm.replace(/testing/g,'');
// 				element.setAttribute('class', classNmModified);
// 			}
// 		}
// }
//
// function InsertDropMenu(list, property, name, size, updater){
// 	var ele = document.createElement('select');
// 	var id = UpdateID('p');
// 	ele.setAttribute('name', name+id);
// 	ele.setAttribute('style', 'width:' + size + 'px; margin-right: 5px;');
// 	if (updater != null){
// 		ele.setAttribute('onchange', 'print()' + updater);
// 	}
// 	else{
// 		ele.setAttribute('onchange', 'print()');
// 	}
// 	ele.setAttribute('id', id);
// 	ListOptions(ele, list, property);
// 	return ele
// }
//
// function menuBuildOperator(){
//
// }

// function RUPathAttribute(element){
// 	var ele = element
// 	var save = [];
// 	var operator = searchOperator(element);
// 	var obj = findOperatorInList(operator.id);
// 	while (ele.getAttribute('id') != 'menu'){
// 		if (ele.constructor == HTMLLIElement){
// 			save.push(ele.childNodes[0].innerHTML);
// 		}
// 		ele = ele.parentNode;
// 	}
// 	obj.aName = save[0];
// 	obj.sLevel = save[1];
// 	obj.bLevel = save[2];
// 	console.log(obj);
// 	PComplete();
// }

// function point(){
// 	var p = InsertP('point', 25, 200);
// 	var textWithin1 = InsertTextBox('Input value: ');
// 	textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
// 	p.appendChild(textWithin1);
// 	p.appendChild(InsertInput('#.#, #.#', 100, "clickedAttribute(this)"));
// 	return p;
// }