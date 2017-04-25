/**
 * Created by JN on 07/03/2017.
 */
class GroupBy{
    constructor(){
        this.groupBy = 'BIND';
        this.useless1 = null;
    }

    set setGroupBy(string){
        this.groupBy = string;
    }

    get returnGroupBy(){
        var rdf = new RDF(this.groupBy, this.useless1, this.useless1);
        var grp = [];
        grp.push(rdf);
        return grp;
    }
}