/**
 * Created by JN on 04/02/2017.
 */
class RDF{
    constructor(subject, predicate, object){
        this.Subject = subject;
        this.Predicate = predicate;
        this.Object = object;
    }

    get returnSubject(){
        return this.Subject;
    }
    get returnPredicate(){
        return this.Predicate;
    }
    get returnObject(){
        return this.Object;
    }
    returnRDF(){
        return this.Subject + " " + this.Predicate + " " + this.Object;
    }
}