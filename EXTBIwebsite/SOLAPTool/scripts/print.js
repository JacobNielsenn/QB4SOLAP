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
function PathName(variableName, nameSpace){
    for (var name in nameSpace){
        if (nameSpace[name].replace(/[0-9]/g, '') == variableName){
            return nameSpace[name];
        }
    }
    var newName = variableName + UpdateID(variableName);
    nameSpace.push(newName);
    return newName;
}
//Printer
function PComplete(){
	var Query = "";
    Filter = "";
	for (var i = 0; i < queryOfOperators.length; i++){
	    if (i == 0){
            Query += POperator(queryOfOperators[i]);
        }
        else{
            Query += "{" + POperator(queryOfOperators[i]) + "}}\n";
        }

	}
    Query += Filter + "}";
	GeneratedQueryElement.innerHTML = Query;
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
	var result = "SELECT " + name("?obs", obj) + " WHERE \n{\n";
	result += RUPath(obj);
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
    var result = "SELECT " + name("?obs", obj) + " WHERE \n{\n";
    result += RUPath(obj);
    result += "SELECT " + name("?obs", obj) + " WHERE \n{\n";
    //result += RUPath(obj, obj.bind, obj.groupby);
    Filter += "FILTER";
    AfterFilter += "GroupBy";
    return result;
}
function PSSWithin(obj){
	var result = "SELECT " + name("?obs", obj) + " WHERE \n{\n";
	result += RUPath(obj);
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
// Helper functions
function name(variableName, object){
    if (!(object.hasOwnProperty("names"))){
        object.names = [];
        var newName = variableName + UpdateID(variableName);
        object.names.push(newName);
        return newName;
    }
    else{
        for (number in object.names){
            if (object.names[number].replace(/[0-9]/g, '') == variableName){
                return object.names[number];
            }
        }
        var newName = variableName + UpdateID(variableName);
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