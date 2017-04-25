/**
 * Created by JN on 07/03/2017.
 */
class Binds{
    constructor(){
        this.bind = 'BIND';
        this.useless1 = null;
    }

    set setBIND(string){
        this.bind = string;
    }

    get returnBIND(){
        var rdf = new RDF(this.bind, this.useless1, this.useless1);
        var grp = [];
        grp.push(rdf);
        return grp;
    }
}