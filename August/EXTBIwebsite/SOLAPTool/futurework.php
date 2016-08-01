<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="queries govAgriBus">
<div class="wrapper row1">
  <header id="header" class="clear">
    <?php include '../logo.html';?>
    <?php include '../menu.html';?>
  </header>
</div>
<div class="wrapper row2">
  <div id="container" class="clear">
    <?php include 'headline.html';?>
    <!-- content body -->
    <div id="content">
      <!-- main content -->



      <section>
        <article>
        
          <p>
The following queries require user-defined or built-in functions in order to support <br> spatial aggregation over spatial RDF data. 
          </p>

        </article>

        <article>
          <p><b>Query: Spatial union of the states in the USA where at least one customer placed an order in 1997. (Built-in function bif:st_union is required)</b></p>
            <pre>

SELECT (bif:st_union(?stateGeo) AS ?stateUnion)
WHERE {?o a qb:Observation; gnw:customerID ?cust; 
   gnw:orderDateID ?orderDate. ?cust skos:broader ?city.
   ?city skos:broader ?state. ?state gnw:stateGeo ?stateGeo. 
   {?state skos:broader ?country.}
UNION {?state skos:broader ?region. 
   ?region skos:broader ?country.} 
   ?country gnw:countryName ?countryName.
   ?orderDate skos:broader ?month. ?month skos:broader ?quarter.
   ?quarter skos:broader ?semester.?semester skos:broader ?year. 
FILTER (?countryName = 'United States' && ?yearNo = 1997)}
			</pre>
        </article>
        <article>
          <p><b>Query: For each supplier, distance between the location of the supplier and the centroid of the locations of all its customers.(Built-in bif:st_convexHull and bif:st_centroid functions are required)</b></p>
            <pre>

SELECT ?supName, ?distance 
WHERE { ?o a qb:Observation; 
  gnw:customerID ?cust; gnw:supplierID ?sup.
  ?cust gnw:customerGeo ?custGeo.?sup gnw:supplierGeo ?supGeo .
BIND(bif:st_centroid(bif:st_convexHull(?custGeo))AS ?centCustGeo)	
BIND(bif:st_distance(?supGeo,?centerCustGeo) AS ?distance)}	
GROUP BY ?supName ?distance

			</pre>
        </article>
        <article>
		<h2> Spatial Facts Cube with Spatial Measures</h2>
		          <p>
The following notional example in Figure 1 represents an ideal spatial fact cube with spatial measures(e.g.location) and spatial dimensions (e.g. highway, city) besides conventional ones (e.g. cost as measure, insurance as dimension). Spatial measures also require the classification of aggregate functions which are specific to spatial type of the measure as given in Figure 2.
          </p>
        </article>
         <article>
          <a href="/QB4SOLAP/images/spatialMeasures.png">
          <img src="/QB4SOLAP/images/spatialMeasures.png" alt="Spatial Measures" style="width:551px;height:203px">
          </a>
        </article>
		
		
	<p>
A spatial aggregation on location measure is defined as convex hull area of the accident locations which is the smallest convex region  that contains accident points. Also the spatial dimension levels related to the facts are city and highway that give a rise to n-ary topological relationships which can relate more than two spatial dimensions. An example spatial dimension <i>ex:highway</i> is given with <i>qb4so:topologicalRelation</i> property to represent intersection with other corresponding spatial dimensions (i.e highway intersects city). 
     </p>
   <pre>

### Measures ###
ex:cost a rdf:Property, qb:MeasureProperty; 
  rdfs:subPropertyOf sdmx-measure:obsValue;rdfs:range xsd:double.
ex:location a rdf:Property, qb:MeasureProperty; 
  rdfs:subPropertyOf sdmx-measure:obsValue;rdfs:range geo:point.
### Cube definition ###
ex:Accidents rdf:type qb:DataStructureDefinition;
### Lowest level for each dimension in the cube ###
qb:component [qb4o:level ex:highway, sdmx-dimension:refArea; 
  qb4o:cardinality qb4o:ManyToOne; 
  qb4so:topologicalRelation qb4so:Intersects].
### Spatial Measures in the Cube ###
qb:component[qb:measure ex:loc;qb4o:aggFunction qb4so:convexHull].  

			</pre>		
		
		
      </section>
    </div>
    <aside id="right_column">
      <?php include '../topics.html';?>
      <?php include 'resources.html';?>
      <!-- /nav -->
      <?php include 'conference.html';?>
      <?php include 'contact.html';?>
    </aside>

  </div>
</div>
<!-- footer -->
<div class="wrapper row3">
  <?php include '../footer.html';?>
  <?php include '../analytics.html';?>
</div>
</body>
</html>