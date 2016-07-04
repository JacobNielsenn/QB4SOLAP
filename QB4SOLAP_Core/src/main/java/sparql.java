import org.apache.jena.query.*;
import org.apache.jena.rdf.model.RDFNode;
import org.apache.jena.sparql.engine.http.QueryEngineHTTP;
import org.apache.jena.vocabulary.RDF;
import virtuoso.jena.driver.VirtGraph;
import virtuoso.jena.driver.VirtuosoQueryExecution;
import virtuoso.jena.driver.VirtuosoQueryExecutionFactory;

/**
 * Created by Jacob on 29/06/2016.
 */
public class sparql {
    public ResultSet query(String input) {
        VirtGraph set = new VirtGraph("jdbc:virtuoso://localhost:1111", "dba", "dba");
        VirtuosoQueryExecution vqe = VirtuosoQueryExecutionFactory.create(input, set);
        ResultSet results = vqe.execSelect();
        return results;
    }
}

