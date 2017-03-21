var urls = ["schemas/gnw_qb4solap_schema.txt", "schemas/geodyrDW_qb4solap_schema.txt"];
var dataEntry = [];
var objs = [];
var DataStructureDefinition = {levelProperty:[], measure:[], dimension:[]};
var DataStructureDefinitionName;
var prefixes = "";
var GlobalObject1 = {levelProperty:[], measure:[], dimension:[]};
var GlobalObject2 = {levelProperty:[], measure:[], dimension:[]};
//Ensure correct load order.



xhrDoc= new XMLHttpRequest();
xhrDoc.open('GET', urls[0])
if (xhrDoc.overrideMimeType)
    xhrDoc.overrideMimeType('text/plain; charset=x-user-defined')
xhrDoc.onreadystatechange =function()
{
    if (this.readyState == 4)
    {
        if (this.status == 200)
        {
            dataEntry.push(this.response) //Here is a string of the text data
            xxhrDoc= new XMLHttpRequest();
            xxhrDoc.open('GET', urls[1])
            if (xxhrDoc.overrideMimeType)
                xxhrDoc.overrideMimeType('text/plain; charset=x-user-defined')
            xxhrDoc.onreadystatechange =function()
            {
                if (this.readyState == 4)
                {
                    if (this.status == 200)
                    {
                        dataEntry.push(this.response) //Here is a string of the text data
                        Initialize();
                    }

                }
            }
            xxhrDoc.send() //sending the request
        }

    }
}
xhrDoc.send() //sending the request

function isEmpty(str){
  return (str.length === 0 || !str.trim());
}

function convertFileIntoLines(data){
  data = data.replace('\t','');
  data = data.replace(/ +(?= )/g,'');
  data = data.trim();
  return lines = data.split("\n");
}

function RemoveComments(lines){
  var newLines = [];
  var status;
  for(var i in lines){
    switch (status){
      case 'inline':
        if (!(lines[i].includes("@prefix") || lines[i].includes("#") || isEmpty(lines[i]))){
          newLines[newLines.length-1] += lines[i];
        }
        if (lines[i].includes('.')){
          status = "";
        }
        break;
      default:
        if (!(lines[i].includes("@prefix") || lines[i].includes("#") || isEmpty(lines[i]))){
          newLines.push(lines[i]);
          if (!lines[i].includes('.')){
            status = "inline";
          }
        }
        if(lines[i].includes("@prefix")){
          var modify = lines[i].replace("@", "").replace(" .", "");
          prefixes += modify + "\n";
        }
    }
  }  
  for (var i in newLines){
    //console.log(i, newLines[i]);
  }
  return newLines;
}

function traverseSet(o, target){
  objs = [];
  return searhFor(o, target);
}

function searhFor(o, target){
  //console.log('Looking for:', target);
  for (var i in o) {
    //console.log(i, o[i])
    if (o[i] == target){
      //console.log('Fount one', target);
      objs.push(o);
    }
    if (o[i] !== null && typeof(o[i])=="object") {
      obj = searhFor(o[i], target);
      if (obj != null){
        //objs.push(obj);
      }
    }
  }
  return objs;
}


//console.log("%c" + lines[i], 'background: #222; color: #bada55');
function convertDataToObjects1(obj ,index) {
    var rdf = convertFileIntoLines(dataEntry[index]);
    rdf = RemoveComments(rdf);
    var filterDataStructure = rdf.filter(function (string) {
          if (string.includes('qb:DataStructureDefinition')){
            return string;
          }});
    filterDataStructure = filterDataStructure[0].split(']');
    var filterLevelAttribute = rdf.filter(function (string) {
        if (string.includes('qb4o:LevelAttribute')){
            return string;
        }});
    var filterMeasureProperty = rdf.filter(function (string) {
        if (string.includes('rdf:Property') && string.includes('qb:MeasureProperty')){
            return string;
        }});
    var filterDimensionProperty = rdf.filter(function (string) {
        if (string.includes('rdf:Property') && string.includes('qb:DimensionProperty')){
            return string;
        }});
    var filterLevelProperty = rdf.filter(function (string) {
        if (string.includes('qb4o:LevelProperty')){
            return string;
        }});
    var filterHierarchyStep = rdf.filter(function (string) {
        if (string.includes('qb4o:HierarchyStep')){
            return string;
        }});
    var filterHierarchy = rdf.filter(function (string) {
        if (string.includes('qb4o:Hierarchy ')){
            return string;
        }});
    var filterGeometry = rdf.filter(function (string) {
        if (string.includes('geo:Geometry') && !string.includes('qb4o:LevelAttribute')){
            return string;
        }});

    console.log('filterDataStructure', filterDataStructure);
    console.log('filterGeometry', filterGeometry);
    console.log('filterLevelAttribute', filterLevelAttribute);
    console.log('filterMeasureProperty', filterMeasureProperty);
    console.log('filterDimensionProperty', filterDimensionProperty);
    console.log('filterLevelProperty', filterLevelProperty);
    console.log('filterHierarchyStep', filterHierarchyStep);
    console.log('filterHierarchy', filterHierarchy);
    console.log(rdf);

    //Fill Dimensions with names and template properties
    for (var i in filterDimensionProperty){
        obj.dimension.push({name: filterDimensionProperty[i].split('Dim')[0].split(':')[1], cardinality:'', dimensionProperty:{hasHierarchy: [], label: '', name: ''}});
    }
    //Fill Dimensions with cardinality
    for (var i in filterDataStructure){
        for (var l in obj.dimension){
            if (filterDataStructure[i].includes(obj.dimension[l].name)){
                var tmp = filterDataStructure[i].split('cardinality')[1].split(':')[1].trim();
                if (tmp.includes(';')){
                    tmp = tmp.split(';')[0];
                }
                obj.dimension[l].cardinality = tmp;
            }
        }
    }
    //Fill Dimensions with dimensionProperty and templates
    for (var i in filterDimensionProperty){
        for( var l in obj.dimension){
            if (filterDimensionProperty[i].includes(obj.dimension[l].name)){
                obj.dimension[l].dimensionProperty.name = filterDimensionProperty[i].split(':')[1].split(' a ')[0];
                obj.dimension[l].dimensionProperty.label = filterDimensionProperty[i].split('"')[1].split('"')[0];
                if (filterDimensionProperty[i].includes('hasHierarchy')){
                    if(filterDimensionProperty[i].split('hasHierarchy')[1].includes(',')){
                        for (var o in filterDimensionProperty[i].split('hasHierarchy')[1].split(',')){
                            obj.dimension[l].dimensionProperty.hasHierarchy.push({hasHierarchy: filterDimensionProperty[i].split('hasHierarchy')[1].split(' ,')[o].split(' .')[0].split(':')[1], hierarchy:{hasLevel: [], hierarchy:'', inDimension:'',label:''},hierarchyStep:[]})
                        }
                    }
                    else{
                        if(filterDimensionProperty[i].split('hasHierarchy')[1].includes(';')){
                            obj.dimension[l].dimensionProperty.hasHierarchy.push({hasHierarchy: filterDimensionProperty[i].split('hasHierarchy')[1].split(' ;')[0].split(':')[1], hierarchy:{hasLevel: [], hierarchy:'', inDimension:'',label:''},hierarchyStep:[]})
                        }
                        else{
                            obj.dimension[l].dimensionProperty.hasHierarchy.push({hasHierarchy: filterDimensionProperty[i].split('hasHierarchy')[1].split(' .')[0].split(':')[1], hierarchy:{hasLevel: [], hierarchy:'', inDimension:'',label:''},hierarchyStep:[]})
                        }

                    }

                }
            }
        }
    }
    //Fill Measure with label, measure, range.
    for (var i in filterMeasureProperty){

        var Llabel = filterMeasureProperty[i].split('"')[1];
        var Lmeasure = filterMeasureProperty[i].split(' a ')[0].split(':')[1];
        var Lrange;
        if (filterMeasureProperty[i].includes('xsd:')){
            Lrange = filterMeasureProperty[i].split('xsd:')[1].split(' .')[0];
        }
        else{
            Lrange = filterMeasureProperty[i].split('range')[1].split(' .')[0];
        }
        obj.measure.push({aggregateFunction:'', label: Llabel, measure: Lmeasure, range: Lrange});
    }
    //Fill Measure with aggregateFunction
    for (var i in filterDataStructure){
        if (filterDataStructure[i].includes('qb:measure')){
            for (var l in obj.measure){
                if (filterDataStructure[i].includes(obj.measure[l].measure)){
                    obj.measure[l].aggregateFunction = filterDataStructure[i].split('Function')[1].split(':')[1];
                }
            }
        }
    }
    //Fill Dimensions with hierarchy
    for (var i in filterHierarchy){
        for (var l in obj.dimension){
            for (var o in obj.dimension[l].dimensionProperty.hasHierarchy){
                if(filterHierarchy[i].includes(obj.dimension[l].dimensionProperty.hasHierarchy[o].hasHierarchy)){
                    obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.hierarchy = filterHierarchy[i].split(' a ')[0].split(':')[1];
                    obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.label = filterHierarchy[i].split('"')[1];
                    obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.inDimension = filterHierarchy[i].split('inDimension')[1].split(':')[1].split(' ;')[0];
                    for (var ii in filterHierarchy[i].split('hasLevel').splice(1)){
                        if (filterHierarchy[i].split('hasLevel').splice(1)[ii].includes(';')){
                            if (filterHierarchy[i].split('hasLevel').splice(1)[ii].includes('qb4o')){
                                obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.hasLevel.push(filterHierarchy[i].split('hasLevel').splice(1)[ii].split(';').slice(0, 1)[0].split(':')[1].replace(' ',''));
                                //console.log('hasLevel' ,filterHierarchy[i].split('hasLevel').splice(1)[ii].split(';').slice(0, 1)[0].split(':')[1]);
                            }
                            else{
                                obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.hasLevel.push(filterHierarchy[i].split('hasLevel').splice(1)[ii].split(';').split(':')[1].replace(' ',''));
                                //console.log('hasLevel' ,filterHierarchy[i].split('hasLevel').splice(1)[ii].split(';').split(':')[1]);
                            }

                        }
                        else if (filterHierarchy[i].split('hasLevel').splice(1)[ii].includes(',')){
                            for (var ll in filterHierarchy[i].split('hasLevel').splice(1)[ii].split(',')){
                                if (filterHierarchy[i].split('hasLevel').splice(1)[ii].split(',')[ll].includes('.')){
                                    obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.hasLevel.push(filterHierarchy[i].split('hasLevel').splice(1)[ii].split(',')[ll].split(' .')[0].split(':')[1].replace(' ',''));
                                    //console.log('hasLevel' ,filterHierarchy[i].split('hasLevel').splice(1)[ii].split(',')[ll].split(' .')[0].split(':')[1]);
                                }
                                else{
                                    obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.hasLevel.push(filterHierarchy[i].split('hasLevel').splice(1)[ii].split(',')[ll].split(':')[1].replace(' ',''));
                                    //console.log('hasLevel' ,filterHierarchy[i].split('hasLevel').splice(1)[ii].split(',')[ll].split(':')[1]);
                                }
                            }
                        }
                        else if (filterHierarchy[i].split('hasLevel').splice(1)[ii].includes('.')){
                            obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchy.hasLevel.push(filterHierarchy[i].split('hasLevel').splice(1)[ii].split(' .')[0].split(':')[1].replace(' ',''));
                            //console.log('hasLevel' ,filterHierarchy[i].split('hasLevel').splice(1)[ii].split(' .')[0].split(':')[1]);
                        }
                    }
                }
                else{
                    //console.log(filterHierarchy[i]);
                }
            }
        }
    }
    //Fill Dimensions with hierarchyStep
    for (var i in filterHierarchyStep){
        for (var l in obj.dimension){
            for (var o in obj.dimension[l].dimensionProperty.hasHierarchy){
                if (filterHierarchyStep[i].includes(obj.dimension[l].dimensionProperty.hasHierarchy[o].hasHierarchy)){
                    var tmppc;
                    if (filterHierarchyStep[i].split('pcCardinality')[1].split('.')[0].includes(';')){
                        tmppc = filterHierarchyStep[i].split('pcCardinality')[1].split('.')[0].split(';')[0];
                    }
                    else{
                        tmppc = filterHierarchyStep[i].split('pcCardinality')[1].split('.')[0];
                    }
                    obj.dimension[l].dimensionProperty.hasHierarchy[o].hierarchyStep.push({childLevel: filterHierarchyStep[i].split('childLevel')[1].split(' ;')[0].split(':')[1], hierarchyStep: filterHierarchyStep[i].split(' a ')[0].split(':')[1], inHierarchy: filterHierarchyStep[i].split('inHierarchy')[1].split(' ;')[0].split(':')[1], parentLevel: filterHierarchyStep[i].split('parentLevel')[1].split(' ;')[0].split(':')[1], pcCardinality: tmppc.split(':')[1].replace(' ','')})
                }
            }
        }
    }
    //Fill LevelProperty with label, levelProperty and templates
    for (var i in filterLevelProperty){
        var tmpGeo = [];
        if (filterLevelProperty[i].includes('hasGeometry')){
            for (var ii in filterLevelProperty[i].split('hasGeometry').slice(1)){
                if(filterLevelProperty[i].split('hasGeometry').slice(1)[ii].includes(';')){
                    tmpGeo.push(filterLevelProperty[i].split('hasGeometry').slice(1)[ii].split(' ;')[0].replace(' ','').split(':')[1]);
                    console.log(filterLevelProperty[i].split('hasGeometry').slice(1)[ii].split(' ;')[0].replace(' ','').split(':')[1]);
                }
                else{
                    tmpGeo.push(filterLevelProperty[i].split('hasGeometry').slice(1)[ii].split('.')[0].replace(' ','').split(':')[1]);
                    console.log(filterLevelProperty[i].split('hasGeometry').slice(1)[ii].split('.')[0].replace(' ','').split(':')[1]);
                }
            }
        }
        obj.levelProperty.push({hasAttribute: [], hasGeometry: tmpGeo, label: filterLevelProperty[i].split('"')[1], levelAttribute: [], levelProperty: filterLevelProperty[i].split(' a ')[0].split(':')[1]});
    }
    //Fill LevelProperty with hasAttribute and levelAttribute
    for (var i in filterLevelAttribute){
        for (var l in obj.levelProperty){
            if (filterLevelAttribute[i].includes(obj.levelProperty[l].levelProperty)){
                obj.levelProperty[l].hasAttribute.push(filterLevelAttribute[i].split(' a ')[0].split(':')[1]);
                if (filterLevelAttribute[i].split('range ')[1].includes('geo:')){
                    obj.levelProperty[l].levelAttribute.push({inLevel: filterLevelAttribute[i].split('inLevel ')[1].split(' ;')[0].split(':')[1] ,label: filterLevelAttribute[i].split('"')[1],levelAttribute: filterLevelAttribute[i].split(' a ')[0].split(':')[1], range: filterLevelAttribute[i].split('range ')[1]});
                }
                else{
                    obj.levelProperty[l].levelAttribute.push({inLevel: filterLevelAttribute[i].split('inLevel ')[1].split(' ;')[0].split(':')[1] ,label: filterLevelAttribute[i].split('"')[1],levelAttribute: filterLevelAttribute[i].split(' a ')[0].split(':')[1], range: filterLevelAttribute[i].split('xsd:')[1].split('.')[0]});
                }
            }
        }
    }
    //Fill LevelProperty with hasGeometry
}


function convertDataToObjects(index){
    var lines = convertFileIntoLines(dataEntry[index]);
  lines = RemoveComments(lines);
  for(var i in lines){
    switch (lines[i].split(" a ")[1].split(" ")[0].split(':')[1].trim()){
      case 'DataStructureDefinition':
        DataStructureDefinitionName = lines[i].split(':')[0].trim();
        var split = lines[i].split('[');
        //console.log('Name:', DataStructureDefinitionName);
        for (var j in split){
          if (split[j].includes(DataStructureDefinitionName)){
            if (split[j].includes("level")){ 
              var level = split[j].split(':')[2].split(';')[0].trim();
              var cardinality = split[j].split(':')[4].split(']')[0].trim();
              //console.log('Level:', level, '\nCardinality:', cardinality);
              DataStructureDefinition.dimension.push({name: level, cardinality: cardinality, dimensionProperty: {}});
            }
            else if (split[j].includes("measure")){
              var measure = split[j].split(':')[2].split(';')[0].trim();
              var aggregateFunction = split[j].split(':')[4].split(']')[0].trim();
              //console.log('Measure:', measure, '\nAggregateFunction:', aggregateFunction);
              DataStructureDefinition.measure.push({measure: measure, aggregateFunction: aggregateFunction, label: {}, range: {}});
            }
          }
        }
        break;
      case 'Property': 
        var name = lines[i].split(' a ')[0].split(':')[1].trim();
        var label = lines[i].split(' "')[1].split('"')[0].trim();
        var range = null;
        var hasHierarchy = null;
        //console.log('Name:', name, '\nLabel:', label); 
        if (lines[i].includes('MeasureProperty')){
          if (lines[i].includes('geo:')){
            range = lines[i].split('geo:')[1].split('.')[0].trim();
          }
          else{
            range = lines[i].split('xsd:')[1].split('.')[0].trim();
          }
          //console.log('Range:', range);
          traverse(DataStructureDefinition, name).label = label;
          traverse(DataStructureDefinition, name).range = range;
        }
        else if (lines[i].includes('DimensionProperty')){   
          var target = traverse(DataStructureDefinition, name.replace('Dim','')); 
          if (lines[i].includes('hasHierarchy')){
            hasHierarchy = lines[i].split('hasHierarchy')[1].split(';')[0].replace('.','').replace(RegExp('gnw:', "gi"),'').trim();
            var obj = {name: name, label: label, hasHierarchy: []};
            target.dimensionProperty = obj;
            var split = hasHierarchy.split(',');
            //console.log('HasHierarchy:', hasHierarchy);
            for(var j in split){
              obj.hasHierarchy.push({hasHierarchy: split[j].trim(), hierarchy: {}, hierarchyStep:[]});
            }
          } 
          else{
            var obj = {name: name, label: label};
            target.dimensionProperty = obj;
          }
        }

        break;
      case 'Hierarchy':
        var hierarchy = lines[i].split(' a ')[0].split(':')[1].trim();
        var label = lines[i].split(' "')[1].split('"')[0].trim();
        var inDimension = lines[i].split('inDimension')[1].split(';')[0].replace(RegExp(DataStructureDefinitionName + ':', "gi"), '').trim();
        var hasLevel = lines[i].split('hasLevel')[1].replace(RegExp(DataStructureDefinitionName + ':', "gi"), '').replace('.','').trim();
        var splitHasLevel = hasLevel.split(',');
        var splitInDimension = inDimension.split(',');
        for(var j in splitInDimension){  
          var target = traverseSet(DataStructureDefinition.dimension, splitInDimension[j].replace('Dim','').trim()); 
          for(var n in target){
            if (typeof target[n].dimensionProperty != 'undefined'){
              for(var l in target[n].dimensionProperty.hasHierarchy){
                if (target[n].dimensionProperty.hasHierarchy[l].hasHierarchy == hierarchy.trim()){
                  var obj = {hierarchy: hierarchy, label: label, inDimension: {}, hasLevel: []};
                  for(var m in splitHasLevel){
                    obj.hasLevel.push(splitHasLevel[m].trim());
                  }
                  obj.label = label;
                  obj.inDimension = splitInDimension[j];
                  target[n].dimensionProperty.hasHierarchy[l].hierarchy = obj;
                }
              }
            }
          }
        }
        
        
        //DataStructureDefinition.hierarchy.push(obj);
        break;
      case 'HierarchyStep':
        //console.log("%c" + lines[i], 'background: #222; color: #bada55');
        var hierarchyStep = lines[i].split(' a ')[0].split(':')[1].trim();
        var inHierarchy = lines[i].split("inHierarchy")[1].split(';')[0].split(':')[1].trim();
        var childLevel = lines[i].split('childLevel')[1].split(';')[0].split(':')[1].trim();
        var parentLevel = lines[i].split('parentLevel ')[1].split(';')[0].split(':')[1].trim();
        var pcCardinality = lines[i].split('pcCardinality')[1].split(';')[0].split(':')[1].replace('.','').trim();
        var topologicalRelation = null;
        var target = traverse(DataStructureDefinition, inHierarchy.trim());
        var obj = {hierarchyStep: hierarchyStep, inHierarchy: inHierarchy, childLevel: childLevel, parentLevel: parentLevel, pcCardinality: pcCardinality};
        //console.log('HierarchyStep:', hierarchyStep, '\nInHierarchy:', inHierarchy, '\nChildLevel:', childLevel, '\nParrentLevel:', parentLevel, '\nPcCardinality:', pcCardinality);
        if (lines[i].includes('topologicalRelation')){
          topologicalRelation = lines[i].split('topologicalRelation')[1].split('.')[0].replace(RegExp('qb4so:', "gi"),'').trim();
          obj = {hierarchyStep: hierarchyStep, inHierarchy: inHierarchy, childLevel: childLevel, parentLevel: parentLevel, pcCardinality: pcCardinality, topologicalRelation: []};
          var split = topologicalRelation.split(',');
          //console.log('TopologicalRelation:', topologicalRelation);
          for(var j in split){
            obj.topologicalRelation.push(split[j].trim());
          }
        }
        target.hierarchyStep.push(obj);
        break;
      case 'LevelProperty':
        var levelProperty = lines[i].split(' a ')[0].split(':')[1].trim();
        var label = lines[i].split(' "')[1].split('"')[0].trim();
        var hasAttribute = lines[i].split('en ;')[1].replace(RegExp('qb4o:hasAttribute', "gi"),'').replace(RegExp(';', "gi"),',').replace(RegExp(DataStructureDefinitionName + ':', "gi"), '').split('.')[0].replace(RegExp(',', "gi"),' ').replace(/\s\s+/g, ' ').split('geo:')[0].trim();
        var obj = {levelProperty: levelProperty, label: label, hasAttribute: [], levelAttribute: []};
        var hasGeometry = null;
        //console.log('LevelProperty:', levelProperty, '\nLabel:', label, '\nHasAttribute:', hasAttribute);
        if (lines[i].includes('hasGeometry')){
          hasGeometry = lines[i].replace('geo:', 'id:').split('id:hasGeometry')[1].replace(RegExp('geo:hasGeometry', "gi"),'').replace('.','').replace(RegExp(';', "gi"),',').replace(/\s\s+/g, ' ').trim();
          obj = {levelProperty: levelProperty, label: label, hasAttribute: [], hasGeometry: [], levelAttribute: []};
          var split = hasGeometry.split(',');
          for(var j in split){
            obj.hasGeometry.push(split[j].replace(RegExp(DataStructureDefinitionName + ':', "gi"), '').trim());
          }
          //console.log('HasGeometry:', hasGeometry);
        }
        var split = hasAttribute.split(' ');
        for(var j in split){
          obj.hasAttribute.push(split[j]);
        }
        DataStructureDefinition.levelProperty.push(obj);
        break;
      case 'LevelAttribute':
        var levelAttribute = lines[i].split(' a ')[0].split(':')[1].trim();
        var label = lines[i].split(' "')[1].split('"')[0].trim();
        var inLevel = lines[i].split('inLevel')[1].split(';')[0].replace(RegExp(DataStructureDefinitionName + ':', "gi"), '').trim();
        var range;     
        var obj = {levelAttribute: levelAttribute, label: label, inLevel: inLevel, range: []};  
        var target = traverseSet(DataStructureDefinition.levelProperty, inLevel); 
        if (lines[i].includes('xsd:')){
          obj.range = lines[i].split('xsd:')[1].replace('.','').trim();
        }
        else if(lines[i].includes('rdfs:range')){
          range = lines[i].split('rdfs:range')[1].replace('.','').replace('geo:','').replace('virtrdf:','').replace('.','').trim();
          var split = range.split(',');
          for(var j in split){
            split[j] = split[j].trim();
          }
          obj.range = split;
        }
        for(var l in target){
          if (target[l].levelProperty){
            target[l].levelAttribute.push(obj);
          }
        }
        //console.log('LevelAttribute:', levelAttribute, '\nLabel:', label, '\nInLevel:', inLevel, 'Range:', range);
        break;
      default:
        console.log(lines[i]);
        break;
    }
  }
  DataStructureDefinitionName = DataStructureDefinitionName + ':';
}
