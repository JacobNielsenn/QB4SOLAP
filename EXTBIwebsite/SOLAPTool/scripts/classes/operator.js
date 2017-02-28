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
        this.filters = new Filters();
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

    set setFirst(value){
        this.first = value;
    }

    set setSecond(value){
        this.second = value;
    }

    set setDistance(value){
        this.distance = value;
    }

    generate(){
        this.selRDF = new Select();
        this.spaRDF = new RDFHandler();
        this.mesRDF = new RDFHandler();
        this.attRDF = new RDFHandler();
        this.filter = new Filters();
        this.generatePath(1);
        this.generatePath(2);
        this.generateAttri(1);
        this.generateAttri(2);
    }

    generatePath(pathNumber){
        if (this['levels'+pathNumber] == null){

        }
        else{
            var unique = false;
            if (pathNumber == 2){
                unique = false; // true
            }
            if (this.measure == null){
                this.spaRDF.add(new RDF(name("?obs", global), 'rdf:type', 'qb:Observation' ));
                this.selRDF.add(name("?obs", global));
            }
            else{
                this.spaRDF.add(new RDF(name("?obs", global), DataStructureDefinitionName + this.measure, '?'));
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
        }
    }

    generateAttri(pathNumber){
        if (this['path'+pathNumber] == null){

        }
        else {
            this.attRDF.add(new RDF(
                name('?' + this['path' + pathNumber].returnEndLevel(), globalPath),
                "gnw:" + this['path' + pathNumber].returnAttribute(),
                name('?' + this['path' + pathNumber].returnAttribute(), globalPath)));
        }
    }
    get returnSelectRDF(){
        return this.selRDF.returnSelect;
    };
    get returnSpatialRDF(){
        this.generate();
        var tmp = jQuery.extend(true, {}, this.spaRDF.returnRDF());
        //this.spaRDF.reset();
        return tmp;
    };
    get returnMeasuresRDF(){
        return this.mesRDF.returnRDF();
    };
    get returnAttributeRDF(){
        this.generate();
        return this.attRDF.returnRDF();
    };
}

class OSlice extends Operator {
    get returnFilter(){
        if (this.first.indexOf(',') != -1){
            console.log('case 1');
            //FILTER (bif:st_within(bif:st_point(10.079956054687502, 51.06211251399775), ?countryGeo1 ,32))
            this.filters.setFilter = 'FILTER (bif:st_within(bif:st_point(' + this.first + '), ?' + name(this.path1.returnAttribute(), global) + ' ,' + this.distance +'))';
        }
        else{
            console.log('case 2');
            //FILTER (bif:st_within(?countryGeo1, bif:st_point(10.079956054687502, 51.06211251399775) ,32))
            this.filters.setFilter = 'FILTER (bif:st_within(?' + name(this.path1.returnAttribute(), global) + ' , bif:st_point(' + this.first + ') ,' + this.distance +'))';
        }
        return this.filters.returnFilter;
    }
}

class OSRU extends Operator {

    set setMeasure(value){
        this.measure = value;
    }
}


class ODice extends Operator {
    get returnFilter(){
        this.filters.setFilter = 'FILTER (bif:st_within(?' + name(this.path1.returnAttribute(), global)
            + ', ?' + name(this.path2.returnAttribute(), global) + ', ' + this.distance + '))';
        return this.filters.returnFilter;
    };
}