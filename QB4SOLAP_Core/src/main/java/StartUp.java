import org.apache.jena.query.Query;
import org.apache.jena.query.QueryFactory;
import org.apache.jena.query.QuerySolution;
import org.apache.jena.query.ResultSet;
import org.apache.jena.rdf.model.RDFNode;
import virtuoso.jena.driver.VirtGraph;
import virtuoso.jena.driver.VirtuosoQueryExecution;
import virtuoso.jena.driver.VirtuosoQueryExecutionFactory;

/**
 * Created by JacobN on 25-06-2016.
 */
public class StartUp {
    public static void main(String[] args) {

/*			STEP 1			*/
        VirtGraph set = new VirtGraph ("jdbc:virtuoso://localhost:1111", "dba", "dba");

/*			STEP 2			*/


/*			STEP 3			*/
/*		Select all data in virtuoso	*/
        Query sparql = QueryFactory.create("SELECT * WHERE { GRAPH ?graph { ?s ?p ?o } } limit 100");

/*			STEP 4			*/
        VirtuosoQueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, set);

        ResultSet results = vqe.execSelect();
        while (results.hasNext()) {
            QuerySolution result = results.nextSolution();
            RDFNode graph = result.get("graph");
            RDFNode s = result.get("s");
            RDFNode p = result.get("p");
            RDFNode o = result.get("o");
            System.out.println(graph + " { " + s + " " + p + " " + o + " . }" + set.getCount());
        }
    }
}
