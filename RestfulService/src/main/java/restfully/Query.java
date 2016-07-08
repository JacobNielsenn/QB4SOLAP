package restfully;

import com.google.gson.annotations.Expose;

import javax.annotation.Generated;

/**
 * Created by Jacob on 06/07/2016.
 */
@Generated("org.jsonschema2pojo")
public class Query {
    @Expose
    private String string;

    public void SetString(String string){
        this.string = string;
    }

    public Query GetString(String string){
        return this;
    }
}
