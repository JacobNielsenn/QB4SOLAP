/**
 * Created by Jacob on 07-12-2016.
 */
function Mes(ele){
    if (ele.classList.contains('hide')){
        ele.innerHTML = "Disaggregate"
        $("#mes").addClass("show");
        $("#mes").removeClass("hide");
        additionalQuery = true;
        PComplete();
        runQuery();
    }
    else {
        ele.innerHTML = "Aggregate"
        $("#mes").addClass("hide");
        $("#mes").removeClass("show");
        additionalQuery = false;
        PComplete();
        runQuery();
    }
}