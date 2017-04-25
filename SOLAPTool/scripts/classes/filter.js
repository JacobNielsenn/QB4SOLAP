/**
 * Created by JN on 28/02/2017.
 */
class Filters{
    constructor(){
        this.filter = 'FILTER';
        this.useless1 = null;
    }

    set setFilter(string){
        this.filter = string;
    }

    get returnFilter(){
        var rdf = new RDF(this.filter, this.useless1, this.useless1);
        console.log(rdf);
        var grp = [];
        grp.push(rdf);
        return grp;
    }
}