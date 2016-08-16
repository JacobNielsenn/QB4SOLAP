<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="queries QB4SOLAP">
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
        <h2>Prefixes</h2>
          <pre>
### Default Data Set Name (GRAPH IRI)
prefix:gnwi: &lt;http://qb4solap.com/cubes/instances/geonorthwind#>
### Additional custom prefixes used in the queries
prefix:gnw:&lt;http://qb4solap.com/cubes/schema/geonorthwind#>
prefix geo:&lt;http://www.opengis.net/ont/geosparql#>
prefix skos:&lt;http://www.w3.org/2004/02/skos/core#>
prefix qb4o:&lt;http://purl.org/qb4olap/cubes#>
prefix qb4so:&lt;http://purl.org/qb4solap/cubes#>
prefix qb:&lt;http://purl.org/linked-data/cube#>		  

          </pre>

        </article>
		<article>
          <h3>Q1: Total sales to customers by city of the closest suppliers. </h3>
            <pre>

SELECT ?city (SUM(?sales) AS ?totalSales)
WHERE {?o a qb:Observation; gnw:customerID ?cust;
	gnw:supplierID ?sup; gnw:salesAmount ?sales. 
	?cust qb4o:inLevel gnw:customer;gnw:customerGeo ?custGeo;
	gnw:customerName ?custName; skos:broader ?city .
	?city qb4o:inLevel gnw:city.?sup gnw:supplierGeo ?supGeo.
#Inner Select:Distance to the closest supplier of the customer
  {SELECT ?cust1 (MIN(?distance) AS ?minDistance)
  WHERE{?o a qb:Observation; gnw:customerID ?cust1;
	gnw:supplierID ?sup1. ?sup1 gnw:supplierGeo ?sup1Geo.
	?cust1 gnw:customerGeo ?cust1Geo .
  BIND (bif:st_distance( ?cust1Geo, ?sup1Geo ) AS ?distance)}
  GROUP BY ?cust1 }
  FILTER (?cust = ?cust1 && bif:st_distance(?custGeo, ?supGeo)=
  ?minDistance)} GROUP BY ?city ORDER BY ?totalSales

			</pre>
        </article>
<article>
          <h3>Q2:  Total sales to the customers located in the city within a 10 km. buffer area from a given point. </h3>
            <pre>

SELECT ?custName ?cityName (SUM(?sales) AS ?totalSales)
WHERE {?o rdf:type qb:Observation; gnw:customerID ?cust; 
   gnw:salesAmount ?sales.?cust gnw:customerName ?custName; 
   skos:broader ?city.?city gnw:cityGeo ?cityGeo; 
	gnw:cityName ?cityName.
FILTER(bif:st_within(?cityGeo,bif:st_point(2.3522,48.856),10))}
GROUP BY ?custName ?cityName ORDER BY ?custName

			</pre>
        </article>
        <article>
        <article>
          <h3>Q3: Total  sales of customers in 1997, located in cities that are within a 100 km buffer area from a given point. </h3>
            <pre>

SELECT ?custName ?yearNo (SUM(?sales) AS ?totalSales)
WHERE { ?o rdf:type qb:Observation; gnw:customerID ?cust; 
   gnw:salesAmount ?sales; gnw:orderDateID ?time.
   ?time gnw:yearNo ?yearNo.?cust gnw:customerName ?custName; 
   skos:broader ?city. ?city gnw:cityGeo ?cityGeo. 
FILTER (?yearNo = "1997" && bif:st_within (?cityGeo, 
bif:st_point(2.3522219, 48.856614),  100))}
GROUP BY ?custName ?yearNo ORDER BY ?custName

			</pre>
        </article>
        <article>
          <h2>Q4 Total sales amount to the customers that have orders delivered by suppliers such that their locations are less than 200 km from each other.</h2>
            <pre>

SELECT ?custName ?distance (SUM(?sales) AS ?totalSales) 
WHERE { ?o a qb:Observation; 
	gnw:customerID ?cust; gnw:supplierID ?sup; 
	gnw:salesAmount ?sales. ?cust gnw:customerName ?custName; 
	gnw:customerGeo ?custGeo. ?sup gnw:supplierGeo ?supGeo. 
BIND (bif:st_distance (?custGeo, ?supGeo) AS ?distance)	
FILTER(?distance < 200 )}
GROUP BY ?custName ?distance ORDER BY ?custName


			</pre>
        </article>
        <article>
          <h2>Q5: Distance between the customers' locations and the center of the city which they are located</h2>
            <pre>

SELECT DISTINCT ?custName ?cityName ?distance 
WHERE { ?o a qb:Observation; gnw:customerID ?cust.
	?cust gnw:customerName ?custName; 
	gnw:customerGeo ?custGeo;skos:broader ?city. 
	?city gnw:cityGeo ?cityGeo; gnw:cityName ?cityName.
BIND (bif:st_distance (?cityGeo, ?custGeo) AS ?distance)}
ORDER BY ?custName
			</pre>
        </article>
        <article>
          <h2>Q6: Number of customers located at more than 100 km from the supplier. </h2>
            <pre>

SELECT ?supName, (COUNT(?cust) AS ?nbCustomers)
WHERE { ?o a qb:Observation; gnw:customerID ?cust;
	gnw:supplierID ?sup. ?cust gnw:customerGeo ?custGeo.
	?sup gnw:supplierGeo ?supGeo; gnw:supplierName ?supName.
FILTER (bif:st_distance(?supGeo, ?custGeo) > 100 ) }
GROUP BY ?supName ORDER BY DESC (?nbCustomers)
			</pre>
        </article>
        <article>
          <h2>Q7: For each customer, total sales amount to its closest supplier.</h2>
            <pre>

SELECT ?custName ?sup ?minDistance (SUM(?sales) AS ?totalSales)
WHERE {?o a qb:Observation; gnw:customerID ?cust;
	gnw:supplierID ?sup;gnw:salesAmount ?sales.
	?cust qb4o:inLevel gnw:customer;gnw:customerGeo ?custGeo;
	gnw:customerName ?custName. ?sup gnw:supplierGeo ?supGeo.
#Inner Select:Total sales to the closest supplier of the customer
  {SELECT ?cust1 (MIN(?distance) AS ?minDistance)
  WHERE{ ?o a qb:Observation; gnw:customerID ?cust1;
	gnw:supplierID ?sup1.?sup1 gnw:supplierGeo ?sup1Geo.
	?cust1 gnw:customerGeo ?cust1Geo .
	BIND (bif:st_distance(?cust1Geo,?sup1Geo) AS ?distance)}
	GROUP BY ?cust1}
FILTER (?cust = ?cust1 && bif:st_distance( ?custGeo, ?supGeo) = 
?minDistance)} GROUP BY ?custName ?minDistance ?sup
			</pre>
        </article>
        <article>
          <h2>Q8: Distance between the customer and supplier for customers that have orders delivered by suppliers of the same country.</h2>
            <pre>
 
SELECT ?custName ?supName ?distance
WHERE{?o a qb:Observation; gnw:customerID ?cust; 
   gnw:supplierID ?sup; gnw:salesAmount ?sales.
   ?cust gnw:customerName ?custName; gnw:customerGeo ?custGeo; 
   skos:broader ?custCity.{?custCity skos:broader ?custCountry.}
UNION {?custCity skos:broader ?custState.
   {?custState skos:broader ?custCountry.}
UNION {?custState skos:broader ?custRegion.
   ?custRegion skos:broader ?custCountry.}}
   ?sup gnw:supplierName ?supName; gnw:supplierGeo ?supGeo; 
   skos:broader ?supCity.{?supCity skos:broader ?supCountry.}
UNION {?supCity skos:broader ?supState.
   {?supState skos:broader ?supCountry.}
UNION {?supState skos:broader ?supRegion.
   ?supRegion skos:broader ?supCountry.}} 
BIND (bif:st_distance (?custGeo , ?supGeo) AS ?distance)
FILTER (?custCountry = ?supCountry)}
GROUP BY ?custName ?supName ?distance
			</pre>
        </article>
        <article>
          <h2>Q9: Total sales to customers located in a state that contains the capital city of the country.</h2>
            <pre>

SELECT ?custName (SUM(?sales) AS ?totalSales)
WHERE {?o rdf:type qb:Observation; gnw:customer ?cust; 
   gnw:salesAmount ?sales. ?cust gnw:customerName ?custName; 
   skos:broader ?city. ?city  skos:broader ?state.
   ?state gnw:stateGeo ?stateGeo.{?state skos:broader ?country.}
UNION {?state skos:broader ?region.
   ?region skos:broader ?country.} 
   ?country gnw:capitalGeo ?capitalGeo. 
FILTER ( bif:st_contains(?stateGeo, ?capitalGeo))}
GROUP BY ?custName ORDER BY ?custName
			</pre>
        </article>
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