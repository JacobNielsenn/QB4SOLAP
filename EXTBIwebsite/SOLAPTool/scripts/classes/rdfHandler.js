/**
 * Created by JN on 14/02/2017.
 */
class RDFHandler{
    constructor(){
        this.rdfs = [];
    }
    add(rdf){
        if (rdf.constructor == RDF){
            this.rdfs.push(rdf);
        }
    }

    remove(){

    }

    returnRDFQuery(){
        var text = "";
        for (var i in this.rdfs){
            text += this.rdfs[i].returnRDF();
        }
        return text;
    }

    returnRDF(){
        return this.rdfs;
    }
}