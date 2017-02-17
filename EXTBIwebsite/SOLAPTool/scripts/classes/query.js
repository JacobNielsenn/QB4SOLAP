/**
 * Created by JN on 14/02/2017.
 */
class Query{
    constructor(){
        this.opertorList = [];
    }

    add(operator){
        if (operator.constructor == Operator){
            this.opertorList.push(operator);
        }
        else{
            alert("Query could not add constructor to list, see log for more info");
            console.log("Add operator to Query list failed.", operator);
        }
    }

    get mainOperator(){
        return this.opertorList[0];
    }

    get check(){
        //Tell the user if the nesting is incorrect.
        var countInList = 2;
        if (this.opertorList.length = 0){ //No operator in list.
            return false;
        }
        else if (this.opertorList.length = 1){ //Only one operator in list.
            return true;
        }
        while (countInList != this.opertorList.length){
            switch (this.opertorList[countInList].constructor){
                case(ODice):
                    if (this.opertorList[countInList-1].constructor == ODice){
                        return false;
                    }
                    break;
                case(OSlice):
                    break;
                case(OSRU):
                    break;
                default:
                    return false;
            }
            countInList++;
        }
        return true;
    }

    get returnQuery(){
        return "entire query nested or not.";
    }
}