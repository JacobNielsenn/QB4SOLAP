<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<script type="text/javascript" src="scripts/interact.js"></script>
	<script type="text/javascript" src="scripts/loadSchema.js"></script>
	<title>EXTBI</title>
	<meta charset="iso-8859-1">
	<link rel="stylesheet" href="../styles/layout.css" type="text/css">
	<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
	<script type="text/javascript">
		////////////////////////////////////
		//         Struct Factory         //
			function makeStruct(names) {
				var names = names.split(' ');
				var count = names.length;
				function constructor() {
					for (var i = 0; i < count; i++) {
						this[names[i]] = arguments[i];
					}
				}
				return constructor;
			}
		////////////////////////////////////
		//        Global Variables        //
			var GeneratedQueryElement;
			var QueryUnit = [];
			var counter = 1;
			var UniqueID = 0;    
			var NoSelectors = 0;
			var OprSelectOptions = [""];
			var selector = null;
			var LastOpr = "null";
			var currentP;
			var selectedOpr;
			var AvailableOperations = [];
			var SpatialAggregation = ['Union', 'Intersection', 'Buffer', 'ConvexHull', 'MinimumBoundingRectangle'];
			var TopologicalRelations = ['Intersects', 'Disjoint', 'Equals', 'Overlaps', 'Contains', 'Within', 'Touches', 'Covers', 'CoveredBy', 'Crosses', 'Distance'];
			var NumericOperations = ['Perimeter', 'Area', 'NearstNeighbor', 'NoOfGeometries'];
			var RelationalOperators = ['Not equal', 'Equal', 'Greater than or equal', 'Less than or equal', 'Greater than', 'Less than'];
			var tab = '   ';
			window.onload = Initialize; 
		////////////////////////////////////
		//         Build Structure        //
			var _FilterOptions 	= makeStruct("SDice");
			var FilterOptions 	= new _FilterOptions(SDice);
			var _FilterCreate 	= makeStruct("P TextBox DropMenu_0");
			var FilterCreate 		= new _FilterCreate('InsertP', 'InsertTextBox', 'InsertDropMenu');
			var _Filter 				= makeStruct("id name create options");
			var Filter 					= new _Filter(UID(), 'Filter', FilterCreate, FilterOptions);

			var _BindFilterOptions 	= makeStruct("SDice");
			var BindFilterOptions 	= new _BindFilterOptions(SDice);
			var _BindFilterCreate 	= makeStruct("P TextBox DropMenu_0");
			var BindFilterCreate 		= new _BindFilterCreate('InsertP', 'InsertTextBox', 'InsertDropMenu');
			var _BindFilter 				= makeStruct("id name create options");
			var BindFilter 					= new _BindFilter(UID(), 'BindFilter', BindFilterCreate, BindFilterOptions);

			var _SDiceOptions 	= makeStruct("BindFilter Filter");
			var SDiceOptions 		= new _SDiceOptions(BindFilter, Filter);
			var _SDiceCreate 		= makeStruct("P TextBox DropMenu_0 DropMenu_1 DropMenu_2");
			var SDiceCreate 		= new _SDiceCreate('InsertP', 'InsertTextBox', 'InsertDropMenu', 'InsertDropMenu', 'InsertDropMenu');
			var _SDice 					= makeStruct("id name select_0 value_0 create options");
			var SDice 					= new _SDice(UID(), 'SDice', TopologicalRelations, '-Select-', SDiceCreate, SDiceOptions);

			var _QueryOptions 	= makeStruct("SDice");
			var QueryOptions 		= new _QueryOptions(SDice);
			var _Query 					= makeStruct("id name options");
			var Query 					= new _Query(UID(), 'Query', QueryOptions);

			var QueryPointer = Query;
		////////////////////////////////////
		// Methods - Initialize & Utility //
			function Initialize(){
				GeneratedQueryElement = document.getElementById('GeneratedQuery');
				ListOperations('Query');
				convertDataToObjects(data);
			}

			function UID(){
				return UniqueID += 1;
			}

			function GetClosestP(element){	
				var p = element;
				while (p.tagName != 'P'){
				p = document.getElementById(p.parentNode.id);
			}
			return p;
			}

			//Return object of the Query tree structure
			function traverse(o, target, tag){
				//console.log('Looking for:', target);
				var obj;
				for (var i in o) {
					if (o[i] == target){
						if (typeof tag == 'undefined'){
								return o;
						}
						else{
							if (typeof o[tag] != 'undefined'){
								console.log(o, 'Found Value');
								return o;
							}
						}
					}
					if (o[i] !== null && typeof(o[i])=="object") {
						obj = traverse(o[i], target, tag);
						if (obj != null){
							return obj;
						}
					}
				}
			}

			function traversetargets(o, target, tag){
				//console.log('Looking for:', target);
				var obj;
				var found = [];
				var allfound = false;
				for (var i in o) {
					for (var l in target){
						for (var j in tag){
							if (o[tag[j]] != 'undefined'){
								found[j] = true;
							}
							for (var h in found){
								if (found[h] == false){
									allfound = false;
									console.log(allfound);
									continue;
								}
							}
							if (allfound){
								return o;
							}
						}
					}
				}
				for (var i in o){
					if (o[i] !== null && typeof(o[i])=="object") {
						obj = traverse(o[i], target, tag);
						if (obj != null){
							return obj;
						}
					}
				}
			}

			function insertAfter(newNode, referenceNode) {
	  		referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
			}

			function findElementType(array, name){
				for (var i in array){
					if (array[i].getAttribute('name').replace(/[0-9]/g, '') == name){
						return array[i];
					}
				}
			}
		////////////////////////////////////
		//    Methods - List Operators    //
			function ListOperations(selector){
				var OperationsList = document.getElementById('OperationsList');
				OperationsList.innerHTML = "";
				var obj = traverse(QueryPointer, selector);
				for(var i in obj.options){
					var text = document.createElement('text');
					text.setAttribute('class', 'build-target noselect');
					text.setAttribute('name', i);        
					text.innerHTML = i;
					OperationsList.appendChild(text);
					InsertBR(OperationsList);
				}
			}

			function OprSelect(event){
				selector = event;
				event.currentTarget.classList.toggle('switch-color');
				selectedOpr = event.currentTarget;
				event.preventDefault();
				ListOperations(LastOpr);
			}
		////////////////////////////////////
		//     Methods - List Options     //
			function ListOptions(ele, list, property){
				ele.innerHTML = "";
				InsertOption(ele, "--Select--");
				for(var i in list){
					InsertOption(ele, eval('list[' + i + ']' + property));
				}
			}

			function ListOptionsArray(ele, list){
				ele.innerHTML = "";
				InsertOption(ele, "--Select--");
				for(var i in list){
					InsertOption(ele, list[i]);
				}
			}

			function InsertOption(ele, text){
				var option = document.createElement('option');
				option.setAttribute('value', text);
				option.innerHTML = text;
				ele.appendChild(option);
			}

			function FilterOptions(list, value){

			}
		////////////////////////////////////
		//       Methods Operations       //
			function fSDice(event, query, id, Obj){
				InsertP(event, query, id, 'SDice');
				InsertTextBox('SDice', currentP, id);
				currentP.appendChild(InsertDropMenuFromArray(currentP, TopologicalRelations, id, "TopologicalRelations", 100, ', FilterUpdate(this)'));
				currentP.appendChild(InsertDropMenuFromObjects(currentP, DataStructureDefinition.dimension, '.name', id, 'baseLevel', 100));
				currentP.appendChild(InsertDropMenuFromObjects(currentP, null, null, id, 'spatialLevel', 100, null));
				return {'name': Obj.name, 'id': id, 'value': Obj.value_0, 'toString': function(){
							GeneratedQueryElement.innerHTML += "SELECT " + this.value + " WHERE\n";
							}};
			}

			function fBindFilter(event, query, id, Obj){
				InsertP(event, query, id, 'BindFilter');
				InsertTextBox('Filter', currentP, id);
				InsertDropMenuFromArray(currentP, NumericOperations, id, 'BIF', 125);
				InsertTextBox('where ', currentP, id);
				currentP.appendChild(InsertInput(currentP, '?', id));
				currentP.appendChild(InsertDropMenuFromArray(currentP, RelationalOperators, id, 'RelationalOperators', 150));
				currentP.appendChild(InsertInput(currentP, '', id));
				return {'name': Obj.name, 'id': id, 'value': Obj.value_0, 'toString': function(){
							GeneratedQueryElement.innerHTML += "SELECT " + this.value + " WHERE\n";
							}};
			}

			function fFilter(event, query, id, Obj){
				InsertP(event, query, id, 'Filter');
				InsertTextBox('Filter', currentP, id);
				currentP.appendChild(InsertDropMenuFromArray(currentP, TopologicalRelations, id, 'TopologicalRelations', 100, ', FilterUpdate(this)'));
				return {'name': Obj.name, 'id': id, 'value': Obj.value_0, 'toString': function(){
							GeneratedQueryElement.innerHTML += "SELECT " + this.value + " WHERE\n";
							}};
			}
		////////////////////////////////////
		//    Methods - Generate Graph    //
			function print(){
				var c = document.getElementById('OprSelectStatement').childNodes;
				var dist = document.getElementById('GeneratedQuery');
				dist.innerHTML = '';
				console.log(c);
				for (var i in c){
					if (c[i].tagName === "P" && c[i].id !== 'StartQuery'){
						var tmp = c[i].getAttribute('name').replace(/[0-9]/g, '');
						switch(tmp){
							case 'SDice':
								dist.innerHTML += 'SELECT ?obs WHERE {\n' + tab + '?obs rdf:type qb:Observation .\n';
								var t = document.getElementsByName(c[i].getAttribute('name'))[0].childNodes;
								if (findElementType(t, 'baseLevel').value == '--Select--'){
									continue;
								}	
								var baseLevel = DataStructureDefinitionName + traverse(DataStructureDefinition, findElementType(t, 'baseLevel').value, 'levelProperty').levelAttribute[0].levelAttribute;
								var baseLevelVariable = baseLevel.split(':')[1].replace('ID','');
								var memberOf = DataStructureDefinitionName + traverse(DataStructureDefinition, findElementType(t, 'baseLevel').value, 'levelProperty').levelProperty;
								dist.innerHTML += tab + '?obs ' + baseLevel + ' ' + baseLevelVariable + ' .\n';
								dist.innerHTML += tab + baseLevelVariable + ' qb4o:memberOf ' + memberOf + ' .\n';
								var memberOfVariable = traverse(DataStructureDefinition, memberOf.split(':')[1]+'Dim', 'inDimension');
								var found = false;
								if(memberOfVariable.hierarchy != "supervision"){
									for (var l in memberOfVariable.hasLevel){
										if (found){
											memberOfVariable = memberOfVariable.hasLevel[l];
											break;
										}
										if (memberOfVariable.hasLevel[l] == memberOf.split(':')[1]){
											found = true;
										}
									}
								}
								else{
									alert('This is not IMPLMENTED yet');
									return;
								}
								dist.innerHTML += tab + baseLevelVariable + ' skos:broader ' + '?' + memberOfVariable + ' .\n';
								var baseGeo = traverse(DataStructureDefinition, findElementType(t, 'baseLevel').value, 'levelProperty').hasGeometry;
								if(typeof baseGeo != 'undefined'){
									dist.innerHTML += tab + baseLevelVariable + ' ' + DataStructureDefinitionName + baseGeo[0] + ' ' + '?' + baseGeo[0] + ' .\n';
								}
								var memberOfGeo = traverse(DataStructureDefinition, memberOfVariable, 'levelProperty').hasGeometry;
								if(typeof memberOfGeo != 'undefined'){
									dist.innerHTML += tab + '?' + memberOfVariable + ' ' + DataStructureDefinitionName + memberOfGeo[0] + ' ' + '?' + memberOfGeo[0] + '.\n';
								}
								//traversetargets(DataStructureDefinition, [])
								console.log('PRINT!', c[i].getAttribute('name'));
								break;
							default:
								console.log('PRINT! NOT IMPLMENTED YET', tmp);
						}
					}
				}	
			}
		////////////////////////////////////
		//    Methods - Building Blocks   //
			function InsertBR(ele){
				ele.appendChild(document.createElement('br'));
			}

			function InsertP(ele, Query, id, name){
				currentP = document.createElement('p');
				currentP.setAttribute('id', id);
				currentP.setAttribute('name', name+id);
				Query.appendChild(currentP);
			}

			function InsertTextBox(title, place, id){
				var TextBox = document.createElement('text');
				TextBox.setAttribute('name', 'textbox'+id);
				TextBox.setAttribute('class', "tap-target noselect");  
				TextBox.setAttribute('id', id);
				TextBox.setAttribute('style', 'margin-right: 5px;');
				TextBox.innerHTML = title;
				place.appendChild(TextBox);
			}

			function InsertInput(place, text, id, size){
				var ele = document.createElement('input');
				ele.setAttribute('name', 'input'+id);
				ele.setAttribute('id', id);
				ele.setAttribute('value', text);
				ele.setAttribute('type', 'text');
				ele.setAttribute('style', 'width:'+ size +'px; margin-right: 5px;');
				ele.setAttribute('onchange', 'ValueChanged(this)');
				return ele;
			}

			function InsertDropMenuFromObjects(place, list, property, id, name, size, updater){
				var ele = document.createElement('select');
				ele.setAttribute('name', name+id);
				ele.setAttribute('style', 'width:' + size + 'px; margin-right: 5px;');
				if (updater != null){
					ele.setAttribute('onchange', 'ValueChanged(this), UpdateSelectOptions(this), print(this)' + updater);
				}
				else{
					ele.setAttribute('onchange', 'ValueChanged(this), UpdateSelectOptions(this), print(this)');
				}
				ele.setAttribute('id', id);
				//place.appendChild(ele);
				ListOptions(ele, list, property);
				return ele
			}

			function InsertDropMenuFromArray(place, list, id, name, size, updater){
				var ele = document.createElement('select');
				ele.setAttribute('name', name+id);
				ele.setAttribute('style', 'width:' + size + 'px; margin-right: 5px;');
				if (updater != null){
					ele.setAttribute('onchange', 'ValueChanged(this), UpdateSelectOptions(this), print(this)' + updater);
				}
				else{
					ele.setAttribute('onchange', 'ValueChanged(this), UpdateSelectOptions(this), print(this)');
				}
				ele.setAttribute('id', id);
				//place.appendChild(ele);
				ListOptionsArray(ele, list);
				return ele;
			}
			
		////////////////////////////////////
		//         Update Methods         //
			function ValueChanged(element){
				/*//console.log(QueryUnit);
				for (var j = 0; j < QueryUnit.length; j++){
					if (QueryUnit[j].id == element.id)
						QueryUnit[j].value = element.value;
						var p = GetClosestP(element);
						//console.log(p);
						if (p.getAttribute('name') == 'SDice'){
							console.log('SDice here in P');
						}
				}
				print();*/
			}

			function UpdateSelectOptions(element){
				var p = GetClosestP(element);
				if (p.getAttribute('name') == 'SDice'+element.id){
					if(element.name == 'baseLevel'+element.id){
						var obj = document.getElementsByName('spatialLevel' + element.id)[0];
						var objlist = traverse(DataStructureDefinition.dimension, element.value+'Dim');
						//ListOptions(obj, DataStructureDefinition.dimension, '.name');
						var tmp = objlist.hasHierarchy[0].hierarchy;
						ListOptionsArray(obj, objlist.hasHierarchy[0].hierarchy.hasLevel);
						console.log(objlist.hasHierarchy[0]);
					}
				}
			}

			function FilterUpdate(element){
				var old = document.getElementsByName('input'+element.id);
				if (typeof old[0] != 'undefined'){
					old[0].parentNode.removeChild(old[0]);
				}
				switch (element.value){
					case 'Within':
						insertAfter(InsertInput(GetClosestP(element), '', element.id), element);
						break;
					case 'Distance':
						insertAfter(InsertInput(currentP, '', element.id), element);
						insertAfter(InsertDropMenuFromArray(currentP, RelationalOperators, element.id, 'RelationalOperators', 150), element);
					break;
				}
			}
		////////////////////////////////////
		//          Click System          //
			interact('.tap-target').on(
				'tap', function (event) {  
					if(selector != null){ //Use To Deselected Query Component (only one component can be active at a time)
						selector.currentTarget.classList.toggle('switch-color');
					}
					if((selector == null ? "" : selector.currentTarget.getAttribute('id')) == event.currentTarget.getAttribute('id')){ //Program will crash if you try to getAttribute from a null. This is the reason for the less desirable code.
						selector = null;
						ListOperations(LastOpr);
					}else{   
						OprSelect(event);
					}      
				}
			);

			interact('.build-target').on(
				'tap', function (event) {
					var selected = event.currentTarget.getAttribute('name');
					console.log('selected:', selected);
					var QueryStatment = document.getElementById('OprSelectStatement');  
					var Obj = traverse(QueryPointer, selected);
					var id = UID();
					var element;
					console.log("Menu Item:", Obj);
					//
						/*for(var i in Obj.create){
							i = i.split('_')[0];
							iNb = i.split('_')[1];
							switch (i){
								case "P":
									InsertP(event, QueryStatment, id);
									break;
								case "BR":
									alert("I Need This");
									break;
								case "TextBox":
									InsertTextBox(Obj.name, currentP, id);
									break;
								case "Input":
									InsertInput(currentP, Obj.value + '_' + iNb, id);
									break;
								case "DropMenu":
									console.log(Obj);
									InsertDropMenu(currentP, Obj.select + '_' + iNb, id);
									break;
								default:
									console.log("Can't Resolve:", i);
									break;
							}
						}
						console.log(Obj);*/
					switch (Obj.name){
						case "SDice":
							//
								/*element = {'name': Obj.name, 'id': id, 'value': Obj.value_0, 'toString': function(){
								GeneratedQueryElement.innerHTML += "SELECT " + this.value + " WHERE\n";
								}};*/
							element = fSDice(event, QueryStatment, id, Obj);
							break;
						case "BindFilter":
							//
								/*element = {'name': Obj.name, 'id': id, 'toString': function(){
								GeneratedQueryElement.innerHTML += "  ?obs rdf:type qb:Observation ;\n    gnw:customerID ?cust .\n  ?cust qb4o:memberOf gnw:customer ;\n    skos:broader ?city .\n  ?city gnw:cityGeo ?cityGeo .\n";
								}};*/
							element = fBindFilter(event, QueryStatment, id, Obj);
							break;
						case "Filter":
							element = fFilter(event, QueryStatment, id, Obj);
							break;
						default:
							alert("Need to define case for element");
							break;
					}
					/*QueryUnit.push(element);*/
					ListOperations(selected);
					print();
					event.preventDefault();
				}
			);
	</script>
	<style type="text/css">
		.noselect {
			-webkit-touch-callout: none; /* iOS Safari */
			-webkit-user-select: none;   /* Chrome/Safari/Opera */
			-khtml-user-select: none;    /* Konqueror */
			-moz-user-select: none;      /* Firefox */
			-ms-user-select: none;       /* Internet Explorer/Edge */
			user-select: none;           /* Non-prefixed version, currently not supported by any browser */                               
		}

		.tap-target, .build-target {
			cursor: pointer;
			transition: all 0.3s;
		}

		.tap-target.switch-color{
			color: green;
			text-decoration: underline;
		}

		.appear {
	    width: 250px; 
	    border: #000 2px solid;
	    background:#F8F8F8;
	    position: relative;
	    top: 5px;
	    left:15px;
	    display:none;
	    padding: 0 20px 20px 20px;
	    z-index: 1000000;
		}
		.hover  {
	    cursor:pointer;
	    width: 5px;
		}
		.hover:hover .appear {
	    display:block;
		}

		input[type=text], select {
			display: inline-block;
			border: 1px solid #ccc;
			border-radius: 4px;
			width: 40px;
			box-sizing: border-box;
		}
	</style>
</head>
<body class="about QB4SOLAP">
	<div class="wrapper row1">
	<header id="header" class="clear">
	<?php include '../logo.html';?>
	<?php include '../menu.html';?>
	</header>
	</div>
	<!-- content -->
	<div class="wrapper row2">
		<div id="container" class="clear">
			<!--<section id="slider"><a href="#"><img src="images/demo/960x360.gif" alt=""></a></section>-->
			<?php include 'headline.html';?>
			<!-- content body -->
			<div id="content">
				<!-- main content -->   
				<aside id="left_column">
					<h2 class="title">SOLAP<br>Operators</h2>
					<section class="last" id="OperationsList" style="margin-left:10px;"></section>
				</aside>
				<section style="margin-left:10px;">
					<div class="no-top-margin" style="float:left;">
						<section>
							<div id="OprSelectStatement" style="margin-top:-20px; height:500px;  width:500px; overflow-y: scroll;">
								<p id="StartQuery">Query:</p>
							</div>
							<div>
								<p>Generated query from the above operators:</p>
								<textarea id="GeneratedQuery" style="margin-top:-5px; width:494px; height: 300px; overflow-y: scroll;"></textarea>
							</div>
						</section>   
					</div>
				</section>
			</div>
			<!-- right column -->  
			<aside id="right_column">
				<?php include '../topics.html';?>
				<?php include 'resources.html';?>
				<!-- /nav -->
				<?php include 'conference.html';?>
				<?php include 'contact.html';?>
			</aside>
			<!-- / content body -->
		</div>
	</div>
	<!-- footer -->
	<div class="wrapper row3">
		<?php include '../footer.html';?>
		<?php include '../analytics.html';?>
	</div>
</body>
</html>