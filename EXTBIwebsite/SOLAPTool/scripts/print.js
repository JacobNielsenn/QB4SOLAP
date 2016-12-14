var tab = '   ';
var Filter = "";
var AfterFilter = "";
function RUPath(object, additionalPath, measure){
	var result = '';
    var path, lb, ls;
    result += RUStart(object);
    if (object.hasOwnProperty('path')){
        path = object.path.split(',');
        lb = path[2];
        ls = path[1];
        result += RUBuildPath(lb, ls, object, object.names);
    }
    else{
        path = object.path1.split(',');
        lb = path[2];
        ls = path[1];
        object.path1Names = [];
        result += RUBuildPath(lb, ls, object, object.path1Names);
        path = object.path2.split(',');
        lb = path[2];
        ls = path[1];
        object.path2Names = [];
        result += RUBuildPath(lb, ls, object, object.path2Names);
    }
	return result;
}
function RUStart(object, measure){
    var result = tab + name("?obs", object) + " " + 'rdf:type qb:Observation .\n';								//Algo1 Line 2
    var measurevariable;
    if (measure != null){
        measurevariable = '?' + measure;
        result += tab + name("?obs", object) + " " + DataStructureDefinitionName + measure + ' ' + measurevariable + ' .\n';
    }
    return result;
}
function RUBuildPath(lb, ls, object, namespace){
    var obj;
    var obj1;
    var levels = traverse(DataStructureDefinition.dimension, lb, '0');
    var aIDName = traverse(DataStructureDefinition, ls, "levelProperty").hasGeometry[0];
    var spatialAttribute = DataStructureDefinitionName + aIDName;
    var baseLevelName;
    var LevelName;
    var memberOf;
    var baseLevelAttributeID;
    var result = '';
    for (var i in levels){
        obj = traverse(DataStructureDefinition, levels[i], 'hasAttribute');
        if (i!=0){
            obj1 = traverse(DataStructureDefinition, levels[i-1], 'hasAttribute');
            baseLevelName = '?' + obj1.levelProperty;
        }
        else{
            baseLevelName = '?' + obj.levelProperty;
        }

        baseLevelAttributeID = DataStructureDefinitionName + obj.hasAttribute[0];
        LevelName = '?' + obj.levelProperty;
        memberOf = DataStructureDefinitionName + obj.levelProperty;
        if(i == 0){
            result += tab + name("?obs", object) + " " + baseLevelAttributeID + ' ' + PathName(LevelName, namespace) + " " + ' .\n';	//Algo1 Line 3
        }
        else {
            //result += tab + name(baseLevelName, object) + " " + baseLevelAttributeID + ' ' + name(LevelName, object) + " " + ' .\n';	//Algo1 Line 3
        }
        result += tab + PathName(LevelName, namespace) + " " + ' qb4o:memberOf ' + memberOf + ' .\n';			//Algo1 Line 3
        if (levels[i] == ls){
            break;
        }
        result += tab + PathName(LevelName, namespace) + " " + ' skos:broader ' + PathName('?' + nextLevel(levels[i]), namespace) + " " + ' .\n';
    }
    result += tab + PathName('?' + ls, namespace) + " " + ' ' + spatialAttribute + " " + PathName('?' + aIDName, namespace) + " " + ' .\n';			//ALgo1 Line 6
    return result;
}
// Helper functions to RUPath
function nextLevel(currentLevel){
	var tmp = traverse(DataStructureDefinition.dimension, currentLevel, "0");
	for (var i = 0; i < tmp.length; i++){
		if (currentLevel == tmp[i]){
			return tmp[i+1];
		}
	}
}
function UpdateNameID(name){
    for (var i in NameID){
        if (i == name){
            NameID[i] += 1;
            return NameID[i];
        }
    }
    NameID[name] = 1;
    return NameID[name];
}
function PathName(variableName, nameSpace){
    for (var name in nameSpace){
        if (nameSpace[name].replace(/[0-9]/g, '') == variableName){
            return nameSpace[name];
        }
    }
    var newName = variableName + UpdateNameID(variableName);
    nameSpace.push(newName);
    return newName;
}
//Printer
function PComplete(){
	var Query = "";
    Filter = "";
    AfterFilter = "";
	for (var i = 0; i < queryOfOperators.length; i++){
	    if (i == 0){
            Query += POperator(queryOfOperators[i]);
        }
        else{
            Query += "{" + POperator(queryOfOperators[i]) + "}}\n";
        }

	}
	if (Query != ""){
        Query += Filter + "}\n";
    }
    Query += AfterFilter;
	GeneratedQueryElement.innerHTML = Query;
    cleanUp(queryOfOperators);
}
function POperator(obj){
	switch (obj.name){
		case ('SSlice'):
			return PSSlice(obj);
			break;
		case ('SDice'):
			return PSDice(obj);
			break;
        case ('SRU'):
            return PSRU(obj);
            break;
		default:
			break;
	}
}
function PSSlice(obj){
	switch (obj.spatialOperator){
		case ('within'):
			return PSSWithin(obj);
		default:
			break;
	}
}
function PSDice(obj){
	var result;
    if (additionalQuery){
        result = "SELECT " + AdditionalQueryOptions(obj) + " WHERE \n{\n";
    }
    else{
        result = "SELECT " + name("?obs", obj) + " WHERE \n{\n";
    }
	result += RUPath(obj);
    if (additionalQuery){
        result += AdditionalQueryOptionsInRUPath(obj);
    }
    else{
        //Nothing
    }
    console.log(obj);
    compareName(obj.path1.split(',')[0], obj.path1Names);
    if (!(typeof(obj.distance) == 'undefined')){
        if (obj.distance == ""){

            Filter += 'FILTER (bif:st_within(' + compareName(obj.path1.split(',')[0], obj.path1Names) + ', ' + compareName(obj.path2.split(',')[0], obj.path2Names) + '))\n';
        }
        else{
            Filter += 'FILTER (bif:st_within(' + compareName(obj.path1.split(',')[0], obj.path1Names) + ', ' + compareName(obj.path2.split(',')[0], obj.path2Names) + ', ' + obj.distance + '))\n';
        }
    }
    else{
        Filter += 'FILTER (bif:st_within(' + compareName(obj.path1.split(',')[0], obj.path1Names) + ', ' + compareName(obj.path2.split(',')[0], obj.path2Names) + '))\n';
    }
	return result;
}
function PSRU(obj){
    var result = "SELECT " + name("?obs", obj) + " " + name("?" + obj.groupBYPath.split(',')[1] + obj.groupBYPath.split(',')[0], obj) + " (" + obj.agg.toUpperCase() + "(" + name("?" + obj.measure, obj) +") AS " + name("?total" + obj.measure, obj) + ")" + " WHERE \n{\n";
    result += RUPath(obj);
    //?sup skos:broader ?supplierCity
    result += tab + PathName("?" + obj.path1.split(',')[1], obj.path1Names) + " skos:broader " + name("?" + obj.groupBYPath.split(',')[1] + obj.groupBYPath.split(',')[0], obj) + " .\n";
    result += tab + name("?" + obj.groupBYPath.split(',')[1] + obj.groupBYPath.split(',')[0], obj) + " qb4o:memberOf " + DataStructureDefinitionName + obj.groupBYPath.split(',')[0] + " .\n";
    result += tab + name("?obs", obj) + " " + DataStructureDefinitionName + obj.measure + " " + name("?" + obj.measure, obj) + " .\n{\n";
    if (obj.hasOwnProperty('innerobj') == false && obj.hasOwnProperty('innerPath1') == true && obj.hasOwnProperty('innerPath2') == true){
        obj.innerobj = {path1: obj.innerPath1, path2: obj.innerPath2, names: []};
    }
    var tmp = RUPath(obj.innerobj);
    result += "SELECT " + PathName("?" + obj.innerobj.path1.split(',')[1], obj.innerobj.path1Names) + " (" + obj.aggFunction + "(" + name("?" + obj.spatialFunction.split('_')[1], obj) + ") AS " + name("?" + obj.aggFunction + obj.spatialFunction.split('_')[1], obj) + ")" + " WHERE \n{\n";
    result += tmp;
    result += "BIND (" + obj.spatialFunction + "( " + PathName("?" + obj.innerobj.path1.split(',')[0], obj.innerobj.path1Names) + ", " + PathName("?" + obj.innerobj.path2.split(',')[0], obj.innerobj.path2Names) + " ) AS " + name("?" + obj.spatialFunction.split('_')[1], obj) + ")}\n";
    result += "GROUP BY " + PathName("?" + obj.innerobj.path1.split(',')[1], obj.innerobj.path1Names) + "}\n";
        //BIND (bif:st_distance( ?cust1Geo, ?sup1Geo ) AS ?distance)}
        //GROUP BY ?cust1 }
    Filter += "FILTER (" + PathName("?" + obj.path1.split(',')[1], obj.path1Names) + " = " + PathName("?" + obj.innerobj.path1.split(',')[1], obj.innerobj.path1Names) + " && " + obj.spatialFunction + "( " + PathName("?" + obj.path1.split(',')[0], obj.path1Names) + ", " + PathName("?" + obj.path2.split(',')[0], obj.path2Names) + " ) = " + name("?" + obj.aggFunction + obj.spatialFunction.split('_')[1], obj) + " )" + "\n";
             //FILTER (?cust = ?cust1 && bif:st_distance( ?custGeo, ?supGeo ) = ?minDistance)
    AfterFilter += "GROUP BY " + name("?obs", obj) + " " + name("?" + obj.groupBYPath.split(',')[1] + obj.groupBYPath.split(',')[0], obj) + "\n";
    return result;
}
function PSSWithin(obj){
    var result;
    if (additionalQuery){
        result = "SELECT " + AdditionalQueryOptions(obj) + " WHERE \n{\n";
    }
    else{
        result = "SELECT " + name("?obs", obj) + " WHERE \n{\n";
    }
    result += RUPath(obj);
    if (additionalQuery){
        result += AdditionalQueryOptionsInRUPath(obj);
    }
    else{
        //Nothing
    }
	var path = obj.path.split(',');
	var aIDName = traverse(DataStructureDefinition, path[1], "levelProperty").hasGeometry[0];
	if (obj.first.indexOf(',') != -1){
        Filter += 'FILTER (bif:st_within(' + PFillUser(obj, obj.first) + ', ' + name('?' + aIDName, obj) + " " +',' + obj.distance + '))\n';
	}
	else{
        Filter += 'FILTER (bif:st_within(' + name('?' + aIDName, obj) + " " + ', ' + PFillUser(obj, obj.second) +',' + obj.distance +'))\n';
	}
	return result;
}
function PFillUser(obj, value){
	var userdata;
	switch (obj.userInput){
		case ('Point'):
			userdata = "bif:st_point(" + value + ")";
			break;
		default:
			break;
	}
	return userdata;
}
function AdditionalQueryOptions(obj){
    var query = name("?cityName",obj) + " (SUM(" + name("?sales",obj) + ") AS " + name("?totalSales",obj) + " )\n(SUM(" + name("?quantity",obj) + ") AS " + name("?totalQuantity",obj) +
        ")\n(AVG(" + name("?discount",obj) + ") AS " + name("?averageDiscount",obj) + ")\n(AVG(" + name("?unitPrice",obj) + ") AS " + name("?averageUnitPrice",obj) +
        ")\n(SUM(" + name("?freight",obj) + ") AS " + name("?totalFreight",obj) + ")";
    return query;
}
function AdditionalQueryOptionsInRUPath(obj) {
    var query = "";
    if (isNameInNamespace("?customer", obj, "names")){
        if (isNameInNamespace("?city", obj, "names")){
            query += tab + PathName("?city", obj.names) + " gnw:cityName " + name("?cityName",obj) + " .\n"
        }
        else{
            query += tab + PathName("?customer", obj.names) + " skos:broader " + PathName("?city",obj.names) + " .\n" +
                tab + PathName("?city", obj.names) + " qb4o:memberOf gnw:city  .\n" +
                tab + PathName("?city", obj.names) + " gnw:cityName " + name("?cityName",obj) + " .\n";
        }
    }
    else if (isNameInNamespace("?customer", obj, "path1Names")){
        if (isNameInNamespace("?city", obj, "path1Names")){
            query += tab + PathName("?city", obj.path1Names) + " gnw:cityName " + name("?cityName",obj) + " .\n"
        }
        else{
            query += tab + PathName("?customer", obj.path1Names) + " skos:broader " + PathName("?city",obj.path1Names) + " .\n" +
                tab + PathName("?city", obj.path1Names) + " qb4o:memberOf gnw:city  .\n" +
                tab + PathName("?city", obj.path1Names) + " gnw:cityName " + name("?cityName",obj) + " .\n";
        }
    }
    else if (isNameInNamespace("?customer", obj, "path2Names")){
        if (isNameInNamespace("?city", obj, "path2Names")){
            query += tab + PathName("?city", obj.path2Names) + " gnw:cityName " + name("?cityName",obj) + " .\n"
        }
        else{
            query += tab + PathName("?customer", obj.path2Names) + " skos:broader " + PathName("?city",obj.path2Names) + " .\n" +
            tab + PathName("?city", obj.path2Names) + " qb4o:memberOf gnw:city  .\n" +
            tab + PathName("?city", obj.path2Names) + " gnw:cityName " + name("?cityName",obj) + " .\n";
        }
    }
    query += tab + name("?obs", obj) + " gnw:salesAmount " + name("?sales",obj) + " .\n" +
            tab + name("?obs", obj) + " gnw:quantity " + name("?quantity",obj) + " .\n" +
            tab + name("?obs", obj) + " gnw:discount " + name("?discount",obj) + " .\n" +
            tab + name("?obs", obj) + " gnw:unitPrice " + name("?unitPrice",obj) + " .\n" +
            tab + name("?obs", obj) + " gnw:freight " + name("?freight",obj) + " .\n";
    return query;
}
// Helper functions
function name(variableName, object){
    if (!(object.hasOwnProperty("names"))){
        object.names = [];
        var newName = variableName + UpdateNameID(variableName);
        object.names.push(newName);
        return newName;
    }
    else{
        for (var number in object.names){
            if (object.names[number].replace(/[0-9]/g, '') == variableName){
                return object.names[number];
            }
        }
        var newName = variableName + UpdateNameID(variableName);
        object.names.push(newName);
        return newName;
    }
}
function compareName(findname, listofNames){
    for (var i in listofNames){
        if (listofNames[i].replace('?','').replace(/[0-9]/g, '').indexOf(findname) != -1 ){
            return listofNames[i];
        }
    }
}
function cleanUp(operators){
    for (var i in operators){
        if (operators[i].hasOwnProperty('names')){
            delete operators[i].names;
        }
        if (operators[i].hasOwnProperty('path1Names')){
            delete operators[i].path1Names;
        }
        if (operators[i].hasOwnProperty('path2Names')){
            delete operators[i].path2Names;
        }
    }
    NameID = {};
}
function isNameInNamespace(variableName, object, field){
    if (!(object.hasOwnProperty(field))){
        return false;
    }
    else{
        for (var number in object[field]){
            if (object[field][number].replace(/[0-9]/g, '') == variableName){
                return true;
            }
        }
        return false;
    }
}