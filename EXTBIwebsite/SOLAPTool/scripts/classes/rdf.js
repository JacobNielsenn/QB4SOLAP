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
        if (this.Subject == null){
            return "";
        }
        return this.Subject;
    }
    get returnPredicate(){
        if (this.Predicate == null){
            return "";
        }
        return this.Predicate;
    }
    get returnObject(){
        if (this.Object == null){
            return "";
        }
        return this.Object;
    }
    returnRDF(){
        if (this.Predicate == null && this.Object == null){
            return this.Subject;
        }
        else if (this.Object == null){
            return this.Subject + " " + this.Predicate;
        }
        else{
            return this.Subject + " " + this.Predicate + " " + this.Object;
        }
    }
}