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
}
function findOperatorInList(id){
    for (var i in queryOfOperators){
        if(queryOfOperators[i].id == id){
            return queryOfOperators[i];
        }
    }
}
function addProperty(ele, name, value){
    var operator = searchOperator(ele);
    var obj = findOperatorInList(operator.id);
    obj[name] = value;
}
