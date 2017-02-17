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
        this.variables.push(variable);
    }

    remove(variable){
        for (var i in this.variables){
            if (this.variables[i] == variable){
                this.variables[i].remove(i);
            }
        }
    }

    get returnSelect(){
        return this.select + " " + this.variables.join().replace(",", " ") + " " + this.where;
    }
}