/**
 * Created by JN on 14/02/2017.
 */
class Query{
    constructor(){
        this.opertorList = [];
        this.rdfList = new RDFHandler();
        this.select = new Select();
        this.aggregate = false;
    }

    add(operator){
        if (operator.constructor == ODice ||
            operator.constructor == OSlice ||
            operator.constructor == OSRU){
            this.opertorList.push(operator);
        }
        else{
            alert("Query could not add constructor to list, see log for more info");
            console.log("Add operator to Query list failed.", operator);
        }
    }

    findHighestLevel(){
        console.log(this.rdfList.returnRDF().length);
        var firstSelect = false;
        var level;
        var index;
        for (var i = 0; i < this.rdfList.returnRDF().length; i++){
            if (this.rdfList.returnRDF()[i].returnSubject == "SELECT"){
                firstSelect = !firstSelect;
            }
            if (firstSelect == true && (this.rdfList.returnRDF()[i].returnPredicate).indexOf('gnw:') !== -1 ){
                level = this.rdfList.returnRDF()[i];
                index = i;
            }
        }
        return {level:level, index:index};
    }

    get list(){
        return this.opertorList;
    }

    get mainOperator(){
        return this.opertorList[0];
    }

    swapOperator(ele1, ele2){
        for (var i in this.opertorList){
            for (var j in this.opertorList) {
                if (this.opertorList[i].id == ele1.id && this.opertorList[j].id == ele2.id) {
                    this.opertorList[j] = this.opertorList.splice(i, 1, this.opertorList[j])[0];
                    return null;
                }
            }
        }
    }

    deleteOperator(operatorID){
        for (var i = 0; i < this.opertorList.length; i++){
            if (this.opertorList[i].id == operatorID){
                this.opertorList.splice(i,1);
            }
        }
    }

    get check(){
        //Tell the user if the nesting is incorrect.
        var countInList = 2;
        if (this.opertorList.length = 0){ //No operator in list.
            return false;
        }
        else if (this.opertorList.length = 1){ //Only one operator in list.
            return true;
        }
        while (countInList != this.opertorList.length){
            switch (this.opertorList[countInList].constructor){
                case(ODice):
                    if (this.opertorList[countInList-1].constructor == ODice){
                        return false;
                    }
                    break;
                case(OSlice):
                    break;
                case(OSRU):
                    break;
                default:
                    return false;
            }
            countInList++;
        }
        return true;
    }

    merge(opr1, opr2){
        var spa1 = opr1;
        var spa2 = opr2;
        for (var opr in spa1){
            this.rdfList.add(spa1[opr]);
        }
        for (var opr in spa2){
            this.rdfList.add(spa2[opr]);
        }
    }

    addToList(rdfList){
        var list = rdfList;
        for (var i in list){
            this.rdfList.add(list[i]);
        }
    }

    get returnQuery(){
        var SRUCase = [];
        this.select = new Select();
        this.rdfList = new RDFHandler();
        if (this.opertorList.length == 0){
            return this.rdfList;
        }
        for (var i = 0; i < this.opertorList.length; i++){
            this.select.adds(this.opertorList[i].selRDF.returnVariables);
        }
        this.addToList(this.select.returnSelect);
        if (this.opertorList.length == 1){
            this.addToList(this.opertorList[0].returnSpatialRDF);
            this.addToList(this.opertorList[0].returnAttributeRDF);
            if (this.opertorList[0].constructor == OSRU){
                this.addToList([new RDF('{', null, null)]);
                this.addToList(this.opertorList[0].returnInnerSelectRDF);
                this.addToList(this.opertorList[0].returnInnerSpatialRDF);
                this.addToList(this.opertorList[0].returnInnerAttributeRDF);
                this.addToList(this.opertorList[0].returnBIND);
                this.addToList([new RDF('}', null, null)]);
                this.addToList(this.opertorList[0].returnGroupBy);
                this.addToList([new RDF('} ', null, null)]);
            }
            this.addToList(this.opertorList[0].returnFilter);
            this.addToList([new RDF('}  ', null, null)]);
            if (this.opertorList[0].constructor == OSRU) {
                this.addToList(this.opertorList[0].endGroupBy.returnEndGroupBy);
            }
        }
        else{

            for (var i = 1; i < this.opertorList.length; i++){
                this.merge(this.opertorList[i-1].returnSpatialRDF, this.opertorList[i].returnSpatialRDF);
            }
            for (var i = 1; i < this.opertorList.length; i++){
                this.merge(this.opertorList[i-1].returnAttributeRDF, this.opertorList[i].returnAttributeRDF);
            }
            for (var i = 0; i < this.opertorList.length; i++){
                console.log("Checking Operator Type of index:", i, "Type is:", this.opertorList[i].constructor);
                if (this.opertorList[i].constructor == OSRU){
                    console.log("I Pushed to SRUCase!!");
                    SRUCase.push(i);
                }
            }
            if (SRUCase.length > 0){
                this.addToList([new RDF('{', null, null)]);
                if (SRUCase.length > 1){
                    for (var i = 1; i < this.SRUCase.length; i++){
                        this.merge(this.opertorList[SRUCase[i-1]].returnInnerSpatialRDF, this.opertorList[SRUCase[i]].returnInnerSpatialRDF);
                    }
                }
                else {
                    console.log(this.opertorList[SRUCase[0]], SRUCase[0]);
                    this.addToList([new RDF('{', null, null)]);
                    this.addToList(this.opertorList[SRUCase[0]].returnInnerSelectRDF);
                    this.addToList(this.opertorList[SRUCase[0]].returnInnerSpatialRDF);
                    this.addToList(this.opertorList[SRUCase[0]].returnInnerAttributeRDF);
                    this.addToList(this.opertorList[SRUCase[0]].returnBIND);
                    this.addToList([new RDF('}', null, null)]);
                    this.addToList(this.opertorList[SRUCase[0]].returnGroupBy);
                    this.addToList([new RDF('} ', null, null)]);
                }
            }
            for (var i = 1; i < this.opertorList.length; i++) {
                this.merge(this.opertorList[i - 1].returnFilter, this.opertorList[i].returnFilter);
            }
            this.addToList([new RDF('}        ', null, null)]);
            if (SRUCase.length > 0){
                console.log("SRUCase:", SRUCase.length);
                for (var i = 0; i < SRUCase.length; i++){
                    this.addToList(this.opertorList[SRUCase[i]].endGroupBy.returnEndGroupBy);
                }
            }
        }
        if (this.aggregate == true){
            //?countryName
            //(SUM(?sales2) AS ?totalSales2 )
            //(SUM(?quantity2) AS ?totalQuantity2)
            //(AVG(?discount2) AS ?averageDiscount2)
            //(AVG(?unitPrice2) AS ?averageUnitPrice2)
            //(SUM(?freight2) AS ?totalFreight2)
            this.select.add(this.findHighestLevel().level.returnSubject.replace(/[0-9]/g,'')+'Name');
            this.select.add('(SUM(?sales1) AS ?totalSales1 )');
            this.select.add('(SUM(?quantity1) AS ?totalQuantity1)');
            this.select.add('(AVG(?discount1) AS ?averageDiscount1)');
            this.select.add('(AVG(?unitPrice1) AS ?averageUnitPrice1)');
            this.select.add('(SUM(?freight1) AS ?totalFreight1)');
            this.select.remove(name('?obs',global));
            this.rdfList.replaceAtIndex(0, this.select.returnSelect[0]);
            var tmp = this.findHighestLevel().level;
            var index = this.findHighestLevel().index;
            //console.log(tmp.returnSubject, 'gnw:'+tmp.returnSubject.replace(/[0-9]/g,'').replace(/\?/g,'')+'Name', tmp.returnSubject.replace(/[0-9]/g,'')+'Name');
            this.rdfList.addAtIndex(index, new RDF(tmp.returnSubject, 'gnw:'+tmp.returnSubject.replace(/[0-9]/g,'').replace(/\?/g,'')+'Name', tmp.returnSubject.replace(/[0-9]/g,'')+'Name'));
            this.rdfList.addAtIndex(index, new RDF('?obs1', 'gnw:salesAmount', '?sales1'));
            this.rdfList.addAtIndex(index, new RDF('?obs1', 'gnw:quantity', '?quantity1'));
            this.rdfList.addAtIndex(index, new RDF('?obs1', 'gnw:discount', '?discount1'));
            this.rdfList.addAtIndex(index, new RDF('?obs1', 'gnw:unitPrice', '?unitPrice1'));
            this.rdfList.addAtIndex(index, new RDF('?obs1', 'gnw:freight', '?freight1'));
            //?obs1 gnw:salesAmount ?sales2 .
            //?obs1 gnw:quantity ?quantity2 .
            //?obs1 gnw:discount ?discount2 .
            //?obs1 gnw:unitPrice ?unitPrice2 .
            //?obs1 gnw:freight ?freight2 .

        }
        return this.rdfList;
    }
}