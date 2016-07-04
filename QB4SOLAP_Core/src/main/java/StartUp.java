
import org.apache.jena.base.Sys;
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
    public static void main(String[] args)
    {
        System.out.println("Hello Website from main");
        if (args[0].equals("1"))
        {
            System.out.println(test());
        }

    }

    public static String test(){
        return "Hello Website from test method";
    }
}

