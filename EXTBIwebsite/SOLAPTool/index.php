<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <script type="text/javascript" src="scripts/interact.js"></script>
  <title>EXTBI</title>
  <meta charset="iso-8859-1">
  <link rel="stylesheet" href="../styles/layout.css" type="text/css">
  <!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
  <script type="text/javascript">
    // Struct Factory
    function makeStruct(names) {
      var names = names.split(' ');
      var count = names.length;
      function constructor() {
        for (var i = 0; i < count; i++) {
          this[names[i]] = arguments[i];
        }
      }
      return constructor;
    }
    // Structs
    //var Item = makeStruct("id speaker country");
    //var row = new Item(1, 'john', 'au');
    //alert(row.speaker); // displays: john
    var GeneratedQueryElement;
    var QueryUnit = [];
    var counter = 1;
    var UniqueID = 0;
    var NoSelectors = 0;
    var OprSelectOptions = [""];
    var selector = null;
    var LastOpr = "null";
    var currentP;
    var selectedOpr;
    var AvailableOperations = [];
    window.onload = Initialize; 

    function Initialize(){
      //console.log("Initialize");
      UpdateAvailableOperations();
      GeneratedQueryElement = document.getElementById('GeneratedQuery');
    }

    function print(){
      //console.log("print");
      GeneratedQueryElement.innerHTML = "";
      for(var i = 0; i < QueryUnit.length ; i++){
        QueryUnit[i].toString();
      }
    }

    function UID(){
      //console.log("UID");
      return UniqueID += 1;
    }

    function SelectMenuOptions(){
      //console.log("SelectMenuOptions");
      var select = document.getElementById('test');
      for (var i = 0; i<=10; i++){
      var opt = document.createElement('option');
      opt.value = i;
      opt.innerHTML = "Test";
      select.appendChild(opt);  
      }
    }

    function NewSelectOption(ele){
      //console.log("NewSelectOption");
      ele.value = ele.value.replace(/\s/g,'');
      if(ele.value[0] == "?" && ele.value[1] != null)
      {
        var target = document.getElementById(ele.parentNode.id);
        var obj = document.createElement('input');
        obj.setAttribute('type', 'text');
        obj.setAttribute('value', '?');
        obj.setAttribute('onchange', 'NewSelectOption(this)');
        obj.setAttribute('name', 'OprSelectAdded');
        obj.setAttribute('style', 'width:40px;');
        target.appendChild(obj);
      }
      else if (ele.value[0] == null && ele.name != "OprSelect1"){
        ele.remove();
      }
    }

    function UpdateAvailableOperations(){
      console.log("UpdateAvailableOperations");
  	  AvailableOperations = [];
      console.log((selector == null ? "null" : selector.currentTarget.getAttribute('name')));
      switch (selector == null ? "null" : selector.currentTarget.getAttribute('name')){
      	case "null":
      		console.log("Selected: None");
      		LookAtQueryFor();
      		break;
      	case "SELECT":
      		AvailableOperations.push("Variable", "SUM", "COUNT", "MIN");
      		break;
        case "S-Dice":
          AvailableOperations.push("RUPath");
          break;
      }
      ListOperations();
    }

    function LookAtQueryFor(){
      console.log("LookAtQueryFor");
    	switch (LastOpr){
    		case "null":
    			AvailableOperations.push("S-Dice");
    		break;
    		case "SELECT":
    			AvailableOperations.push("WHERE");
        break;
        case "WHERE":
          AvailableOperations.push("S-Dice");
        break;
        case "S-Dice":
          AvailableOperations.push("RUPath");
        break;
    	}
    }

    function InsertBR(ele){
      //console.log("InsertBR");
      ele.appendChild(document.createElement('br'));
    }

    function InsertP(ele, Query){
      //console.log("InsertP");
    	currentP = document.createElement('p');
      currentP.setAttribute('id', UID());
      Query.appendChild(currentP);
    }

    function InsertTextBox(title, place){
      //console.log("InsertTextBox");
    	var TextBox = document.createElement('text');
    	TextBox.setAttribute('name', title);
    	TextBox.setAttribute('class', "tap-target noselect");  
      TextBox.setAttribute('id', UID());
  		TextBox.innerHTML = title;
  		place.appendChild(TextBox);
    }

    function InsertInput(place, text){
      //console.log("InsertInput");
    	var ele = document.createElement('input');
		ele.setAttribute('name', 'OprSelect1');
		ele.setAttribute('value', text);
		ele.setAttribute('type', 'text');
		ele.setAttribute('style', 'width:40px;');
    ele.setAttribute('onchange', 'inputValueChanged(this)');
		place.appendChild(ele);
    }

    function inputValueChanged(element){
      //console.log("inputValueChanged");
      for (var i = 0; i < QueryUnit.length; i++){
        if (parseInt(element.parentNode.id)+2 == String(QueryUnit[i].id)){
            QueryUnit[i].variableName = element.value;           
        }
      }
      print();
    }

    function ListOperations(){
      //console.log("ListOperations");
      var target = document.getElementById('OprList');
      target.innerHTML = "";

      for(var i= 0; i < AvailableOperations.length; i++){
      	var obj =  document.createElement('text');
      	obj.setAttribute('class', 'build-target noselect');
        obj.setAttribute('name', AvailableOperations[i]);        
        obj.innerHTML = AvailableOperations[i];
        target.appendChild(obj);
        InsertBR(target);
      }
    }
    
    function GetClosestP(element){
    //console.log("GetClosestP");	
    	var p = element;
    	while (p.tagName != 'P'){
    		p = document.getElementById(p.parentNode.id);
    	}
    	return p;
    }
    
    interact('.tap-target')
      .on('tap', function (event) {  
        //console.log("Tap-Target");   
        if(selector != null){ //Use To Deselected Query Component (only one component can be active at a time)
          selector.currentTarget.classList.toggle('switch-color');
        }
        if((selector == null ? "" : selector.currentTarget.getAttribute('id')) == event.currentTarget.getAttribute('id')){ //Program will crash if you try to getAttribute from a null. This is the reason for the less desirable code.
          selector = null;
          UpdateAvailableOperations();
        }
        else{   
          OprSelect(event);
        }      
    });

    function OprSelect(event){
      //console.log("OprSelect");
      selector = event;
      event.currentTarget.classList.toggle('switch-color');
      selectedOpr = event.currentTarget;
      event.preventDefault();
      UpdateAvailableOperations();
    }

    interact('.build-target')
      .on('tap', function (event) {
        var selected = event.currentTarget.getAttribute('name');
        var QueryStatment = document.getElementById('OprSelectStatement');    
        switch (selected){
        	case "SELECT":
        		InsertP(event, QueryStatment);
        		InsertTextBox("SELECT", currentP);
        		LastOpr = selected;
        	break;
        	case "WHERE":
        		InsertP(event, QueryStatment);
        		InsertTextBox("WHERE", currentP);
        		LastOpr = selected;
        	break;
        	case "SUM":
        		InsertTextBox(" (SUM", GetClosestP(selectedOpr));
        		InsertInput(GetClosestP(selectedOpr));
        		InsertTextBox(" AS ", GetClosestP(selectedOpr));
        		InsertInput(GetClosestP(selectedOpr));
        		InsertTextBox(")", GetClosestP(selectedOpr));
        	break;
        	case "Variable":
        		InsertTextBox(" ",GetClosestP(selectedOpr));
        		InsertInput(GetClosestP(selectedOpr));
        	break;
          case "S-Dice":
            InsertP(event, QueryStatment);
            InsertTextBox("S-Dice", currentP);
            InsertInput(currentP, "?obs");
            LastOpr = selected;
            var SDice = {'name': 'SDice', 'id': UID(), 'variableName': '?obs', 'toString': function(){
              GeneratedQueryElement.innerHTML += "SELECT " + this.variableName + " WHERE\n";
            }};
            QueryUnit.push(SDice);
        } 
        UpdateAvailableOperations();
        print();
        event.preventDefault();
    });
  </script>
  <style type="text/css">
    .noselect {
      -webkit-touch-callout: none; /* iOS Safari */
      -webkit-user-select: none;   /* Chrome/Safari/Opera */
      -khtml-user-select: none;    /* Konqueror */
      -moz-user-select: none;      /* Firefox */
      -ms-user-select: none;       /* Internet Explorer/Edge */
      user-select: none;           /* Non-prefixed version, currently not supported by any browser */                               
    }

    .tap-target, .build-target {
  cursor: pointer;

  transition: all 0.3s;
}

.tap-target.switch-color{
  color: green;
  text-decoration: underline;
}

input[type=text], select {
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 40px;
    box-sizing: border-box;
}
  </style>
</head>
<body class="about QB4SOLAP">
<div class="wrapper row1">
  <header id="header" class="clear">
    <?php include '../logo.html';?>
    <?php include '../menu.html';?>
  </header>
</div>
<!-- content -->
<div class="wrapper row2">
  <div id="container" class="clear">
    <!--<section id="slider"><a href="#"><img src="images/demo/960x360.gif" alt=""></a></section>-->
    <?php include 'headline.html';?>
    <!-- content body -->
    <div id="content">
      <!-- main content -->   
      <aside id="left_column">
        <h2 class="title">SOLAP<br>Operators</h2>
          <section class="last" id="OprList" style="margin-left:10px;">
          </section>
        </aside>
      <section style="margin-left:10px;">
        <div class="no-top-margin" style="float:left;">
          <section>
            <div id="OprSelectStatement" style="margin-top:-20px; height:500px;  width:500px; overflow-y: scroll;">
              <p>Query:</p>
            </div>
            <div>
              <p>Generated query from the above operators:</p>
              <textarea id="GeneratedQuery" style="margin-top:-5px; width:494px; height: 300px; overflow-y: scroll;"></textarea>
            </div>
          </section>   
        </div>
      </section>
    </div>
    <!-- right column -->
    <!-- Trash 
        <article>
          <div id="0" ondragover="allowDrop(event)" ondrop="drop(event)" style="float: right; height:10px; width:600px; border: 1px; border-style: solid;">
          <p class="no-top-margin" style="text-align:center">Select</p>
          </div>
          <div id="div3" style="float:left; background-color: black" draggable="true" ondragstart="drag(event)">
            <div id="div1" subitems="true">Slice</div>
              <div id="div1" subitems="true">
              <p subitems="true">ID:<input class="name" subitems="true" type="text" value="" style="margin-left: 1px; width:35px" /></p>
              </div>
            </div>
            <br><br><br><br><br>
            <div id="div4" style="float:left; background-color: black" draggable="true" ondragstart="drag(event)">
              <div id="div1" subitems="true">Roll-up</div>
              <div id="div1" subitems="true">
              <p subitems="true">ID:<input class="name" subitems="true" type="text" value="" style="margin-left: 1px; width:35px" /></p>
              </div>
          </div>
        </article>     -->
    
    <aside id="right_column">
      <?php include '../topics.html';?>
      <?php include 'resources.html';?>
      <!-- /nav -->
      <?php include 'conference.html';?>
      <?php include 'contact.html';?>
    </aside>
    <!-- / content body -->
  </div>
</div>
<!-- footer -->
<div class="wrapper row3">
  <?php include '../footer.html';?>
  <?php include '../analytics.html';?>
</div>
</body>
</html>