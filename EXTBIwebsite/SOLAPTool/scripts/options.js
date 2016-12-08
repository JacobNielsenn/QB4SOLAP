/**
 * Created by Jacob on 07-12-2016.
 */
function Mes(ele){
    if (ele.classList.contains('hide')){
        ele.innerHTML = "Measures ON"
        $("#mes").addClass("show");
        $("#mes").removeClass("hide");
        additionalQuery = true;
        PComplete();
    }
    else {
        ele.innerHTML = "Measures OFF"
        $("#mes").addClass("hide");
        $("#mes").removeClass("show");
        additionalQuery = false;
        PComplete();
    }
}