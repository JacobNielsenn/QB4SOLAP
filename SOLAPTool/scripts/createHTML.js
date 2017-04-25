//Basic HTML components
function InsertP(name, minheight, minwidth){
    var P = document.createElement('p');
    var id = UpdateID('p');
    P.setAttribute('id', id);
    P.setAttribute('name', name);
    P.setAttribute('style','margin-top: 0px; margin-bottom: 0px; min-height:' + minheight + 'px; min-width:' + minwidth + 'px;');
    return P;
}
function InsertDiv(){
    var div = document.createElement('div');
    var id = UpdateID('div');
    div.setAttribute('id', id);
    div.setAttribute('name', 'div'+id);
    return div;
}
function InsertBR(){
    var br = document.createElement('br');
    var id = UpdateID('br');
    br.setAttribute('id', id);
    br.setAttribute('name', 'br'+id);
    return br;
}
function InsertTextBox(title){
    var TextBox = document.createElement('text');
    var id = UpdateID('p');
    TextBox.setAttribute('name', 'textbox'+id);
    TextBox.setAttribute('class', "tap-target noselect");
    TextBox.setAttribute('id', id);
    TextBox.setAttribute('style', 'margin-right: 5px; padding: 0px 0px 5px 5px;');
    TextBox.innerHTML = title;
    return TextBox;
}
function InsertInput(text, size, updater, tooltipText){
    var ele = document.createElement('input');
    var id = UpdateID('p');
    ele.setAttribute('name', 'input'+id);
    ele.setAttribute('id', id);
    ele.setAttribute('placeholder', text);
    ele.setAttribute('type', 'text');
    ele.setAttribute('style', 'width:'+ size +'px; margin-right: 5px;  padding: 0px 0px 0px 0px;');
    if (updater != null){
        ele.setAttribute('onchange', updater);
    }
    return ele;
}
//Menu HTML components
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
//header and button HTML components
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
//Operator HTML components
function SSlice_within(event){
    var p = InsertP('Operator', 'border: 1px solid black;');
    var header = Insertheader('S-Slice');
    var body = InsertP('body');
    body.setAttribute('style', 'min-height: 50px; margin:0px 0px 0px 0px; background: lightgrey;');
    var p1 = InsertP('select1', 25, 100);
    var p2 = InsertP('select2', 25, 100);
    var textWithin1 = InsertTextBox('Select Geometry: ');
    textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
    var pick1 = createMenuObj(['Map', 'Spatial Level'], 'Geometry#1 from');
    var inputpick1 = InsertSingleMenu(pick1, 125, "clickedMenu(this)", "name");
    var textWithin2 = InsertTextBox('Select Geometry: ');
    textWithin2.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
    var pick2 = createMenuObj(['Map', 'Spatial Level'], 'Geometry#2 from');
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
    Q.add(new OSlice("SSlice", p.id));
    QueryStatment.appendChild(p);
    Q.avaiableOperators();
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
    Q.add(new ODice("SDice", p.id));
    QueryStatment.appendChild(p);
    Q.avaiableOperators();
}
function SRU(event){
    var p = InsertP('Operator', 'border: 1px solid black;');
    var header = Insertheader('S-Roll-up');
    var body = InsertP('body');
    body.setAttribute('style', 'min-height: 50px; margin:0px 0px 0px 0px; background: lightgrey;');
    body.appendChild(SpatialLevel('Select Spatial Attribute#1 from: ', 'SpatialLevel1SRU'));
    body.appendChild(SpatialLevel('Select Spatial Attribute#2 from: ', 'SpatialLevel2SRU'));
    var test = createMenuObj(DataStructureDefinition.measure, "test", false,  structureLevel.Attribute);
    body.appendChild(measureLevel('Select Measure:'));
    body.appendChild(groupBy('Select Aggregation Level: '));
    body.appendChild(sFunction('Select Spatial Function: '));
    body.appendChild(aggFunction('Select Agg Function: '));
    p.appendChild(header);
    p.appendChild(body);
    Q.add(new OSRU("SRU", p.id));
    QueryStatment.appendChild(p);
    Q.avaiableOperators();
}
//Operator Logic
function clickedAttribute(element){
    var operator = searchOperator(element);
    var cls = findOperatorInClass(operator.id);
    switch (cls.name){
        case ("SSlice"):
            var p = GetClosestP(element)
            if (p.getAttribute('name').indexOf('distance') != -1){
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
                        cls.setFirst = element.innerHTML;
                    }
                    else{
                        cls.setFirst = element.value;
                    }
                }
                else if (p.getAttribute('name').indexOf('2') != -1){
                    console.log("second");
                    if (element.innerHTML != ""){
                        cls.setSecond = element.innerHTML;
                    }
                    else{
                        cls.setSecond = element.value;
                    }
                }
            }
        case ("SDice"):
            if (element.value != undefined){
                cls.setDistance = element.value;
            }
            break;
        case ("SRU"):
            switch (GetClosestP(element).getAttribute('name')){
                case ("measureLevel"):
                    var agg = traverse(DataStructureDefinition.measure, element.innerHTML, "aggregateFunction");
                    cls.setMeasure = agg.measure;
                    cls.setAgg = agg.aggregateFunction;
                    break;
                case ("groupBY"):
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
                    cls.setSpatialFunction = element.innerHTML;
                    break;
                case ("agg"):
                    cls.setAggFunction = element.innerHTML;
                    break;
            }
            break;
        default:
            console.log(cls, "is not implemented yet");
            break;
    }
    Q.printQuery();
}
function clickedMultiMenu(element){
    var operator = searchOperator(element);
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
        cls.setPath1 = path;

    }
    if (GetClosestP(element).getAttribute('name') == 'SpatialLevel1'){
        cls.setPath1 = path;
    }
    if (GetClosestP(element).getAttribute('name') == 'SpatialLevel2'){
        cls.setPath2 = path;
    }
    if (GetClosestP(element).getAttribute('name') == 'SpatialLevel1SRU'){
        cls.setPath1 = path;
    }
    if (GetClosestP(element).getAttribute('name') == 'SpatialLevel2SRU'){
        cls.setPath2 = path;
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
            list = within();
            for (ele in list){
                ChangeMenuName('within', element);
                bodyElement.appendChild(list[ele]);
            }
            break;
        case ('Map'):
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
            break;
        case('Point'):
            var p = GetClosestP(element);
            ChangeMenuName('Point', element);
            insertAfter(point(), p);
            break;
        default:
            console.log('Not handled:', element.innerHTML);
            break;
    }
}
function Delete(element){
    console.log(Q.opertorList, GetClosestP(element));
    Q.deleteOperator(GetClosestP(element).id);
    //var operator = searchOperator(element);
    //deleteOperatorInList(operator.id);
    GetClosestP(element).remove();
    Q.printQuery();
    Q.avaiableOperators();
}
function MoveDown(element){
    Q.avaiableOperators();
    if (Q.opertorList.length != 1){
        var test = GetClosestP(element);
        //swapOperatorInList(test, test.nextSibling);
        Q.swapOperator(test, test.nextSibling);
        swapElements(test, test.nextSibling);
        Q.printQuery();
    }
}
function MoveUp(element){
    Q.avaiableOperators();
    if (Q.opertorList.length != 1){
        var test = GetClosestP(element);
        //swapOperatorInList(test, test.previousSibling);
        Q.swapOperator(test, test.previousSibling);
        swapElements(test, test.previousSibling);
        Q.printQuery();
    }
}
//Used by buttons to move the elements
function swapElements(obj1, obj2) {
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
    if (Q.opertorList.length != 1) {
        var test = GetClosestP(element);
        if (test.nextSibling != null) {
            console.log("This can't got further down", test);
        }
    }
}
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
function sFunction(name, optionalID){
    var p;
    if(optionalID == null){
        p = InsertP('spatialFunction', 25, 200);
    }
    else{
        p = InsertP(optionalID, 25, 200);
    }
    var textWithin1 = InsertTextBox(name);
    var spatialLevelObj = createMenuObj(SpatialFunction, 'Spatial Function', false, SpatialFunction, spatialMode.Off);
    var spatialMenu = InsertSingleMenu(spatialLevelObj, 110, "SpatialLevelHelper(this)", "name");
    textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
    p.appendChild(textWithin1);
    p.appendChild(spatialMenu);
    return p;
}
function aggFunction(name, optionalID){
    var p;
    if(optionalID == null){
        p = InsertP('agg', 25, 200);
    }
    else{
        p = InsertP(optionalID, 25, 200);
    }
    var textWithin1 = InsertTextBox(name);
    var spatialLevelObj = createMenuObj(AGG, 'Agg Function', false, AGG, spatialMode.Off);
    var spatialMenu = InsertSingleMenu(spatialLevelObj, 110, "SpatialLevelHelper(this)", "name");
    textWithin1.setAttribute('style', 'padding: 0px 0px 0px 5px; float: left;');
    p.appendChild(textWithin1);
    p.appendChild(spatialMenu);
    return p;
}
function UserInput(){
    var div = document.createElement('div');
    var name = "mapid" + UpdateID('mapid');
    div.setAttribute('id', name);
    div.setAttribute('style', "width:90%; height: 300px; margin: 2% 2%");
    return [div, name];
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