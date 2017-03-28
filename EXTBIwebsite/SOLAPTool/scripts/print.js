var tab = '   ';
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
