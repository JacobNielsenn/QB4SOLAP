//Functions used to create the object required to generate the query.
function searchOperator(ele){
    var findOperator = ele;
    while (findOperator.getAttribute('name') != 'Operator'){
        findOperator = findOperator.parentNode;
    }
    return findOperator;
}
function addOperator(ele, name){
    var obj = {id: ele.id, name: name};
    queryOfOperators.push(obj);
    if (queryOfOperators[0].name == "SRU"){
        document.getElementById('mes').disabled = true;
    }
}
function findOperatorInList(id){
    for (var i in queryOfOperators){
        if(queryOfOperators[i].id == id){
            return queryOfOperators[i];
        }
    }
}
function deleteOperatorInList(id){
    for (var i in queryOfOperators){
        if(queryOfOperators[i].id == id){
            queryOfOperators.splice(i,1);
        }
    }
}
function addProperty(ele, name, value){
    var operator = searchOperator(ele);
    var obj = findOperatorInList(operator.id);
    obj[name] = value;
}
function swapOperatorInList(ele1, ele2){
    console.log('Start', ele1.id, ele2.id);
    for (var i in queryOfOperators){
        for (var j in queryOfOperators) {
            if (queryOfOperators[i].id == ele1.id && queryOfOperators[j].id == ele2.id) {
                queryOfOperators[j] = queryOfOperators.splice(i, 1, queryOfOperators[j])[0];
                return null;
            }
        }
    }
}
