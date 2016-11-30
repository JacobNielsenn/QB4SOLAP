/**
 * Created by Jacob on 29-09-2016.
 */
function debug(obj){
    console.log(obj.constructor);
}

function checkObj(obj){
    debug(obj);
    if (obj.constructor == obj){
        return true;
    }
    else{
        return false;
    }
}

function checkListOfStrings(obj){
    debug(obj);
    if (obj.constructor == Array){
        for (var i = 0; i < obj.length; i++){
            if (obj[i].constructor != String){
                return false;
            }
        }
        return true;
    }
    else{
        return false;
    }
}

function checkListOfObjs(obj){
    debug(obj);
    if (obj.constructor == Array){
        for (var i = 0; i < obj.length; i++){
            if (obj[i].constructor != obj){
                return false;
            }
        }
        return true;
    }
    else{
        return false;
    }
}