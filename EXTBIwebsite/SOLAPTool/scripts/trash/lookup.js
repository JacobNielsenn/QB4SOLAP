//Return object of the Query tree structure
// function traversetargets(o, target, tag){
// 	console.log('traversetargets', o, target, tag);
// 	var obj;
// 	var found = [];
// 	var allfound = false;
// 	for (var i in o) {
// 		for (var l in target){
// 			for (var j in tag){
// 				if (o[tag[j]] != 'undefined'){
// 					found[j] = true;
// 				}
// 				for (var h in found){
// 					if (found[h] == false){
// 						allfound = false;
// 						//console.log(allfound);
// 						continue;
// 					}
// 				}
// 				if (allfound){
// 					return o;
// 				}
// 			}
// 		}
// 	}
// 	for (var i in o){
// 		if (o[i] !== null && typeof(o[i])=="object") {
// 			obj = traverse(o[i], target, tag);
// 			if (obj != null){
// 				return obj;
// 			}
// 		}
// 	}
// }
// function findElementType(array, name){
// 	console.log('findElementType', array, name);
// 	for (var i in array){
// 		if (array[i].getAttribute('name').replace(/[0-9]/g, '') == name){
// 			return array[i];
// 		}
// 	}
// }