/**
 * Created by JN on 14/02/2017.
 */
class Select{
    constructor(){
        this.select = "SELECT";
        this.variables = [];
        this.where = "WHERE";
    }

    add(variable){
        if(!this.detectedDuplicate(variable)){
            this.variables.push(variable);
        }
    }

    detectedDuplicate(variable){
        for (var i in this.variables){
            if(this.variables[i] == variable){
                return true;
            }
        }
        return false;
    }

    remove(variable){
        for (var i in this.variables){
            if (this.variables[i] == variable){
                this.variables[i].remove(i);
            }
        }
    }

    get returnSelect(){
        var rdf = new RDF(this.select, this.variables.join().replace(",", " "), this.where);
        console.log(rdf);
        var grp = [];
        grp.push(rdf);
        return grp;
    }
}