/**
 * Created by JN on 04/02/2017.
 */
class RDF{
    constructor(subject, predicate, object){
        this.Subject = subject;
        this.Predicate = predicate;
        this.Object = object;
    }

    returnRDF(){
        return this.Subject + " " + this.Predicate + " " + this.Object;
    }
}