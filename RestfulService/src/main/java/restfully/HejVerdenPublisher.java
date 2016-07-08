package restfully;

import javax.xml.ws.Endpoint;

/**
 * Created by Jacob on 08/07/2016.
 */
public class HejVerdenPublisher {

    public static void main(String[] args){
        Endpoint.publish("http://localhost:8080/ws/hello", new HejVerdenImpl());
    }
}
