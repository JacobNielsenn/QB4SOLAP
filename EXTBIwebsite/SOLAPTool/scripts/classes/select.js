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
            console.log(this.variables);
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

    get returnVariables(){
        var tmp = [];
        for (var i = 0; i < this.variables.length; i++){
            if (this.variables[i][0] == '('){}
            else {
                tmp.push(this.variables[i]);
            }
        }
        return tmp;
    }

    get returnSelect(){
        var rdf = new RDF(this.select, this.variables.join().replace(/,/g, " "), this.where);
        console.log(rdf);
        var grp = [];
        grp.push(rdf);
        return grp;
    }
}