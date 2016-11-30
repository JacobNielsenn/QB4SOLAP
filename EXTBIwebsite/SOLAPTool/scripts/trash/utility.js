/**
 * Created by Jacob on 29-11-2016.
 */
function ListPropertyFromArrayofObject(obj, propertyName){
    var result = [];
    for (var i in obj){
        for (var j in obj[i]){
            if (j == propertyName){
                result.push(obj[i][j]);
            }
        }
    }
    console.log("ListPropertyFromArrayofObject", obj, propertyName);
    return result;
}