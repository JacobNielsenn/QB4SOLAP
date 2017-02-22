/**
 * Created by JN on 21/02/2017.
 */
class Levels{
    constructor(string){
        var split = string.split(',');
        this.startLevel = split[2];
        this.endLevel = split[1];
        this.levels = [];
        var tmp = traverse(DataStructureDefinition.dimension, this.startLevel, '0');
        var inlevel = false;
        for (var level in tmp){
            if (this.endLevel == tmp[level]){
                inlevel = false;
                this.levels.push(tmp[level]);
            }
            else if (this.startLevel == tmp[level]){
                inlevel = true;
                this.levels.push(tmp[level]);
            }
            else if (inlevel){
                this.levels.push(tmp[level]);
            }
        }
    }

    get returnLevels(){
        return this.levels;
    };
}