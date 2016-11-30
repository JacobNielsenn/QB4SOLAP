/**
 * Created by Jacob on 17-11-2016.
 */
var test, test1, test2, test3, test4, test5, test6, test7;
var test1Obj, test2Obj, test3Obj, test4Obj, test5Obj, test6Obj, test7Obj, test8Obj;
function setupdebug(){
    test = document.getElementById('test');
    test1 = SpatialAggregation;
    test2 = TopologicalRelations;
    test3 = NumericOperations;
    test4 = DataTypes;
    test5 = RelationalOperators;
    test6 = AGG;
    test7 = ['employee', 'orderDate', 'dueDate', 'shippedDate', 'product', 'order', 'shipper', 'supplier', 'customer'];
    test1Obj = createMenuObj(test1, 'SpatialAggregation');
    test2Obj = createMenuObj(test2, 'TopologicalRelations');
    test3Obj = createMenuObj(test3, 'NumericOperations');
    test4Obj = createMenuObj(test4, 'DataTypes');
    test5Obj = createMenuObj(test5, 'RelationalOperators');
    test6Obj = createMenuObj(test6, 'AGG');
    test7Obj = createMenuObj(test7, 'Dimensions', true, structureLevel.Dimenasion);
    test8Obj = createMenuObj(test7, 'Dimensions', true, structureLevel.Dimenasion, spatialMode.On);
    test.appendChild(InsertSingleMenu(test1Obj, 150));
    test.appendChild(InsertBR());
    test.appendChild(InsertSingleMenu(test2Obj, 150));
    test.appendChild(InsertBR());
    test.appendChild(InsertSingleMenu(test3Obj, 150));
    test.appendChild(InsertBR());
    test.appendChild(InsertSingleMenu(test4Obj, 150));
    test.appendChild(InsertBR());
    test.appendChild(InsertSingleMenu(test5Obj, 150));
    test.appendChild(InsertBR());
    test.appendChild(InsertSingleMenu(test6Obj, 150));
    test.appendChild(InsertBR());
    test.appendChild(InsertSingleMenu(test7Obj, 150));
    test.appendChild(InsertBR());
    test.appendChild(InsertSingleMenu(test8Obj, 150));
    test.appendChild(InsertBR());
    $('#test').hide();
}
//Only used for debugging menus, to toggle them on and off.
function debugMenu(ele){
    if (ele.classList.contains('show')){
        $('#test').hide();
        ele.className = 'hide';
    }
    else {
        $('#test').show();
        ele.className = 'show';
    }
}