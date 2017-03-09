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
        this.levelsGroupByPath = null;
        this.spatialOperator = null;
        this.userInput = null;
        //return lists containing RDF lines.
        this.selRDF = new Select();
        this.innerSelRDF = new Select();
        this.spaRDF = new RDFHandler();
        this.innerSpaRDF = new RDFHandler();
        this.mesRDF = new RDFHandler();
        this.attRDF = new RDFHandler();
        this.innerAttRDF = new RDFHandler();
        this.groupBYRDF = new RDFHandler();
        this.filters = new Filters();
        this.bind = new Binds();
        this.groupBy = new GroupBy();
        this.aggregationLevel = null;
        this.endGroupBy = new EndGroupBy();
    }

    set setPath1(pathname){
        this.path1 = new Path(pathname);
        var tmp = new Levels(pathname, false);
        this.levels1 = tmp.returnLevels;
    };

    set setPath2(pathname){
        this.path2 = new Path(pathname);
        var tmp = new Levels(pathname, false);
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

    set setAggregationLevel(string){
        this.aggregationLevel = new Path("blank,"+string);
        var tmp = new Levels("blank,"+string, true);
        this.levelsGroupByPath = tmp.returnLevels;
    }

    generate(){
        this.selRDF = new Select();
        this.spaRDF = new RDFHandler();
        this.mesRDF = new RDFHandler();
        this.attRDF = new RDFHandler();
        this.filter = new Filters();
        this.generatePath(1);
        this.generatePath(2);
        this.generateGroupByPath();
        this.generateInnerPath(1);
        this.generateInnerPath(2);
        this.generateAttri(1);
        this.generateAttri(2);
        this.generateInnerAttri(1);
        this.generateInnerAttri(2);
    }

    generatePath(pathNumber){
        if (this['levels'+pathNumber] == null){

        }
        else{
            if (this.measure == null){
                this.spaRDF.add(new RDF(name("?obs", global), 'rdf:type', 'qb:Observation' ));
                this.selRDF.add(name("?obs", global));
            }
            else{
                this.spaRDF.add(new RDF(name("?obs", global), 'rdf:type', 'qb:Observation' ));
                this.spaRDF.add(new RDF(name("?obs", global), 'gnw:'+this.measure, '?'+name(this.measure, global)));
                this.selRDF.add(name("?obs", global));
                this.selRDF.add("(" + this.setAgg + "(?" + name(this.measure, global) + ") AS ?" + name("total"+this.measure, global) + ")");

            }
            this.spaRDF.add(new RDF(
                name("?obs", global),
                DataStructureDefinitionName + this['levels'+pathNumber][0] + 'ID',
                name('?'+this['levels'+pathNumber][0], globalPath)));
            for (var i = 0; i < this['levels'+pathNumber].length; i++){
                if (i != this['levels'+pathNumber].length && i != 0 && this['levels'+pathNumber].length > 2){
                    this.spaRDF.add(new RDF(
                        name('?'+this['levels'+pathNumber][i-1], globalPath),
                        "skos:broader",
                        name('?'+this['levels'+pathNumber][i], globalPath)));
                }
                this.spaRDF.add(new RDF(
                    name('?'+this['levels'+pathNumber][i], globalPath),
                    "qb4o:memberOf",
                    "gnw:"+this['levels'+pathNumber][i]));

            }
        }
    }

    generateGroupByPath(){
        var specialName;
        if (this.levelsGroupByPath == null){
        }
        else{
            for (var i = 0; i < this.levelsGroupByPath.length; i++){
                if (i != this.levelsGroupByPath.length && i != 0 && this.levelsGroupByPath.length > 1){
                    if (i == this.levelsGroupByPath.length-1){
                        specialName = name('?'+this.aggregationLevel.startLevel+this.levelsGroupByPath[i], globalPath);
                        this.spaRDF.add(new RDF(
                            name('?'+this.levelsGroupByPath[i-1], globalPath),
                            "skos:broader",
                            specialName));
                    }
                    else{
                        this.spaRDF.add(new RDF(
                            name('?'+this.levelsGroupByPath[i-1], globalPath),
                            "skos:broader",
                            name('?'+this.levelsGroupByPath[i], globalPath)));
                    }

                }
                if (i == this.levelsGroupByPath.length-1){
                    this.spaRDF.add(new RDF(
                        specialName,
                        "qb4o:memberOf",
                        "gnw:"+this.levelsGroupByPath[i]));
                }
                else{
                    this.spaRDF.add(new RDF(
                        name('?'+this.levelsGroupByPath[i], globalPath),
                        "qb4o:memberOf",
                        "gnw:"+this.levelsGroupByPath[i]));
                }
                if (i == this.levelsGroupByPath.length-1){
                    this.selRDF.add(specialName);
                    this.endGroupBy.adds(this.selRDF.returnVariables);
                }

            }
        }
    }

    generateInnerPath(pathNumber){
        if (this['levels'+pathNumber] == null){

        }
        else{
            this.innerSpaRDF.add(new RDF(name("?obs", innerGlobal), 'rdf:type', 'qb:Observation' ));
            if (this.measure != null && this.setAggFunction != null && this.setSpatialFunction != null){
                this.innerSelRDF.add(name("?"+this.levels1[0], innerGLobalPath));
                //(MIN(?distance1) AS ?MINdistance1)
                this.innerSelRDF.add("("+this.setAggFunction+"(?"+name(this.setSpatialFunction.split("_")[1], innerGlobal)+") AS ?"+name(this.setAggFunction+this.setSpatialFunction.split("_")[1], innerGlobal)+")");
            }
            this.innerSpaRDF.add(new RDF(
                name("?obs", innerGlobal),
                DataStructureDefinitionName + this['levels'+pathNumber][0] + 'ID',
                name('?'+this['levels'+pathNumber][0], innerGLobalPath)));
            for (var i = 0; i < this['levels'+pathNumber].length; i++){
                if (i != this['levels'+pathNumber].length && i != 0 && this['levels'+pathNumber].length > 2){
                    this.innerSpaRDF.add(new RDF(
                        name('?'+this['levels'+pathNumber][i-1], innerGLobalPath),
                        "skos:broader",
                        name('?'+this['levels'+pathNumber][i], innerGLobalPath)));
                }
                this.innerSpaRDF.add(new RDF(
                    name('?'+this['levels'+pathNumber][i], innerGLobalPath),
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
    generateInnerAttri(pathNumber){
        if (this['path'+pathNumber] == null){

        }
        else {
            this.innerAttRDF.add(new RDF(
                name('?' + this['path' + pathNumber].returnEndLevel(), innerGLobalPath),
                "gnw:" + this['path' + pathNumber].returnAttribute(),
                name('?' + this['path' + pathNumber].returnAttribute(), innerGLobalPath)));
        }
    }
    get returnSelectRDF(){
        this.generate();
        return this.selRDF.returnSelect;
    };

    get returnInnerSelectRDF(){
        this.generate();
        return this.innerSelRDF.returnSelect;
    };

    get returnSpatialRDF(){
        this.generate();
        var tmp = jQuery.extend(true, {}, this.spaRDF.returnRDF());
        //this.spaRDF.reset();
        return tmp;
    };

    get returnInnerSpatialRDF(){
        this.generate();
        var tmp = jQuery.extend(true, {}, this.innerSpaRDF.returnRDF());
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

    get returnInnerAttributeRDF(){
        this.generate();
        return this.innerAttRDF.returnRDF();
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

    get returnBIND(){
        //BIND (bif:st_distance( ?supplierGeo2, ?customerGeo2 ) AS ?distance1)
        this.bind.setBIND = 'BIND (bif:'+this.setSpatialFunction+"("+name('?'+this.path1.returnAttribute(), innerGLobalPath)+', '+name('?'+this.path2.returnAttribute(), innerGLobalPath)+') AS ?'+name(this.setSpatialFunction.split("_")[1], innerGlobal)+')';
        return this.bind.returnBIND;
    }

    get returnGroupBy(){
        //GROUP BY ?supplier2
        this.bind.setBIND = 'GROUP BY '+name("?"+this.levels1[0], innerGLobalPath);
        return this.bind.returnBIND;
    }

    get returnFilter(){
        //FILTER (?supplier1 = ?supplier2 && bif:st_distance( ?supplierGeo1, ?customerGeo1 ) = ?MINdistance1 )
        this.filters.setFilter = 'FILTER ('+name("?"+this.levels1[0], globalPath)+' = '+name("?"+this.levels1[0], innerGLobalPath)+' && bif:'+this.setSpatialFunction+'('+name('?'+this.path1.returnAttribute(), globalPath)+', '+name('?'+this.path2.returnAttribute(), globalPath)+') = ?'+name(this.setAggFunction+this.setSpatialFunction.split("_")[1], innerGlobal)+')';
        return this.filters.returnFilter;
    }
}


class ODice extends Operator {
    get returnFilter(){
        this.filters.setFilter = 'FILTER (bif:st_within(?' + name(this.path1.returnAttribute(), global)
            + ', ?' + name(this.path2.returnAttribute(), global) + ', ' + this.distance + '))';
        return this.filters.returnFilter;
    };
}