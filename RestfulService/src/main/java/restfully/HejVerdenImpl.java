package restfully;

import javax.jws.WebService;

/**
 * Created by Jacob on 08/07/2016.
 */
@WebService(endpointInterface = "restfully.HejVerden")
public class HejVerdenImpl implements HejVerden {

    public String getHejVerdenAsString(String name) {
        return "Hello World JAX-WS " + name;
    }
}
