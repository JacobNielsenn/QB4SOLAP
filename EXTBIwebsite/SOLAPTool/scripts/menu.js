function createMenuObj(list, label, lookup, inStruct, mode){
	var obj = {};
	obj.name = label;
	obj.list = [];
	for (var i in list){
		if (lookup == true){
			switch (inStruct){
				case (structureLevel.Attribute):
					obj.list.push({name: list[i]});
					break;
				case (structureLevel.Level):
					var obj1 = traverse(DataStructureDefinition.levelProperty, list[i], 'levelProperty');
					var newlist = [];
					if(obj1.hasOwnProperty('hasGeometry')){
						for (var o in obj1.hasAttribute){
							newlist.push(obj1.hasAttribute[o]);
						}
						for (var o in obj1.hasGeometry){
							newlist.push(obj1.hasGeometry[o]);
						}
					}
					if (mode == spatialMode.On){
						if(obj1.hasOwnProperty('hasGeometry')){
							obj.list.push(createMenuObj(obj1.hasGeometry, list[i], lookup, inStruct-1, mode));
						}
					}
					else{
						if(obj1.hasOwnProperty('hasGeometry')){

							obj.list.push(createMenuObj(newlist, list[i], lookup, inStruct-1, mode));
						}
						else{
							obj.list.push(createMenuObj(obj1.hasAttribute, list[i], lookup, inStruct-1, mode));
						}
					}

					break;
				case (structureLevel.Dimenasion):
					var obj1 = traverse(DataStructureDefinition.dimension, list[i], 'dimensionProperty');
					for (var hi in obj1.dimensionProperty.hasHierarchy){
						if (mode == spatialMode.On){
							var dimensionContainSpatialLevel = false;
							for (var level in obj1.dimensionProperty.hasHierarchy[hi].hierarchy.hasLevel){
								var obj2 = traverse(DataStructureDefinition.levelProperty, obj1.dimensionProperty.hasHierarchy[hi].hierarchy.hasLevel[level], 'levelProperty');
								if(obj2.hasOwnProperty('hasGeometry')){
									dimensionContainSpatialLevel = true;
								}
							}
							if (dimensionContainSpatialLevel == true){
								obj.list.push(createMenuObj(obj1.dimensionProperty.hasHierarchy[hi].hierarchy.hasLevel, list[i], lookup, inStruct-1, mode));
							}
						}
						else{
							obj.list.push(createMenuObj(obj1.dimensionProperty.hasHierarchy[hi].hierarchy.hasLevel, list[i], lookup, inStruct-1, mode));
						}

					}
					break;
				default:
					console.log("Something is wrong:", inStruct, lookup)
			}
		}
		else
			obj.list.push({name: list[i]});
	}
	return obj;
}
//Called from HTML elements
function SpatialOptions(element){
	var elements = GetClosestP(element).childNodes;
	var spatialLevel = HtmlSearch(GetClosestP(element), 'spatialLevel');
	var levelList = [];
	var ele = 0;
	var spatialList = [];
	//console.log(elements, spatialLevel);
	for (var i = 0; i < elements.length; i++){
		if (elements[i].getAttribute('name').replace(/[0-9]/g, '') == 'baseLevel' && elements[i].value != defaultvalue){
			var obj = traverse(DataStructureDefinition.dimension, elements[i].value+'Dim');
			var list;
			if (obj.hasOwnProperty('hasHierarchy')){
				list = obj.hasHierarchy[0].hierarchy.hasLevel;
			}
			else {
				list = [];
			}
			ele++;
			for (var l in list){
				levelList.push(list[l]);
			}
		}
	}
	for (var i in levelList){
		var count = 0;
		for (var l in levelList){
			if (levelList[i] == levelList[l]){
				count++;
			}
			if (count == ele){
				if (spatialList.indexOf(levelList[i]) == -1){
					spatialList.push(levelList[i]);
				}
			}
		}
	}
	ListOptions(spatialLevel, spatialList);
}
function AttributeOptions(element){
	var obj = traverse(DataStructureDefinition.levelProperty, element.value);
	console.log(obj);
	var p = GetClosestP(element);
	var name = element.name.replace(/[0-9]/g, '') + 'Atr';
	var atr = HtmlSearch(p, name);
	var list = [];
	for (var i in obj.levelAttribute){
		list.push(obj.levelAttribute[i].levelAttribute);
	}
	ListOptions(atr, list);
}
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
		OperationsList.appendChild(InsertBR());
	}
}
function OprSelect(event){
	selector = event;
	event.currentTarget.classList.toggle('switch-color');
	event.preventDefault();
	ListOperations(LastOpr);
}
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
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}
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
// Used for generate the menus
function mainMenu(width){
	/*<ul id="menu" name="menu">*/
	var menu = document.createElement('ul');
	var menuID = UpdateID('ul');
	menu.setAttribute('id', 'menu');
	menu.setAttribute('name', 'ul'+menuID);
	menu.setAttribute('style', 'width:' + width + 'px; float: left;');
	return menu;
}
function liMenu(){
	var li = document.createElement('li');
	var liID = UpdateID('li');
	li.setAttribute('id', liID);
	li.setAttribute('name', 'li'+liID);
	//li.setAttribute('onclick', 'debuging(this)');
	li.setAttribute('stack', '0');
	return li;
}
function atrMenu(text, updater){
	var a = document.createElement('a');
	var aID = UpdateID('a');
	a.setAttribute('id', aID);
	a.setAttribute('name', 'a'+aID);
	a.setAttribute('onclick', updater);
	a.innerHTML = text;
	return a;
}
function ulMenu(){
	var innerMenu = document.createElement('ul');
	var innerMenuID = UpdateID('ul');
	innerMenu.setAttribute('id', innerMenuID);
	innerMenu.setAttribute('name', 'ul'+innerMenuID);
	return innerMenu;
}
function InsertSingleMenu(objectArrays, width, updater, field){
	var menu = mainMenu(width);
	var li = liMenu(width);
	var a = atrMenu(objectArrays.name);
	var innerMenu = ulMenu();
	menu.appendChild(li);
	li.appendChild(a);
	li.appendChild(innerMenu);
	for (var i in objectArrays.list){
		var innerli = liMenu();
		var aref = atrMenu(objectArrays.list[i][field], updater);
		innerMenu.appendChild(innerli);
		innerli.appendChild(aref);
	}
	return menu;
}
function InsertMultiMenu(objectArrays, width, updater, field){
	var menu = mainMenu(width);
	var li = liMenu(width);
	var a = atrMenu(objectArrays.name);
	var innerMenu = ulMenu();
	menu.appendChild(li);
	li.appendChild(a);
	li.appendChild(innerMenu);
	for (var i in objectArrays.list){
		var innerli = liMenu();
		var aref = atrMenu(objectArrays.list[i][field]);
		var ul = ulMenu();
		innerMenu.appendChild(innerli);
		innerli.appendChild(aref);
		innerli.appendChild(ul);
		for (var l in objectArrays.list[i].list){
			var lli = liMenu();
			var ref = atrMenu(objectArrays.list[i].list[l][field]);
			var uul = ulMenu();
			ul.appendChild(lli);
			lli.appendChild(ref);
			lli.appendChild(uul);
			for (var k in objectArrays.list[i].list[l].list){
				var liatr = liMenu();
				var aatr = atrMenu(objectArrays.list[i].list[l].list[k][field], updater);
				uul.appendChild(liatr);
				liatr.appendChild(aatr);
			}
		}
	}
	return menu;
}

function InsertMultiMenuLevel(objectArrays, width, updater, field){
	var menu = mainMenu(width);
	var li = liMenu(width);
	var a = atrMenu(objectArrays.name);
	var innerMenu = ulMenu();
	menu.appendChild(li);
	li.appendChild(a);
	li.appendChild(innerMenu);
	for (var i in objectArrays.list){
		var innerli = liMenu();
		var aref = atrMenu(objectArrays.list[i][field]);
		var ul = ulMenu();
		innerMenu.appendChild(innerli);
		innerli.appendChild(aref);
		innerli.appendChild(ul);
		for (var l in objectArrays.list[i].list){
			var lli = liMenu();
			var aatr = atrMenu(objectArrays.list[i].list[l][field], updater);
			ul.appendChild(lli);
			lli.appendChild(aatr);
		}
	}
	return menu;
}
// Used to add property to the object in the queryOfOperators list.
function clickedAttribute(element){
	var operator = searchOperator(element);
	var obj = findOperatorInList(operator.id);
	var cls = findOperatorInClass(operator.id);
	switch (obj.name){
		case ("SSlice"):
			switch (obj.spatialOperator){
				case ("within"):
					var p = GetClosestP(element)
					if (p.getAttribute('name').indexOf('distance') != -1){
                        addProperty(element, 'distance', element.value);
                        console.log('distance changed', cls);
                        cls.setDistance = element.value;
					}
					else{
                        while (p.getAttribute('name').indexOf('select') == -1){
                            p = p.previousElementSibling;
                        }
                        if (p.getAttribute('name').indexOf('1') != -1){
                            console.log("first");
                            if (element.innerHTML != ""){
                                addProperty(element, 'first', element.innerHTML);
                                cls.setFirst = element.innerHTML;
                            }
                            else{
                                addProperty(element, 'first', element.value);
                                cls.setFirst = element.value;
                            }
                        }
                        else if (p.getAttribute('name').indexOf('2') != -1){
                            console.log("second");
                            if (element.innerHTML != ""){
                                addProperty(element, 'second', element.innerHTML);
                                cls.setSecond = element.innerHTML;
                            }
                            else{
                                addProperty(element, 'second', element.value);
                                cls.setSecond = element.value;
                            }
                        }
					}
					break;
			}
			break;
		case ("SDice"):
			addProperty(element, 'distance', element.value);
			if (element.value != undefined){
                cls.setDistance = element.value;
			}
			break;
		case ("SRU"):
			switch (GetClosestP(element).getAttribute('name')){
				case ("measureLevel"):
					var agg = traverse(DataStructureDefinition.measure, element.innerHTML, "aggregateFunction");
					addProperty(element, 'measure', agg.measure);
                    cls.setMeasure = agg.measure;
					addProperty(element, 'agg', agg.aggregateFunction);
                    cls.setAgg = agg.aggregateFunction;
					break;
				case ("groupBY"):
					addProperty(element, 'groupBY', element.innerHTML);
                    var path = "";
                    while (element.getAttribute('id').indexOf('menu') == -1){
                        element = element.parentNode;
                        if (element.constructor == HTMLLIElement)
                        {
                            path += element.childNodes[0].innerHTML + ","
                        }
                    }
                    cls.setAggregationLevel = path;
					break;
				case ("spatialFunction"):
					addProperty(element, 'spatialFunction', "bif:" + element.innerHTML);
                    cls.setSpatialFunction = element.innerHTML;
					break;
				case ("agg"):
					addProperty(element, 'aggFunction', element.innerHTML);
                    cls.setAggFunction = element.innerHTML;
					break;
			}
			break;
		default:
			console.log(obj, obj.spatialOperator, "is not implemented yet");
			break;
	}
    console.log(operator, obj, cls);
	PComplete();
}
// Used to change the name of the multimenu after it has been clicked.
function clickedMultiMenu(element){
	var operator = searchOperator(element);
	var obj = findOperatorInList(operator.id);
    var cls = findOperatorInClass(operator.id);
	var ele = element;
	var path = "";
	while (ele.getAttribute('id').indexOf('menu') == -1){
		ele = ele.parentNode;
		if (ele.constructor == HTMLLIElement)
		{
			path += ele.childNodes[0].innerHTML + ","
		}
	}
	ChangeMenuName(element.innerHTML, element);
	if (GetClosestP(element).getAttribute('name') == 'SpatialLevel'){
		addProperty(element, "path", path);
		cls.setPath1 = path;

	}
	if (GetClosestP(element).getAttribute('name') == 'SpatialLevel1'){
		addProperty(element, "path1", path);
        cls.setPath1 = path;
	}
	if (GetClosestP(element).getAttribute('name') == 'SpatialLevel2'){
		addProperty(element, "path2", path);
        cls.setPath2 = path;
	}
	if (GetClosestP(element).getAttribute('name') == 'SpatialLevel1SRU'){
		addProperty(element, "path1", path);
		addProperty(element, "innerPath1", path);
        cls.setPath1 = path;
	}
	if (GetClosestP(element).getAttribute('name') == 'SpatialLevel2SRU'){
		addProperty(element, "path2", path);
		addProperty(element, "innerPath2", path);
        cls.setPath2 = path;
	}
	if (GetClosestP(element).getAttribute('name') == 'groupBY'){
		addProperty(element, "groupBYPath", path);
	}
}
function clickedMenu(element){
	var bodyElement = element;
	var menuHeaderElement = element;
	//Find Body of the operator were clicked was used in.
	while (bodyElement.getAttribute('name').replace(/[0-9]/g, '') != 'body'){
		bodyElement = bodyElement.parentNode;
	}
	while (menuHeaderElement.getAttribute('id') != 'menu'){
		menuHeaderElement = menuHeaderElement.parentNode;
	}
	switch(element.innerHTML){
		case ('Within'):
			//menuHeaderElement.innerHTML = element.innerHTML;
			list = within();
			for (ele in list){
				ChangeMenuName('within', element);
				bodyElement.appendChild(list[ele]);
			}
			addProperty(element, 'spatialOperator', 'within');
			break;
		case ('Map'):
			//menuHeaderElement.innerHTML = element.innerHTML;
			var p = GetClosestP(element);
			ChangeMenuName('Map', element);
			var data = UserInput();
			insertAfter(data[0], p);
			initializeMap(data[1]);
			break;
		case('Spatial Level'):
			var p = GetClosestP(element);
			ChangeMenuName('Spatial level', element);
			insertAfter(SpatialLevel(), p);
			//menuHeaderElement.innerHTML = element.innerHTML;
			break;
		case('Point'):
			var p = GetClosestP(element);
			ChangeMenuName('Point', element);
			addProperty(element, 'userInput', 'Point');
			insertAfter(point(), p);
			break;
		default:
			console.log('Not handled:', element.innerHTML);
			break;
	}
}

