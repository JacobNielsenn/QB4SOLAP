<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<script type="text/javascript" src="scripts/utility/interact.js"></script>
		<script type="text/javascript" src="scripts/utility/loadSchema.js"></script>
		<script type="text/javascript" src="scripts/utility/jquery-3.1.0.js"></script>
		<script type="text/javascript" src="scripts/utility/double-linked-list.js"></script>
		<script type="text/javascript" src="scripts/BasicComponentHTML.js"></script>
		<script type="text/javascript" src="scripts/createHTML.js"></script>
		<script type="text/javascript" src="scripts/lookUp.js"></script>
		<script type="text/javascript" src="scripts/menu.js"></script>
		<script type="text/javascript" src="scripts/print.js"></script>
		<script type="text/javascript" src="scripts/utility.js"></script>
		<script type="text/javascript" src="scripts/buildquery.js"></script>
		<script type="text/javascript" src="scripts/runQuery.js"></script>
		<script type="text/javascript" src="scripts/debug.js"></script>
		<script type="text/javascript" src="scripts/map.js"></script>
		<script type="text/javascript" src="scripts/options.js"></script>
        <script type="text/javascript" src="scripts/classes/operator.js"></script>
        <script type="text/javascript" src="scripts/classes/rdf.js"></script>
        <script type="text/javascript" src="scripts/classes/select.js"></script>
        <script type="text/javascript" src="scripts/classes/rdfHandler.js"></script>
        <script type="text/javascript" src="scripts/classes/query.js"></script>
		<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>
		<title>EXTBI</title>
		<meta charset="iso-8859-1">
		<link rel="stylesheet" href="../styles/layout.css" type="text/css">
		<link rel="stylesheet" href="./css/menu/build.css" type="text/css">
		<link rel="stylesheet" href="./css/menu/RUPath.css" type="text/css">
		<link rel="stylesheet" href="./css/button/button.css" type="text/css">
		<link rel="stylesheet" href="./css/trash/tap.css" type="text/css">
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css"/>
		<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
		<script type="text/javascript">
		////////////////////////////////////
		//        Global Variables        //
			var GeneratedQueryElement;
			var QueryStatment;
			var ID = {};
			var NameID = {};
			var queryOfOperators = [];
			var additionalQuery = false;

		// - SOLAP 						  //
		  //var Dimensions = ['employee', 'orderDate', 'dueDate', 'shippedDate', 'product', 'order', 'shipper', 'supplier', 'customer'];
			var SpatialAggregation = ['Union', 'Intersection', 'Buffer', 'ConvexHull', 'MinimumBoundingRectangle'];
			var TopologicalRelations = ['Intersects', 'Disjoint', 'Equals', 'Overlaps', 'Contains', 'Within', 'Touches', 'Covers', 'CoveredBy', 'Crosses', 'Distance'];
			var NumericOperations = ['Perimeter', 'Area', 'NoOfInteriorRings' , 'Distance', 'HaversineDistance', 'NearstNeighbor', 'NoOfGeometries'];
			var DataTypes = ['Point', 'Polygon', 'Multi Polygon'];
			var RelationalOperators = ['Not equal', 'Equal', 'Greater than or equal', 'Less than or equal', 'Greater than', 'Less than'];
			var AGG = ['MAX', 'MIN', 'AVG'];
			var SpatialDimensions = ['supplier', 'customer'];
			var SpatialFunction = ['st_distance'];
		// - Enums //
			structureLevel = {Dimenasion: 3, Level: 2, Attribute: 1};
			spatialMode = {On: 1, Off:0};
		////////////////////////////////////
		// Methods - Initialize & Utility //
			window.onload = Initialize;
			function Initialize(){
				GeneratedQueryElement = document.getElementById('GeneratedQuery');
				QueryStatment = document.getElementById('StartQuery');
				convertDataToObjects(data);
				RemoveRegionFromDataStructureDefinition(DataStructureDefinition);
				console.log(DataStructureDefinition);
				document.getElementById('prefix').innerHTML = prefixes; //Fill the Prefiexs area
				setupdebug();
				$('#prefix').hide();
				document.getElementById('debug').style.visibility = "hidden";
				testing();
			}

			function testing(){
			    var a = new Query();
                var b = new ODice("test", 2);
                b.selRDF.add("?TestingVariable1");
                b.selRDF.add("?TestingVariable2");
                b.spaRDF.add(new RDF("test", "bla", "sefse"));
                GeneratedQueryElement.innerHTML = a.returnQuery;
            }
		</script>
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
            <div id="schema">
                <img id="myImg" src="./images/gnwSchema.png" width="100%">
            </div>
			<div id="content" style="width:960px;">
				<!-- main content -->   
				<section style="margin-left:10px;">
							<!-- old style: overflow-y: scroll; height:500px;-->
							<P>
								<button id="debug" class="hide" onclick="debugMenu(this)">Menus - Hide</button>
								<div id="test">

								</div>
							</P>
							<div id="OprSelectStatement" style="margin-top:0px; width:350px;  float:left;">
								<p id="StartQuery" name="Start" style="margin-top: 0px; margin-bottom: 0px;"></p>
								<div id="Builder" class="dropdown" style="width: 345px; height: 25px; border: 2px solid transparent; border-color: black; border-radius: 0px 0px 5px 5px; border-style: dashed;">
									<button class="dropbtn" style="text-align:center; margin:-2px -1px -1px -1px; height:28px; border-radius: 0px 0px 5px 5px; width:347px;">Choose an operator</button>
								  <div id="myDropdown" class="dropdown-content">
								    <a href="#" onclick="SSlice_within(this)">S-Slice</a>
								    <a href="#" onclick="SDice(this)">S-Dice</a>
								    <a href="#" onclick="SRU(this)">S-Roll-up</a>
								  </div>
								</div>
							</div>
							<div style="float:right; width: 550px;">
								<p></p>
								<button id="pre" style="float:left;" class="hide" onclick="prefixText(this)">Prefixes</button>
								<textarea disabled id="prefix" style="margin-top:0px; width:500px; height: 200px; float:left; background-color: lightgrey; overflow:hidden;"></textarea>
								<textarea id="GeneratedQuery" style="margin-top:0px; width:500px; height: 300px; overflow-y: scroll; float:left;"></textarea>
                                <p id="PreventSwap">
                                    <button id="QueryButton" style="float: left" onclick="runQuery()">Run Query</button>
									<button id="mes" style="float:left;" class="hide" onclick="Mes(this)">Aggregate</button>
									<button id="cls" style="float:left;" class="hide" onclick="Cls(this)">Clear Tables</button>
                                </p>
							</div>

				</section>
				<div id="ResultFromQuery" style="float:left; width: 100%; padding-top: 10px;">
				</div>
			</div>
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