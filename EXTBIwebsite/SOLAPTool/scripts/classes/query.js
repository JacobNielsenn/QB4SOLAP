/**
 * Created by JN on 14/02/2017.
 */
class Query{
    constructor(){
        this.opertorList = [];
        this.rdfList = new RDFHandler();
        this.select = new Select();
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

    get list(){
        return this.opertorList;
    }

    get mainOperator(){
        return this.opertorList[0];
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
        this.rdfList = new RDFHandler();
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
        return this.rdfList;
    }
}