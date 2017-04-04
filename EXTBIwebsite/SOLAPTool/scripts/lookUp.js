//Find Closest P element from the input element.
function GetClosestP(element){
	var p = element.parentNode;
	while (p.constructor != HTMLParagraphElement){
		p = p.parentNode;
	}
	return p;
}
//Lookup Objects
function traverse(o, target, tag){
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
//Lookup html element by its name
function HtmlSearch(p, name){
	console.log('HtmlSearch', p, name);
	while (p != null){
		for(var i in p.childNodes){
			if (typeof p.childNodes[i].tagName != 'undefined'){
				if (p.childNodes[i].getAttribute('name').replace(/[0-9]/g, '') == name){
					return p.childNodes[i];
				}
				else if (p.childNodes[i].tagName == 'P'){
					var tmp = HtmlSearch(p.childNodes[i], name);
					if (tmp != null){
						return tmp;
					}
				}
			}
		}
		p = p.nextSibling;
	}
	return null;
}
function searchOperator(ele){
    var findOperator = ele;
    while (findOperator.getAttribute('name') != 'Operator'){
        findOperator = findOperator.parentNode;
    }
    return findOperator;
}
function findOperatorInList(id){
    for (var i in queryOfOperators){
        if(queryOfOperators[i].id == id){
            return queryOfOperators[i];
        }
    }
}
function findOperatorInClass(id){
    console.log("Search for:", id);
    for (var i in Q.list){
        if(Q.list[i].id == id){
            return Q.list[i];
        }
    }
}
// Helper functions to RUPath
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
// Helper functions
function name(variableName, object, forceNewName){
    if (forceNewName == null){
        forceNewName = false;
    }
    if (!(object.hasOwnProperty("names"))){
        object.names = [];
        var newName = variableName + UpdateNameID(variableName);
        object.names.push(newName);
        return newName;
    }
    else{
        for (var number in object.names){
            if (forceNewName == false && object.names[number].replace(/[0-9]/g, '') == variableName){
                return object.names[number];
            }
        }
        var newName = variableName + UpdateNameID(variableName);
        object.names.push(newName);
        return newName;
    }
}