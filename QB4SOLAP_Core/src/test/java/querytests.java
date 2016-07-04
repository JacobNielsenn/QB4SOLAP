import static org.junit.Assert.assertEquals;

import org.apache.jena.base.Sys;
import org.apache.jena.query.QuerySolution;
import org.apache.jena.query.ResultSet;
import org.apache.jena.rdf.model.RDFNode;
import org.junit.Before;
import org.junit.Test;
/**
 * Created by Jacob on 29/06/2016.
 */
public class querytests {
    String prefix = "prefix qb: <http://purl.org/linked-data/cube#>\n" +
            "prefix gnwi: <http://qb4solap.org/cubes/instances/geonorthwind#> \n" +
            "prefix gnw: <http://qb4solap.org/cubes/schemas/geonorthwind#> \n" +
            "prefix geo: <http://www.opengis.net/ont/geosparql#> \n" +
            "prefix skos: <http://www.w3.org/2004/02/skos/core#>\n" +
            "prefix qb4o: <http://purl.org/qb4solap/cubes#> ";

    // Query 1. S-Slice : Total sales to the customers located in cities that are within a 100 km. buffer area from a given point.
    @Test
    public void Q1_S_Slice(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?custName (SUM(?sales) AS ?totalSales) " +
                "WHERE { " +
                "GRAPH ?graph { " +
                "?o rdf:type qb:Observation;\n" +
                "\tgnw:customerID ?cust; \n" +
                "\tgnw:salesAmount ?sales.\n" +
                "?cust gnw:customerName ?custName; \n" +
                "\tskos:broader ?city.\n" +
                "?city gnw:cityGeo ?cityGeo; \n" +
                "\tgnw:cityName ?cityName.\n" +
                "FILTER (bif:st_intersects (?cityGeo, bif:st_point(2.3522219, 48.856614), 100)) } } " +
                "GROUP BY ?custName\n" +
                "ORDER BY ?custName");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(2, count);
    }

    //Query 2. S-Slice: Total  sales of customers in 1997, located in cities that are within a 100 km buffer area from a given point.
    @Test
    public void Q2_S_Slice(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?custName ?yearNo (SUM(?sales) AS ?totalSales)\n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "?o rdf:type qb:Observation ; \n" +
                "  gnw:customerID ?cust ; \n" +
                "  gnw:salesAmount ?sales ;\n" +
                "  gnw:orderDateID ?time.\n" +
                "?time gnw:yearNo ?yearNo.\n" +
                "?cust gnw:customerName ?custName ; \n" +
                "  skos:broader ?city .\n" +
                "?city gnw:cityGeo ?cityGeo . \n" +
                "FILTER (?yearNo = \"1997\" && bif:st_within (?cityGeo, bif:st_point(2.3522219, 48.856614),  100)) \n" +
                "} }\n" +
                "GROUP BY ?custName ?yearNo\n" +
                "ORDER BY ?custName");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(1, count);
    }

    //Query 3. S-??:Distance between the customers’ locations and the center of the city which they are located
    @Test
    public void Q3(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT DISTINCT ?custName ?cityName ?distance \n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "?o a qb:Observation; \n" +
                "   gnw:customerID ?cust .\n" +
                "?cust gnw:customerName ?custName ; \n" +
                "   gnw:customerGeo ?custGeo ; \n" +
                "  skos:broader ?city .\n" +
                "?city gnw:cityGeo ?cityGeo ;\n" +
                "    gnw:cityName ?cityName.\n" +
                "BIND (bif:st_distance (?cityGeo, ?custGeo ) AS ?distance) } }\n" +
                "ORDER BY ?custName");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(87, count);
    }

    //Query 4. S-Dice: Total sales amount to the customers that have orders delivered by suppliers such that their locations are less than 200 km from each other.
    @Test
    public void Q4_S_Dice(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?custName ?distance (SUM(?sales) AS ?totalSales)\n" +
                "WHERE {\n" +
                "GRAPH ?graph { " +
                "?o a qb:Observation ; \n" +
                "\tgnw:customerID ?cust ;\n" +
                "\tgnw:supplierID ?sup; \n" +
                "\tgnw:salesAmount ?sales.\n" +
                "?cust gnw:customerName ?custName; \n" +
                "\tgnw:customerGeo ?custGeo .\n" +
                "?sup gnw:supplierGeo ?supGeo .\n" +
                "BIND (bif:st_distance (?custGeo, ?supGeo) AS ?distance)\n" +
                "FILTER ( ?distance < 200 ) } }\n" +
                "GROUP BY ?custName ?distance\n" +
                "ORDER BY ?custName");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(27, count);
    }

    //Query 5. S-??: For each supplier, number of customers located at more than 100 km from the supplier.
    @Test
    public void Q5(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?supName, (COUNT(?cust) AS ?nbCustomers)\n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "?o a qb:Observation; \n" +
                "\tgnw:customerID ?cust;\n" +
                "\tgnw:supplierID ?sup. \n" +
                "?cust gnw:customerGeo ?custGeo.\n" +
                "\t?sup gnw:supplierGeo ?supGeo; \n" +
                "\tgnw:supplierName ?supName.\n" +
                "FILTER (bif:st_distance(?supGeo, ?custGeo) > 100 ) } }\n" +
                "GROUP BY ?supName\n" +
                "ORDER BY DESC (?nbCustomers)");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(28, count);
    }

    //Query 6. S- ??: For each customer, total sales amount to its closest supplier
    @Test
    public void Q6(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?custName ?sup ?minDistance (SUM(?sales) AS ?totalSales)\n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "?o a qb:Observation; \n" +
                "\tgnw:customerID ?cust ;\n" +
                "\tgnw:supplierID ?sup;gnw:salesAmount ?sales.\n" +
                "?cust qb4o:inLevel gnw:customer;\n" +
                "\tgnw:customerGeo ?custGeo;\n" +
                "\tgnw:customerName ?custName.\n" +
                "?sup gnw:supplierGeo ?supGeo .\n" +
                "{ SELECT ?cust1 (MIN(?distance) AS ?minDistance)\n" +
                "WHERE { \n" +
                "?o a qb:Observation; \n" +
                "\tgnw:customerID ?cust1;\n" +
                "\tgnw:supplierID ?sup1. \n" +
                "?sup1 gnw:supplierGeo ?sup1Geo.\n" +
                "?cust1 gnw:customerGeo ?cust1Geo .\n" +
                "BIND (bif:st_distance( ?cust1Geo, ?sup1Geo ) AS ?distance)}\n" +
                "GROUP BY ?cust1 }\n" +
                "FILTER (?cust = ?cust1 && bif:st_distance( ?custGeo, ?supGeo ) = ?minDistance)} }\n" +
                "GROUP BY ?custName ?minDistance ?sup");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(0, count);
    }

    //Query 7. S-??: Distance between the customer and supplier for customers that have orders delivered by suppliers of the same country.
    @Test
    public void Q7(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?custName ?supName ?distance\n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "\t?o a qb:Observation ; \n" +
                "\t\tgnw:customerID ?cust ; \n" +
                "\t\tgnw:supplierID ?sup ; \n" +
                "\t\tgnw:salesAmount ?sales .\n" +
                "\t?cust gnw:customerName ?custName ; \n" +
                "\t\tgnw:customerGeo ?custGeo ; \n" +
                "\t\tskos:broader ?custCity .\n" +
                "{ ?custCity skos:broader ?custCountry . }\n" +
                "UNION\n" +
                "{ \n" +
                "\t?custCity skos:broader ?custState .\n" +
                "{ \t?custState skos:broader ?custCountry . }\n" +
                "UNION\n" +
                "{ \n" +
                "\t?custState skos:broader ?custRegion .\n" +
                "\t?custRegion skos:broader ?custCountry . } \n" +
                "}\n" +
                "\t?sup gnw:supplierName ?supName ; \n" +
                "\t\tgnw:supplierGeo ?supGeo ; \n" +
                "\t\tskos:broader ?supCity .\n" +
                "{ ?supCity skos:broader ?supCountry . }\n" +
                "UNION\n" +
                "{ \n" +
                "\t?supCity skos:broader ?supState .\n" +
                "{ \t?supState skos:broader ?supCountry . }\n" +
                "UNION\n" +
                "{ \n" +
                "\t?supState skos:broader ?supRegion .\n" +
                "\t?supRegion skos:broader ?supCountry . } \n" +
                "} \n" +
                "BIND (bif:st_distance (?custGeo , ?supGeo) AS ?distance)\n" +
                "FILTER ( ?custCountry = ?supCountry )\n" +
                "} }\n" +
                "GROUP BY ?custName ?supName ?distance");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(156, count);
    }

    //Query 8. S-??: Total sales to customers located in a state that contains the capital city of the country.
    @Test
    public void Q8(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?custName (SUM(?sales) AS ?totalSales)\n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "\t?o rdf:type qb:Observation ; \n" +
                "\t\tgnw:customer ?cust ; \n" +
                "\t\tgnw:salesAmount ?sales .\n" +
                "\t?cust gnw:customerName ?custName ; \n" +
                "\t\tskos:broader ?city .\n" +
                "\t?city  skos:broader ?state .\n" +
                "\t?state gnw:stateGeo ?stateGeo .\n" +
                "\t{?state skos:broader ?country .}\n" +
                "UNION \n" +
                "\t{ ?state skos:broader ?region .\n" +
                "\t?region skos:broader ?country . } \n" +
                "\t?country gnw:capitalGeo ?capitalGeo . \n" +
                "FILTER ( bif:st_contains(?stateGeo, ?capitalGeo) ) } }\n" +
                "GROUP BY ?custName\n" +
                "ORDER BY ?custName");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(0, count);
    }

    //Query 9. S-Union ??: Spatial union of the states in the USA where at least one customer placed an order in 1997.
    @Test
    public void Q9_S_Union(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT (bif:st_union(?stateGeo) AS ?stateUnion)\n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "\t?o a qb:Observation ; \n" +
                "\t\tgnw:customerID ?cust ; \n" +
                "\t\tgnw:orderDateID ?orderDate.\n" +
                "\t?cust skos:broader ?city .\n" +
                "\t?city skos:broader ?state .\n" +
                "\t?state gnw:stateGeo ?stateGeo . \n" +
                "{ \t?state skos:broader ?country . }\n" +
                "UNION\n" +
                "{ \t?state skos:broader ?region . \n" +
                "\t?region skos:broader ?country . } \n" +
                "?country gnw:countryName ?countryName .\n" +
                "?orderDate skos:broader ?month .\n" +
                "?month skos:broader ?quarter .\n" +
                "?quarter skos:broader ?semester .\n" +
                "?semester skos:broader ?year . \n" +
                "FILTER ( ?countryName = 'United States' && ?yearNo = 1997 ) } }");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(0, count);
    }

    //Query 10. S- ??: Number of customers from European countries with an area larger than 50,000 km2.
    @Test
    public void Q10(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?countryName, (COUNT(?cust) AS ?nbCustomers)\n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "\t?o a qb:Observation ; \n" +
                "\t\tgnw:customerID ?cust .\n" +
                "\t?cust skos:broader ?city .\n" +
                "{ ?city skos:broader ?country . }\n" +
                "UNION\n" +
                "{ \n" +
                "\t?city skos:broader ?state .\n" +
                "{ \t?state skos:broader ?country . }\n" +
                "UNION\n" +
                "{ \n" +
                "\t?state skos:broader ?region .\n" +
                "\t?region skos:broader ?country . } \n" +
                "}\n" +
                "\t?country gnw:countryName ?countryName ; \n" +
                "\t\tgnw:area ?countryArea ; \n" +
                "\t\tskos:broader ?continent . \n" +
                "\t?continent gnw:continentName ?continentName . \n" +
                "FILTER ( ?continentName = 'Europe' && ?countryArea > 5 )\n" +
                "} }\n" +
                "GROUP BY ?countryName");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(7, count);
    }

    //Query 11. S- ??: For each supplier, distance between the location of the supplier and the centroid of the locations of all its customers.
    @Test
    public void Q11(){
        sparql Sparql = new sparql();
        ResultSet resultSet = Sparql.query(prefix + "SELECT ?supName, ?distance \n" +
                "WHERE { \n" +
                "GRAPH ?graph { " +
                "\t?o a qb:Observation ; \n" +
                "\t\tgnw:customerID ?cust ; \n" +
                "\t\tgnw:supplierID ?sup .\n" +
                "\t?cust gnw:customerGeo ?custGeo .\n" +
                "\t?sup gnw:supplierGeo ?supGeo .\n" +
                "BIND (bif:st_centroid(bif:st_union(?custGeo)) AS ?centerCustGeo)\t\n" +
                "BIND (bif:st_distance(?supGeo , ?centerCustGeo) AS ?distance)\n" +
                "} }\t\n" +
                "GROUP BY ?supName ?distance");
        int count = 0;
        while(resultSet.hasNext()){
            count++;
            resultSet.nextSolution();
        }
        assertEquals(0, count);
    }
}
