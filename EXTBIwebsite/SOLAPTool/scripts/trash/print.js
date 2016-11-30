/**
 * Created by Jacob on 28-11-2016.
 */



function attributeInLevel(attributeName, level){
    var tmp = traverse(DataStructureDefinition.levelProperty, level, "levelProperty");
    for (var i = 0; i < tmp.levelAttribute.length; i++){
        if (tmp.levelAttribute[i].levelAttribute == attributeName){
            return true;
        }
    }
    return false;
}

function baseID(level){
    var tmp = traverse(DataStructureDefinition.levelProperty, level, "levelProperty");
    return (DataStructureDefinitionName + tmp.levelAttribute[0].levelAttribute);
}