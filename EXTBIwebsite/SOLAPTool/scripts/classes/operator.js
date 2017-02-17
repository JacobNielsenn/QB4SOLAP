/**
 * Created by JN on 04/02/2017.
 */
class Operator{
    constructor(name, id){
        //Operator Information
        this.name = name;
        this.id = id;
        //Operator settings
        this.first = null;
        this.second = null;
        this.path = null;
        //return lists containing RDF lines.
        this.selRDF = new Select();
        this.spaRDF = new RDFHandler();
        this.mesRDF = new RDFHandler();
        this.attRDF = new RDFHandler();
    }

    get returnSelectRDF(){
        return this.selRDF.returnSelect;
    };
    get returnSpatialRDF(){

        return this.spaRDF.returnRDF();
    };
    get returnMeasuresRDF(){
        return this.mesRDF.returnRDF();
    };
    get returnAttributeRDF(){
        return this.attRDF.returnRDF();
    };
    get returnFilter(){
        return "FILTER NO YET READY\n";
    };
}

class OSlice extends Operator {
    get generateQuery(){};
}

class OSRU extends Operator {
    get generateQuery(){};
}

class ODice extends Operator {
    get generateQuery(){};
}