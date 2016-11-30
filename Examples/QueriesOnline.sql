-- Additional custom prefixes used in the queries:
prefix qb: <http://purl.org/linked-data/cube#>
prefix gnwi: <http://qb4solap.org/cubes/instances/geonorthwind#> 
prefix gnw: <http://qb4solap.org/cubes/schemas/geonorthwind#> 
prefix geo: <http://www.opengis.net/ont/geosparql#> 
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix qb4o: <http://purl.org/qb4solap/cubes#> 


-- Default Data Set Name (Graph IRI)
http://qb4solap.org/cubes/instances/geonorthwind#

http://164.15.78.30:8890/conductor/
http://164.15.78.30:8890/sparql



-- Query: S-Roll-UP : total sales to customers by city of the closest suppliers

SELECT ?city (SUM(?sales) AS ?totalSales)
WHERE { 
?o a qb:Observation; 
	gnw:customerID ?cust ;
	gnw:supplierID ?sup;gnw:salesAmount ?sales.
?cust qb4o:inLevel gnw:customer;
	gnw:customerGeo ?custGeo;
	gnw:customerName ?custName;
        skos:broader ?city .
?city qb4o:inLevel gnw:city.
?sup gnw:supplierGeo ?supGeo .
# Inner Select for the total sales to the closest supplier of the customer
{ SELECT ?cust1 (MIN(?distance) AS ?minDistance)
WHERE { 
?o a qb:Observation; 
	gnw:customerID ?cust1;
	gnw:supplierID ?sup1. 
?sup1 gnw:supplierGeo ?sup1Geo.
?cust1 gnw:customerGeo ?cust1Geo .
BIND (bif:st_distance( ?cust1Geo, ?sup1Geo ) AS ?distance)}
GROUP BY ?cust1 }
FILTER (?cust = ?cust1 && bif:st_distance( ?custGeo, ?supGeo ) = ?minDistance)}
GROUP BY ?city



--Query 1. S-Slice : Total sales to the customers located in cities that are within a 100 km. buffer area from a given point.
SELECT ?custName (SUM(?sales) AS ?totalSales)
WHERE { 
?o rdf:type qb:Observation;
	gnw:customerID ?cust; 
	gnw:salesAmount ?sales.
?cust gnw:customerName ?custName; 
	skos:broader ?city.
?city gnw:cityGeo ?cityGeo; 
	gnw:cityName ?cityName.
FILTER (bif:st_intersects (?cityGeo, bif:st_point(2.3522219, 48.856614), 100))}
GROUP BY ?custName
ORDER BY ?custName

--Query 2. S-Slice: Total  sales of customers in 1997, located in cities that are within a 100 km buffer area from a given point.

SELECT ?custName ?yearNo (SUM(?sales) AS ?totalSales)
WHERE { 
?o rdf:type qb:Observation ; 
  gnw:customerID ?cust ; 
  gnw:salesAmount ?sales ;
  gnw:orderDateID ?time.
?time gnw:yearNo ?yearNo.
?cust gnw:customerName ?custName ; 
  skos:broader ?city .
?city gnw:cityGeo ?cityGeo . 
FILTER (?yearNo = "1997" && bif:st_within (?cityGeo, bif:st_point(2.3522219, 48.856614),  100)) 
}
GROUP BY ?custName ?yearNo
ORDER BY ?custName

--Query 3. S-??:Distance between the customers’ locations and the center of the city which they are located

SELECT DISTINCT ?custName ?cityName ?distance 
WHERE { 
?o a qb:Observation; 
   gnw:customerID ?cust .
?cust gnw:customerName ?custName ; 
   gnw:customerGeo ?custGeo ; 
  skos:broader ?city .
?city gnw:cityGeo ?cityGeo ;
    gnw:cityName ?cityName.
BIND (bif:st_distance (?cityGeo, ?custGeo ) AS ?distance) }
ORDER BY ?custName 

-- Query 4. S-Dice: Total sales amount to the customers that have orders delivered by suppliers such that their locations are less than 200 km from each other.

SELECT ?custName ?distance (SUM(?sales) AS ?totalSales)
WHERE {
?o a qb:Observation ; 
	gnw:customerID ?cust ;
	gnw:supplierID ?sup; 
	gnw:salesAmount ?sales.
?cust gnw:customerName ?custName; 
	gnw:customerGeo ?custGeo .
?sup gnw:supplierGeo ?supGeo .
BIND (bif:st_distance (?custGeo, ?supGeo) AS ?distance)
FILTER ( ?distance < 200 ) }
GROUP BY ?custName ?distance
ORDER BY ?custName

-- Query 5. S-??: For each supplier, number of customers located at more than 100 km from the supplier.

SELECT ?supName, (COUNT(?cust) AS ?nbCustomers)
WHERE { 
?o a qb:Observation; 
	gnw:customerID ?cust;
	gnw:supplierID ?sup. 
?cust gnw:customerGeo ?custGeo.
	?sup gnw:supplierGeo ?supGeo; 
	gnw:supplierName ?supName.
FILTER (bif:st_distance(?supGeo, ?custGeo) > 100 ) }
GROUP BY ?supName
ORDER BY DESC (?nbCustomers)

-- Query 6. S- ??: For each customer, total sales amount to its closest supplier

SELECT ?custName ?sup ?minDistance (SUM(?sales) AS ?totalSales)
WHERE { 
?o a qb:Observation; 
	gnw:customerID ?cust ;
	gnw:supplierID ?sup;gnw:salesAmount ?sales.
?cust qb4o:inLevel gnw:customer;
	gnw:customerGeo ?custGeo;
	gnw:customerName ?custName.
?sup gnw:supplierGeo ?supGeo .
# Inner Select for the total sales to the closest supplier of the customer
{ SELECT ?cust1 (MIN(?distance) AS ?minDistance)
WHERE { 
?o a qb:Observation; 
	gnw:customerID ?cust1;
	gnw:supplierID ?sup1. 
?sup1 gnw:supplierGeo ?sup1Geo.
?cust1 gnw:customerGeo ?cust1Geo .
BIND (bif:st_distance( ?cust1Geo, ?sup1Geo ) AS ?distance)}
GROUP BY ?cust1 }
FILTER (?cust = ?cust1 && bif:st_distance( ?custGeo, ?supGeo ) = ?minDistance)}
GROUP BY ?custName ?minDistance ?sup

-- Query 7. S-??: Distance between the customer and supplier for customers that have orders delivered by suppliers of the same country.

SELECT ?custName ?supName ?distance
WHERE { 
	?o a qb:Observation ; 
		gnw:customerID ?cust ; 
		gnw:supplierID ?sup ; 
		gnw:salesAmount ?sales .
	?cust gnw:customerName ?custName ; 
		gnw:customerGeo ?custGeo ; 
		skos:broader ?custCity .
{ ?custCity skos:broader ?custCountry . }
UNION
{ 
	?custCity skos:broader ?custState .
{ 	?custState skos:broader ?custCountry . }
UNION
{ 
	?custState skos:broader ?custRegion .
	?custRegion skos:broader ?custCountry . } 
}
	?sup gnw:supplierName ?supName ; 
		gnw:supplierGeo ?supGeo ; 
		skos:broader ?supCity .
{ ?supCity skos:broader ?supCountry . }
UNION
{ 
	?supCity skos:broader ?supState .
{ 	?supState skos:broader ?supCountry . }
UNION
{ 
	?supState skos:broader ?supRegion .
	?supRegion skos:broader ?supCountry . } 
} 
BIND (bif:st_distance (?custGeo , ?supGeo) AS ?distance)
FILTER ( ?custCountry = ?supCountry )
}
GROUP BY ?custName ?supName ?distance


-- Query 8. S-??: Total sales to customers located in a state that contains the capital city of the country.
--Query is possible though returning null - missing states

SELECT ?custName (SUM(?sales) AS ?totalSales)
WHERE { 
	?o rdf:type qb:Observation ; 
		gnw:customer ?cust ; 
		gnw:salesAmount ?sales .
	?cust gnw:customerName ?custName ; 
		skos:broader ?city .
	?city  skos:broader ?state .
	?state gnw:stateGeo ?stateGeo .
	{?state skos:broader ?country .}
UNION 
	{ ?state skos:broader ?region .
	?region skos:broader ?country . } 
	?country gnw:capitalGeo ?capitalGeo . 
FILTER ( bif:st_contains(?stateGeo, ?capitalGeo) ) }
GROUP BY ?custName
ORDER BY ?custName

-- Query 9. S-Union ??: Spatial union of the states in the USA where at least one customer placed an order in 1997.
--Query is not possible though the correct SPARQL syntax is given as below with a notional bif:st_union function

SELECT (bif:st_union(?stateGeo) AS ?stateUnion)
WHERE { 
	?o a qb:Observation ; 
		gnw:customerID ?cust ; 
		gnw:orderDateID ?orderDate.
	?cust skos:broader ?city .
	?city skos:broader ?state .
	?state gnw:stateGeo ?stateGeo . 
{ 	?state skos:broader ?country . }
UNION
{ 	?state skos:broader ?region . 
	?region skos:broader ?country . } 
?country gnw:countryName ?countryName .
?orderDate skos:broader ?month .
?month skos:broader ?quarter .
?quarter skos:broader ?semester .
?semester skos:broader ?year . 
FILTER ( ?countryName = 'United States' && ?yearNo = 1997 ) }


-- Query 10. S- ??: Number of customers from European countries with an area larger than 50,000 km2.
--Query is not possible with area function - precalculation of the areas needed.

SELECT ?countryName, (COUNT(?cust) AS ?nbCustomers)
WHERE { 
	?o a qb:Observation ; 
		gnw:customerID ?cust .
	?cust skos:broader ?city .
{ ?city skos:broader ?country . }
UNION
{ 
	?city skos:broader ?state .
{ 	?state skos:broader ?country . }
UNION
{ 
	?state skos:broader ?region .
	?region skos:broader ?country . } 
}
	?country gnw:countryName ?countryName ; 
		gnw:area ?countryArea ; 
		skos:broader ?continent . 
	?continent gnw:continentName ?continentName . 
FILTER ( ?continentName = 'Europe' && ?countryArea > 5 )
}
GROUP BY ?countryName

-- Query 11. S- ??: For each supplier, distance between the location of the supplier and the centroid of the locations of all its customers.
--Query is not possible though the correct SPARQL syntax is given as below with a notional bif:st_centroid and bif:st_union function

SELECT ?supName, ?distance 
WHERE { 
	?o a qb:Observation ; 
		gnw:customerID ?cust ; 
		gnw:supplierID ?sup .
	?cust gnw:customerGeo ?custGeo .
	?sup gnw:supplierGeo ?supGeo .
BIND (bif:st_centroid(bif:st_union(?custGeo)) AS ?centerCustGeo)	
BIND (bif:st_distance(?supGeo , ?centerCustGeo) AS ?distance)
}	
GROUP BY ?supName ?distance




-- Spatial Roll-up example : Total sales to the customers by city of the closest supplier

-- Spatial Drill-down example: 

-- Spatial Slice example: Total sales of the largest state in U.S.

-- Spatial Dice example: Total sales to customers that are located less than 5 km. from their city center

-- Spatial Aggregation example: Total sales and convex hull area of customer locations by city

-- Spatial Drill-across example : Total sales by country compared with those of its bordering countries

-- Spatial Union example: 

-- Spatial Difference example: 







--------------------------------------------------------------------------------------------
SELECT ?custName ?cityName(SUM(?sales) AS ?totalSales)
WHERE { 
?o rdf:type qb:Observation ; 
  gnw:customerID ?cust ; 
  gnw:salesAmount ?sales .
?cust gnw:customerName ?custName ; 
  skos:broader ?city .
?city gnw:cityGeo ?cityGeo ;
  gnw:cityName ?cityName .
FILTER (bif:st_geomfromtext( "POLYGON((200.0 50.0, 300.0 50.0, 300.0 80.0, 200.0 80.0, 200.0 50.0))"))
}
GROUP BY ?custName ?cityName
ORDER BY ?custName



prefix qb: <http://purl.org/linked-data/cube#>
prefix gnwi: <http://qb4solap.org/cubes/instances/geonorthwind#> 
prefix gnw: <http://qb4solap.org/cubes/schemas/geonorthwind#> 
prefix geo: <http://www.opengis.net/ont/geosparql#> 
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix qb4o: <http://purl.org/qb4solap/cubes#> 

SELECT ?custName ?cityName ?cityGeo(SUM(?sales) AS ?totalSales)
WHERE { 
?o rdf:type qb:Observation ; 
  gnw:customerID ?cust ; 
  gnw:salesAmount ?sales .
?cust gnw:customerName ?custName ; 
  skos:broader ?city .
?city gnw:cityGeo ?cityGeo ;
  gnw:cityName ?cityName .
#FILTER (bif:st_intersects (?cityGeo, bif:st_point(-99.1332 , 19.4326), 1)) 
#FILTER (bif:st_intersects (?cityGeo, bif:st_point(2.3522219, 48.856614), 110)) 
#FILTER (regex(?cityName,'London'))
FILTER (bif:st_intersects (?cityGeo, bif:st_polygon((200.0 50.0, 300.0 50.0, 300.0 80.0, 200.0 80.0, 200.0 50.0))))
}

GROUP BY ?custName ?cityName ?cityGeo 
#ORDER BY ?custName


--localhost endpoint

prefix qb: <http://purl.org/linked-data/cube#>
prefix gnwi: <http://qb4solap.org/cubes/instances/geonorthwind#> 
prefix gnw: <http://qb4solap.org/cubes/schemas/geonorthwind#> 
prefix geo: <http://www.opengis.net/ont/geosparql#> 
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix qb4o: <http://purl.org/qb4solap/cubes#> 


SELECT ?custName ?cityName ?cityGeo(SUM(?sales) AS ?totalSales)
WHERE { 
?o rdf:type qb:Observation ; 
  gnw:customerID ?cust ; 
  gnw:salesAmount ?sales .
?cust gnw:companyName ?custName ; 
  skos:broader ?city .
?city gnw:cityGeo ?cityGeo ;
  gnw:cityName ?cityName .
#FILTER (bif:st_intersects (?cityGeo, bif:st_point(-0.1198244, 51.5112139), 200))
FILTER (bif:st_intersects (?cityGeo, bif:st_point(-99.1332 , 19.4326), 0.1))  
#FILTER (bif:st_intersects (?cityGeo, bif:stpoint(2.3522219, 48.856614), 18)) 
#FILTER (bif:st_intersects (?cityGeo, bif:st_geomfromtext( "POLYGON((200.0 50.0, 300.0 50.0, 300.0 80.0, 200.0 80.0, 200.0 50.0))"))) 

}

GROUP BY ?custName ?cityName ?cityGeo
ORDER BY ?custName


SELECT ?custName ?supName ?distance
WHERE { ?o a qb:Observation; gnw:customerID ?cust;
gnw:supplierID ?sup; gnw:salesAmount ?sales.
?cust gnw:customerName ?custName; skos:broader ?custCity
{?custCity skos:broader ?custCountry. }
UNION { ?custCity skos:broader ?custState.
{?custState skos:broader ?custCountry. }}
UNION { ?custState skos:broader ?custRegion.
{?custRegion skos:broader ?custCountry. }}
?sup gnw:supplierName ?supName; gnw:supplierGeo ?supGeo;
skos:broader ?supCity. { ?supCity skos:broader ?supCountry. }
UNION { ?supCity skos:broader ?supState.
{?supState skos:broader ?supCountry. }}
UNION { ?supState skos:broader ?supRegion.
{?supRegion skos:broader ?supCountry. }}
BIND (bif:st_distance (?custGeo , ?supGeo) AS ?distance)
FILTER (?custCountry = ?supCountry) }
GROUP BY ?custName ?supName ?distance