/**
 * Created by JN on 21/02/2017.
 */
class Path{
    constructor(string){
        var split = string.split(',');
        this.startLevel = split[2];
        this.endLevel = split[1];
        this.attribute = split[0];
    }

    returnStartLevel(){
        return this.startLevel;
    }
    returnEndLevel(){
        return this.endLevel;
    }
    returnAttribute(){
        return this.attribute;
    }
}