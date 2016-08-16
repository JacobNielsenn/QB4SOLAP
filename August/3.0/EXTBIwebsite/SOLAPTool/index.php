<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<script type="text/javascript" src="scripts/interact.js"></script>
	<script type="text/javascript" src="scripts/loadSchema.js"></script>
	<title>EXTBI</title>
	<meta charset="iso-8859-1">
	<link rel="stylesheet" href="../styles/layout.css" type="text/css">
	<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
	<script type="text/javascript">
		////////////////////////////////////
		//         Struct Factory         //
			function makeStruct(names) {
				var names = names.split(' ');
				var count = names.length;
				function constructor() {
					for (var i = 0; i < count; i++) {
						this[names[i]] = arguments[i];
					}
				}
				return constructor;
			}
		////////////////////////////////////
		//        Global Variables        //
			var treeRoot = {
				parent: null,
				child: null,
				lastAdded: null
			};
			var GeneratedQueryElement;
			var QueryStatment;
			var QueryTextString;
			var UniqueID = 0;  
			var ID = {};  
			var selector = null;
			var LastOpr = "null";
			var currentP;
			var tab = '   ';
			var defaultvalue = '--Select--';
		// - SOLAP 						  //
			var SpatialAggregation = ['Union', 'Intersection', 'Buffer', 'ConvexHull', 'MinimumBoundingRectangle'];
			var TopologicalRelations = ['Intersects', 'Disjoint', 'Equals', 'Overlaps', 'Contains', 'Within', 'Touches', 'Covers', 'CoveredBy', 'Crosses', 'Distance'];
			var NumericOperations = ['Perimeter', 'Area', 'NoOfInteriorRings' , 'Distance', 'HaversineDistance', 'NearstNeighbor', 'NoOfGeometries'];
			var DataTypes = ['Point', 'Polygon', 'Multi Polygon'];
			var RelationalOperators = ['Not equal', 'Equal', 'Greater than or equal', 'Less than or equal', 'Greater than', 'Less than'];
			var AGG = ['MAX', 'MIN', 'AVG'];
		// - Building Structure Variables //
			var QueryPointer;
			var _QueryOptions, QueryOptions, _Query, Query;
			var _SDiceOptions, SDiceOptions, _SDice, SDice;
			var _SSliceOptions, SSliceOptions, _SSlice, SSlice;
			var _SRUOptions, SRUOptions, _SRU, SRU;
		////////////////////////////////////
		// Methods - Initialize & Utility //
			window.onload = Initialize; 
			function Initialize(){
				GeneratedQueryElement = document.getElementById('GeneratedQuery');
				QueryStatment = document.getElementById('OprSelectStatement'); 
				InitializeBuildStructure();
				ListOperations('Query');
				convertDataToObjects(data);
				console.log(DataStructureDefinition);
			}

			function InitializeBuildStructure(){
				_SRUOptions 	= makeStruct('SDice SSlice SRU');
				SRUOptions 		= new _SRUOptions(SDice, SSlice, SRU);
				_SRU 			= makeStruct('id name options');
				SRU 			= new _SRU(3, 'SRU', SRUOptions);

				_SSliceOptions 	= makeStruct('SDice SSlice SRU');
				SSliceOptions 	= new _SSliceOptions(SDice, SSlice, SRU);
				_SSlice 		= makeStruct('id name options');
				SSlice 			= new _SSlice(2, 'SSlice', SSliceOptions);

				_SDiceOptions 	= makeStruct('SDice SSlice SRU');
				SDiceOptions 	= new _SDiceOptions(SDice, SSlice, SRU);
				_SDice 			= makeStruct("id name select_0 value_0 options");
				SDice 			= new _SDice(1, 'SDice', TopologicalRelations, '-Select-', SDiceOptions);

				_QueryOptions 	= makeStruct('SDice SSlice SRU');
				QueryOptions 	= new _QueryOptions(SDice, SSlice, SRU);
				_Query 			= makeStruct("id name options");
				Query 			= new _Query(0, 'Query', QueryOptions);

				QueryPointer = Query;
			}

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

			function GetClosestP(element){	
				var p = element.parentNode;
				while (p.tagName != 'P'){
					console.log(p, p.tagName);
				}
			return p;
			}

			//Return object of the Query tree structure
			function traverse(o, target, tag){
				//console.log('Looking for:', target);
				var obj;
				for (var i in o) {
					if (o[i] == target){
						if (typeof tag == 'undefined'){
								return o;
						}
						else{
							if (typeof o[tag] != 'undefined'){
								//console.log(o, 'Found Value');
								return o;
							}
						}
					}
					if (o[i] !== null && typeof(o[i])=="object") {
						obj = traverse(o[i], target, tag);
						if (obj != null){
							return obj;
						}
					}
				}
			}

			function traversetargets(o, target, tag){
				//console.log('Looking for:', target);
				var obj;
				var found = [];
				var allfound = false;
				for (var i in o) {
					for (var l in target){
						for (var j in tag){
							if (o[tag[j]] != 'undefined'){
								found[j] = true;
							}
							for (var h in found){
								if (found[h] == false){
									allfound = false;
									//console.log(allfound);
									continue;
								}
							}
							if (allfound){
								return o;
							}
						}
					}
				}
				for (var i in o){
					if (o[i] !== null && typeof(o[i])=="object") {
						obj = traverse(o[i], target, tag);
						if (obj != null){
							return obj;
						}
					}
				}
			}

			function insertAfter(newNode, referenceNode) {
	  		referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
			}

			function findElementType(array, name){
				for (var i in array){
					if (array[i].getAttribute('name').replace(/[0-9]/g, '') == name){
						return array[i];
					}
				}
			}

			function ListPropertyFromArrayofObject(obj, propertyName){
				var result = [];
				for (var i in obj){
					for (var j in obj[i]){
						if (j == propertyName){
							result.push(obj[i][j]);
						} 
					}
				}		
				return result;
			}
		////////////////////////////////////
		//            OnChange            //
			function MoreInput(ele){
				var name = ele.name;
				var value1 = ele.value.split(',')[0];
				var value2 = ele.value.split(',')[1];
				console.log(value1, value2);
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
				console.log(ele, p.nextSibling);
				if (p.nextSibling == null || p.nextSibling.getAttribute('name').replace(/[0-9]/g, '') != 'Polygon'){
					var p = InsertP('Polygon');
					var closetP = GetClosestP(ele);
					var number = parseInt(closetP.innerHTML.split('Polygon ')[1].split(':')[0])+1;
					insertAfter(p, closetP);
					p.appendChild(InsertTextBox('Polygon ' + number + ':'));
					p.appendChild(InsertInput('', 75, 'MoreInput(this), MorePolyon(this)'));
				}
			}

			function HtmlSearch(p, name){
				while (p != null){
					for(var i in p.childNodes){
						if (p.childNodes[i].getAttribute('name').replace(/[0-9]/g, '') == name){
							return p.childNodes[i];
						}
					}
					p = p.nextSibling;
				}
			}

			function UpdateSelectOptions(element){
				var p = GetClosestP(element);
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
		////////////////////////////////////
		//    Methods - List Operators    //
			function ListOperations(selector){
				var OperationsList = document.getElementById('OperationsList');
				OperationsList.innerHTML = "";
				var obj = traverse(QueryPointer, selector);
				for(var i in obj.options){
					var text = document.createElement('text');
					text.setAttribute('class', 'build-target noselect');
					text.setAttribute('name', i);        
					text.innerHTML = i;
					OperationsList.appendChild(text);
					InsertBR(OperationsList);
				}
			}

			function OprSelect(event){
				selector = event;
				event.currentTarget.classList.toggle('switch-color');
				event.preventDefault();
				ListOperations(LastOpr);
			}
		////////////////////////////////////
		//     Methods - List Options     //
			function ListOptions(ele, list, property){
				ele.innerHTML = "";
				InsertOption(ele, defaultvalue);
				for(var i in list){
					if (typeof list[i] == 'string'){
						InsertOption(ele, list[i]);
					}
					else{
						InsertOption(ele, eval('list[' + i + ']' + property));
					}
				}
			}

			function InsertOption(ele, text){
				var option = document.createElement('option');
				option.setAttribute('id', text);
				option.innerHTML = text;
				ele.appendChild(option);
			}
		////////////////////////////////////
		//     Buttons & Functionality    //
		// - Buttons					  //
			function bMoveUp(){
				var b = document.createElement('button');
				b.setAttribute('name', 'up');
				b.setAttribute('onclick', 'MoveUp(this), buttonstyleupdate(this), print()');
				b.setAttribute('class', 'udbutton stylebutton');
				b.setAttribute('style', 'border-radius: 4px 0px 0px 0px; margin-left: 0px; margin-top: 0px; border:none;');
				b.innerHTML = '&uarr;';
				return b;
			}

			function bMoveDown(){
				var b = document.createElement('button');
				b.setAttribute('name', 'down');
				b.setAttribute('onclick', 'MoveDown(this), buttonstyleupdate(this), print()');
				b.setAttribute('class', 'udbutton stylebutton');
				b.setAttribute('style', 'border-radius: 0px 0px 0px 0px; margin-left: 0px; margin-top: 0px; border:none;');
				b.innerHTML = '&darr;';
				return b;
			}

			function bDelete(){
				
				var b = document.createElement('button');
				b.setAttribute('name', 'delete');
				b.setAttribute('onclick', 'Delete(this), print()');
				b.setAttribute('class', 'dbutton stylebutton');
				b.setAttribute('style', 'border-radius: 0px 0px 4px 0px; margin-left: 0px; margin-top: 0px; border:none;');
				b.innerHTML = 'X';
				return b;
			}
		// - Update Button style 		  //
			function buttonstyleupdate(element){
				var test = GetClosestP(GetClosestP(element));
				if (test.nextSibling != null){
					console.log("This can't got further down", test);
				}
			}
		// - Methods					  //
			function Delete(element){
				GetClosestP(GetClosestP(element)).remove();
			}

			function MoveDown(element){
				var test = GetClosestP(GetClosestP(element));
				swapElements(test, test.nextSibling);
			}

			function MoveUp(element){
				var test = GetClosestP(GetClosestP(element));
				swapElements(test, test.previousSibling);
			}

			function swapElements(obj1, obj2) {
			    // save the location of obj2
			    if(obj2 == null){
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
		////////////////////////////////////
		//       Methods Operations       //
			function fSDice(event){
				var p = InsertP('SDice', 'border: 1px solid #ccc; border-radius: 4px; background-color: #cce6ff;');
				var buttonP = InsertP('Buttons', 'margin-top: -3px;');
				var innerP = InsertP('Options');
				buttonP.appendChild(bMoveUp());
				buttonP.appendChild(bMoveDown());
				buttonP.appendChild(bDelete());
				innerP.appendChild(InsertTextBox('SDice'));
				innerP.appendChild(InsertDropMenu(TopologicalRelations, null, "TopologicalRelations", 100, ', FilterUpdate(this)'));
				p.appendChild(buttonP);
				p.appendChild(innerP);
				QueryStatment.insertBefore(p, QueryStatment.firstChild);
			}

			function fSSlice(event){
				var p = InsertP('SSlice', 'border: 1px solid #ccc; border-radius: 4px; background-color: #cce6ff;');
				var buttonP = InsertP('Buttons', 'margin-top: -3px;');
				var innerP = InsertP('Options');
				buttonP.appendChild(bMoveUp());
				buttonP.appendChild(bMoveDown());
				buttonP.appendChild(bDelete());
				var tmpList = DataTypes.slice();
				tmpList.push('Function');
				innerP.appendChild(InsertTextBox('SSlice'));
				innerP.appendChild(InsertDropMenu(tmpList, null, 'SSliceV_s', 100, ', FilterUpdate(this)'));
				p.appendChild(buttonP);
				p.appendChild(innerP);
				QueryStatment.insertBefore(p, QueryStatment.firstChild);
			}

			function fRUPath(event){
				var p = InsertP('RUPath');
				insertAfter(p, event);
				p.appendChild(InsertTextBox('Level<sub>b</sub>:'));
				p.appendChild(InsertDropMenu(DataStructureDefinition.dimension, '.name', 'baseLevel', 100));
				p.appendChild(InsertTextBox('Level<sub>s</sub>:'));
				p.appendChild(InsertDropMenu(null, null, 'spatialLevel', 100, null));
				p.appendChild(InsertTextBox('Attribute:'));
				p.appendChild(InsertDropMenu(null, null, 'attribute', 100, null));
			}

			function fSpatial(paragrahp){
				var p = InsertP('Function');
				p.appendChild(InsertTextBox('Spatial<sub>f</sub> :'));
				p.appendChild(InsertDropMenu(NumericOperations, null, 'NumericOperations', 150));
				var pp = InsertP('InnerSelect');
				pp.appendChild(InsertTextBox('Inner Select:'));
				pp.appendChild(InsertDropMenu(AGG, null, 'AGG', 150));
				paragrahp.appendChild(p);
				paragrahp.appendChild(pp);
				fRUPath(pp);
			}

			function fSRU(event){
				var p = InsertP('SRU', 'border: 1px solid #ccc; border-radius: 4px; background-color: #cce6ff;');
				var buttonP = InsertP('Buttons', 'margin-top: -3px;');
				var innerP = InsertP('Options');
				buttonP.appendChild(bMoveUp());
				buttonP.appendChild(bMoveDown());
				buttonP.appendChild(bDelete());
				innerP.appendChild(InsertTextBox('SRU'));
				fSpatial(innerP);
				p.appendChild(buttonP);
				p.appendChild(innerP);
				QueryStatment.insertBefore(p, QueryStatment.firstChild);
			}
		////////////////////////////////////
		//    Methods - Generate Graph    //
			function print(){
				var c = document.getElementById('OprSelectStatement').childNodes;
				var dist = document.getElementById('GeneratedQuery');
				dist.innerHTML = '';
				var filtervariable = '';
				var filterType = '';
				var useFilter = false;
				var operations = [];
				for (var i in c){	
					if (c[i].tagName === "P" && c[i].id !== 'StartQuery'){
						var tmp = c[i].getAttribute('name');
						switch(tmp.replace(/[0-9]/g, '')){
							case 'SSlice':
							case 'SDice':	
							case 'SRU':
								var select = '';
								var where = '';
								var filter = '';
								if (operations.length == 0){
									select = 'SELECT ?obs WHERE {\n';
								}
								else{
									select = '{SELECT ?obs WHERE {\n';
								}
								
								var inside = c[i].childNodes;
								for (var l = 0; l < inside.length; l++){
									var subtmp = inside[l].getAttribute('name');
									switch(subtmp.replace(/[0-9]/g, '')){
										case'Within':
											filter = 'FILTER (bif:st_within("POINT(' + inside[l].childNodes[1].value +')", "POINT(' + inside[l].childNodes[3].value + ')")\n';
											break;
										case'RUPath':
											if(inside[l].childNodes[1].value != defaultvalue && inside[l].childNodes[3].value != defaultvalue && inside[l].childNodes[5].value != defaultvalue){
												where = RUPath(inside[l].childNodes[1].value, inside[l].childNodes[3].value, inside[l].childNodes[5].value);
											}
											break;
										case'Options':
										case'Buttons':
											//Do Nothing.
											break;
										default:
											console.log(subtmp, 'not implemented');
									}
								}
								operations.push(select+where+filter);
								dist.innerHTML = '';
								for (var o in operations){
									dist.innerHTML += operations[o];
								}
								dist.innerHTML += '}';
								break;
							default:
								console.log('PRINT! NOT IMPLMENTED YET', tmp);
						}
					}
				}
			}

			function RUPath(lb, ls, aID){
				var result = '';
				var obj = traverse(DataStructureDefinition, lb);
				var baseLevelAttributeID = DataStructureDefinitionName + obj.hasAttribute[0];
				var baseLevelName = '?' + obj.levelProperty;
				var memberOf = DataStructureDefinitionName + obj.levelProperty;
				var aIDName = traverse(DataStructureDefinition, aID).levelAttribute;
				var spatialAttribute = DataStructureDefinitionName + aIDName;
				var levels = traverse(DataStructureDefinition.dimension, lb, '0');
				var inLevel = false;
				var outLevel = false

				result += tab + '?obs rdf:type qb:Observation .\n';								//Algo1 Line 2
				result += tab + '?obs ' + baseLevelAttributeID + ' ' + baseLevelName + ' .\n';	//Algo1 Line 3
				result += tab + baseLevelName + ' qb4o:memberOf ' + memberOf + ' .\n';			//Algo1 Line 3
				for (var i in levels){
					if (levels[i] == lb){
						inLevel = true
					}
					else if (inLevel && !outLevel){
						result += tab + baseLevelName + ' skos:broader ' + '?' + levels[i] + ' .\n';				//Algo1 Line 5
						result += tab + '?' + levels[i] + ' skos:broader ' + '?' + ls + ' .\n';					//ALgo1 Line 5
					}
					if (levels[i] == ls){
						outLevel = true
					}
				}
				result += tab + '?' + ls + ' ' + spatialAttribute + ' ?' + aIDName + ' .\n';			//ALgo1 Line 6
				return result;
			}
		////////////////////////////////////
		//    Methods - Building Blocks   //
			function InsertBR(ele){
				var br = document.createElement('br');
				ele.appendChild(br);
			}

			function InsertP(name, style){
				var P = document.createElement('p');
				var id = UpdateID('p');
				P.setAttribute('id', id);
				P.setAttribute('name', name+id);
				if (style != null){
					P.setAttribute('style','margin-top: 0px; margin-bottom: 0px;' + style);
				}
				else {
					P.setAttribute('style','margin-top: 0px; margin-bottom: 0px;');
				}
				return P;
			}

			function InsertTextBox(title){
				var TextBox = document.createElement('text');
				var id = UpdateID('p');
				TextBox.setAttribute('name', 'textbox'+id);
				TextBox.setAttribute('class', "tap-target noselect");  
				TextBox.setAttribute('id', id);
				TextBox.setAttribute('style', 'margin-right: 5px;');
				TextBox.innerHTML = title;
				return TextBox;
			}

			function InsertInput(text, size, updater, tooltipText){
				var ele = document.createElement('input');
				var id = UpdateID('p');
				ele.setAttribute('name', 'input'+id);
				ele.setAttribute('id', id);
				ele.setAttribute('value', text);
				ele.setAttribute('type', 'text');
				ele.setAttribute('style', 'width:'+ size +'px; margin-right: 5px;');
				if (updater != null){
					ele.setAttribute('onchange', updater);
				}
				else{
					//ele.setAttribute('onchange', '');
				}
				/*if (tooltipText != null){
					span = InsertSpan(null);
					span.setAttribute('class', 'tooltip');
					span.appendChild(InsertSpan('decimal, decimal'));
					span.appendChild(ele);
					return span;
				}
				else{*/
					return ele;
				//}
			}

			function InsertSpan(text){
				var ele = document.createElement('span');
				var id = UpdateID('p');
				ele.setAttribute('name', 'span'+id);
				ele.setAttribute('id', id);
				if (text != null){
					ele.setAttribute('class', 'tooltiptext');
					ele.innerHTML = text;
				}
				return ele;
			}

			function InsertDropMenu(list, property, name, size, updater){
				var ele = document.createElement('select');
				var id = UpdateID('p');
				ele.setAttribute('name', name+id);
				ele.setAttribute('style', 'width:' + size + 'px; margin-right: 5px;');
				if (updater != null){
					ele.setAttribute('onchange', 'UpdateSelectOptions(this), print()' + updater);
				}
				else{
					ele.setAttribute('onchange', 'UpdateSelectOptions(this), print()');
				}
				ele.setAttribute('id', id);
				ListOptions(ele, list, property);
				return ele
			}	
		////////////////////////////////////
		//          Click System          //
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
							fSSlice(event);
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
	</script>
	<style type="text/css">
		.noselect {
			-webkit-touch-callout: none; /* iOS Safari */
			-webkit-user-select: none;   /* Chrome/Safari/Opera */
			-khtml-user-select: none;    /* Konqueror */
			-moz-user-select: none;      /* Firefox */
			-ms-user-select: none;       /* Internet Explorer/Edge */
			user-select: none;           /* Non-prefixed version, currently not supported by any browser */                               
		}

		.tap-target, .build-target {
			cursor: pointer;
			transition: all 0.3s;
		}

		.tap-target.switch-color{
			color: green;
			text-decoration: underline;
		}

		.appear {
	    width: 250px; 
	    border: #000 2px solid;
	    background:#F8F8F8;
	    position: relative;
	    top: 5px;
	    left:15px;
	    display:none;
	    padding: 0 20px 20px 20px;
	    z-index: 1000000;
		}
		.hover  {
	    cursor:pointer;
	    width: 5px;
		}
		.hover:hover .appear {
	    display:block;
		}

		/* Tooltip */
			.tooltip {
			    position: relative;
			}

			.tooltip .tooltiptext {
			    visibility: hidden;
			    width: 120px;
			    max-height: 50px;
			    background-color: black;
			    color: #fff;
			    text-align: center;
			    border-radius: 6px;
			    padding: 0px 0;
			    
			    /* Position the tooltip */
			    position: absolute;
			    z-index: 1;
			    bottom: 100%;
			    left: 50%;
			    margin-left: -60px;
			}

			.tooltip:hover .tooltiptext{
				visibility: visible;
			}

		input[type=text], select {
			display: inline-block;
			border: 1px solid #ccc;
			border-radius: 4px;
			width: 40px;
			box-sizing: border-box;
		}

		/* Button Style */
			.stylebutton {
				border-radius: 0px;
				/*border-color: #ccc;*/
				transition-duration: 0.4s;
			}
			.dbutton {
				background-color: #ff6666;
				color: white;	
			}

			.dbutton:hover {
				background-color: white;
				color: red;
			}

			.udbutton {
				background-color: #33adff;
				color: white;	
			}

			.udbutton:hover {
				background-color: white;
				color: blue;
			}
			.stylebutton:hover{
				/*box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);*/
			}
	</style>
</head>
<body class="about QB4SOLAP">
	<div class="wrapper row1">
	<header id="header" class="clear">
	<?php include '../logo.html';?>
	<?php include '../menu.html';?>
	</header>
	</div>
	<!-- content -->
	<div class="wrapper row2">
		<div id="container" class="clear">
			<!--<section id="slider"><a href="#"><img src="images/demo/960x360.gif" alt=""></a></section>-->
			<?php include 'headline.html';?>
			<!-- content body -->
			<div id="content">
				<!-- main content -->   
				<aside id="left_column">
					<h2 class="title">SOLAP<br>Operators</h2>
					<section class="last" id="OperationsList" style="margin-left:10px;"></section>
				</aside>
				<section style="margin-left:10px;">
					<div class="no-top-margin" style="float:left;">
						<section>
							<p id="StartQuery" name="Start" style="margin-top: 0px; margin-bottom: 0px;">Query:</p>
							<div id="OprSelectStatement" style="margin-top:0px; height:500px;  width:500px; overflow-y: scroll;">	
							</div>
							<div>
								<p>Generated query from the above operators:</p>
								<textarea id="GeneratedQuery" style="margin-top:0px; width:494px; height: 300px; overflow-y: scroll;"></textarea>
							</div>
						</section>   
					</div>
				</section>
			</div>
			<!-- right column -->  
			<aside id="right_column">
				<?php include '../topics.html';?>
				<?php include 'resources.html';?>
				<!-- /nav -->
				<?php include 'conference.html';?>
				<?php include 'contact.html';?>
			</aside>
			<!-- / content body -->
		</div>
	</div>
	<!-- footer -->
	<div class="wrapper row3">
		<?php include '../footer.html';?>
		<?php include '../analytics.html';?>
	</div>
</body>
</html>