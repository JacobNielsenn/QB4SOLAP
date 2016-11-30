/**
 * Created by Jacob on 17-11-2016.
 */
//Basic html component to minimize code everywhere else in the project.
function InsertP(name, minheight, minwidth){
    var P = document.createElement('p');
    var id = UpdateID('p');
    P.setAttribute('id', id);
    P.setAttribute('name', name);
    P.setAttribute('style','margin-top: 0px; margin-bottom: 0px; min-height:' + minheight + 'px; min-width:' + minwidth + 'px;');
    return P;
}
function InsertDiv(){
    var div = document.createElement('div');
    var id = UpdateID('div');
    div.setAttribute('id', id);
    div.setAttribute('name', 'div'+id);
    return div;
}
function InsertBR(){
    var br = document.createElement('br');
    var id = UpdateID('br');
    br.setAttribute('id', id);
    br.setAttribute('name', 'br'+id);
    return br;
}
function InsertTextBox(title){
    var TextBox = document.createElement('text');
    var id = UpdateID('p');
    TextBox.setAttribute('name', 'textbox'+id);
    TextBox.setAttribute('class', "tap-target noselect");
    TextBox.setAttribute('id', id);
    TextBox.setAttribute('style', 'margin-right: 5px; padding: 0px 0px 5px 5px;');
    TextBox.innerHTML = title;
    return TextBox;
}
function InsertInput(text, size, updater, tooltipText){
    var ele = document.createElement('input');
    var id = UpdateID('p');
    ele.setAttribute('name', 'input'+id);
    ele.setAttribute('id', id);
    ele.setAttribute('placeholder', text);
    ele.setAttribute('type', 'text');
    ele.setAttribute('style', 'width:'+ size +'px; margin-right: 5px;  padding: 0px 0px 0px 0px;');
    if (updater != null){
        ele.setAttribute('onchange', updater);
    }
    else{
        //ele.setAttribute('onchange', '');
    }
    return ele;
}
