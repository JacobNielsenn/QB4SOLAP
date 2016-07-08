package restfully;

import javax.jws.WebMethod;
import javax.jws.WebService;
import javax.jws.soap.SOAPBinding;

/**
 * Created by Jacob on 08/07/2016.
 */
@WebService
@SOAPBinding(style = SOAPBinding.Style.RPC)
public interface HejVerden {
    @WebMethod
    String getHejVerdenAsString(String name);
}
