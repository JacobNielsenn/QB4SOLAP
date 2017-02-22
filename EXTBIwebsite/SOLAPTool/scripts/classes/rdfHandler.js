/**
 * Created by JN on 14/02/2017.
 */
class RDFHandler{
    constructor(){
        this.rdfs = [];
    }


    remove(){

    }

    reset(){
        this.rdfs = [];
    }

    add(rdf){
        if (rdf.constructor == RDF){
            if (!this.detectDuplicate(rdf))
                this.rdfs.push(rdf);
        }
        else{
            alert('You need to add an rdf and not a string');
        }
    }

    detectDuplicate(rdf){
        for (var ele in this.rdfs){
            if (this.rdfs[ele].returnSubject == rdf.returnSubject){
                if (this.rdfs[ele].returnObject == rdf.returnObject){
                    if (this.rdfs[ele].returnPredicate == rdf.returnPredicate){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    returnRDFQuery(){
        var text = "";
        for (var i in this.rdfs){
            text += this.rdfs[i].returnRDF() + " .\n";
        }
        return text;
    }

    returnRDF(){
        return this.rdfs;
    }
}