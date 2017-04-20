/**
 * Created by JN on 09/03/2017.
 */
class EndGroupBy{
    constructor(){
        this.group = "GROUP BY";
        this.variables = [];
    }

    add(variable){
        if(!this.detectedDuplicate(variable)){
            this.variables.push(variable);
            console.log(this.variables);
        }
    }

    adds(variables){
        for (var i = 0; i < variables.length; i++){
            this.add(variables[i]);
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

    get returnEndGroupBy(){
        var rdf = new RDF(this.group, this.variables.join().replace(/,/g, " "), null);
        console.log(rdf);
        var grp = [];
        grp.push(rdf);
        return grp;
    }
}