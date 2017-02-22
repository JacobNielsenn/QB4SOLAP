/**
 * Created by JN on 04/02/2017.
 */
class Operator{
    constructor(name, id){
        //Operator Information
        this.name = name;
        this.id = id;
        //Operator settings
        this.measure = null;
        this.first = null;
        this.second = null;
        this.path1 = null;
        this.path2 = null;
        this.pathNames = {};
        this.distance = null;
        this.levels1 = null;
        this.levels2 = null;
        this.spatialOperator = null;
        this.userInput = null;
        //return lists containing RDF lines.
        this.selRDF = new Select();
        this.spaRDF = new RDFHandler();
        this.mesRDF = new RDFHandler();
        this.attRDF = new RDFHandler();
    }

    set setPath1(pathname){
        this.path1 = new Path(pathname);
        var tmp = new Levels(pathname);
        this.levels1 = tmp.returnLevels;
    };

    set setPath2(pathname){
        this.path2 = new Path(pathname);
        var tmp = new Levels(pathname);
        this.levels2 = tmp.returnLevels;
    };

    generatePath(pathNumber){
        var unique = false;
        if (pathNumber == 2){
            unique = true;
        }
        if (this.measure == null){
            this.spaRDF.add(new RDF(name("?obs", global), 'rdf:type', 'qb:Observation' ));
        }
        else{
            this.spaRDF.add(new RDF(name("?obs", object, unique), DataStructureDefinitionName + measure, '?'+measurevariable));
        }
        this.spaRDF.add(new RDF(
            name("?obs", global),
            DataStructureDefinitionName + this['levels'+pathNumber][0] + 'ID',
            name('?'+this['levels'+pathNumber][0], globalPath, unique)));
        for (var i = 0; i < this['levels'+pathNumber].length; i++){
            if (i != this['levels'+pathNumber].length && i != 0 && this['levels'+pathNumber].length > 2){
                this.spaRDF.add(new RDF(
                    name('?'+this['levels'+pathNumber][i-1], globalPath, unique),
                    "skos:broader",
                    name('?'+this['levels'+pathNumber][i], globalPath, unique)));
            }
            this.spaRDF.add(new RDF(
                name('?'+this['levels'+pathNumber][i], globalPath, unique),
                "qb4o:memberOf",
                "gnw:"+this['levels'+pathNumber][i]));

        }
        console.log(this.pathNames1)
    }

    generateAttri(pathNumber){
        console.log(this.path1);
        this.attRDF.add(new RDF(
            name('?'+this['path'+pathNumber].returnEndLevel(), globalPath),
            "gnw:"+this['path'+pathNumber].returnAttribute(),
            name('?'+this['path'+pathNumber].returnAttribute(), globalPath)));
        console.log(this.pathNames1)
    }

    get returnSelectRDF(){
        return this.selRDF.returnSelect;
    };
    get returnSpatialRDF(){
        var tmp = jQuery.extend(true, {}, this.spaRDF.returnRDF());
        //this.spaRDF.reset();
        return tmp;
    };
    get returnMeasuresRDF(){
        return this.mesRDF.returnRDF();
    };
    get returnAttributeRDF(){
        return this.attRDF.returnRDF();
    };
    get returnFilter(){
        return "FILTER NO YET READY\n";
    };
}

class OSlice extends Operator {
    get generateQuery(){};
}

class OSRU extends Operator {
    get generateQuery(){};
}

class ODice extends Operator {
    get generateQuery(){};
}