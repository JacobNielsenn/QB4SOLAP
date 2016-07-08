package restfully;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.sun.jersey.api.NotFoundException;
import com.sun.org.apache.xerces.internal.util.URI;
import com.sun.xml.internal.ws.api.server.Container;

import javax.ws.rs.*;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;

@Path("/{input}")
public class HelloWorld {

    public static <T> String ConvertToJson(T classOfT){
        Gson gson = new GsonBuilder().create();
        return gson.toJson(classOfT);
    }

    @GET
    @Produces(MediaType.TEXT_PLAIN)
    public String getMessage(@PathParam("input") String test) {
        return test;
    }

}