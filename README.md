# QB4SOLAP
This readme goes over how to install Virtuoso and XAMPP, it also covers how to setup Virtuoso with the schema and datasets and where to copy the web service to. Lasty in Structure of the code information about the different classes that are used and how to change the schema if needed.

## Virtuoso

### Install Virtuoso on Windows 10
First download the files from the homepage located here https://virtuoso.openlinksw.com/dataspace/doc/dav/wiki/Main/VOSDownload. This guide uses Virtuoso 7.2.4 64-bit version which can be found under pre-built binaries for Windows on their homepage.
Unpack the files to a location of your choice. For simplicity, this guide stores the unpacked files at “C:\Virtuoso”.
To start the virtuoso through command prompt you will need to first setup the Environment Variables, which can be done by opening up the Control Panel -> System -> Advanced system settings -> Environment Variables. In the bottom of the new windows the System variables is located select the variable Path and double click on it, this opens up another windows, now we need to add two new variables paths this is done by clicking on the button New, and type/copy in the following path “C:\Virtuoso\virtuoso-opensource\bin” and repeat for “C:\Virtuoso\virtuoso-opensource\lib”. After that close the windows and open up the command prompt and make sure you run it as administrator, then type in “virtuoso-t -?” and a list of commands should appear, If not, that means that the environment variables have not been setup correctly.

### Start/Close Server
Open command prompt as administrator and navigate to the database folder by typing “cd C:\Virtuoso\virtuoso-opensource\database”, afterwards type “virtuoso-t -f” to start the server. To close the server hold ctrl while pressing c.

After starting the server you will now be able to interact with virtuoso through the web based GUI virtuoso provides, by open your internet browser and typing “http://localhost:8890” in the web address. Here you will be able to see some documentation about virtuoso along with some tutorials and other stuff.

### Uploading Schemas and Data to Virtuoso
Go to your virtuoso website (http://localhost:8890) and click on Conductor which is located in the upper left side of the website. The default login and password is dba and are suggest that you change though the GUI which is located here; System Admin -> User Accounts, a list of accounts will then be shown and to edit the password of the dba click the edit button related to dba account.

When you are logged in, go to Linked Data -> Quad Store Upload. Here you will be able to upload your schema and data sets. The named IRI Graph for shema should be “http://qb4solap.org/cubes/schema/geonorthwind#” and for the datasets use “http://qb4solap.org/cubes/instance/geonorthwind#”.

## XAMPP

This guide will explain how to setup the web service using XAMPP, first of go to the XAMPP website https://www.apachefriends.org/index.html and download the newest version of XAMPP. After the installation has completed you are going to unzip the file containing the web service inside a folder called SOLAPTool is located, you need to copy that to your XAMPP location, default is C drive so the path would look like this C:\xampp , from there we go to htdocs and copy the folder SOLAPTool into the directory. The path to the index.php which is located inside SOLAPTool should be similar to this: C:\xampp\htdocs\SOLAPTool\index.php .

To check if it is working we need to open XAMPP, this needs to be done via administrator rights. Once open you can start the Apache server manually, if skype is running on your machine you need to change the port to something else. Now try and open your browser and type in "localhost" this should redirect you to the XAMPP dashboard which means you have installed and started apache correctly. Next up is simple referencing the location from the htdocs were we copied the SOLAPTool to. So the url would be "http://localhost/SOLAPTool/index.php" and now you should be able to run your first query.

## Structure of the code

Global Variables
- Q is of claas ClsQuery which holds a list with all operator classes, this is also the class that is used to generate the query.
- GeneratedQueryElement holds the query.
- QueryStatment helper variable to generatedQueryElement
- ID used in UpdateID to return new variables name without overwriten exsisting onces.
- NameID helper function to RUPath.
- global help identify already used names.
- globalPath help identify already used names.
- innerGlobal help identify already used names.
- innerGLobalPath help identify already used names.
- SpatialAggregation array of spatialAggregation names for the schema.
- TopologicalRelations array of TopologicalRelations names for the schema.
- NumericOperations array of NumericOperations names for the schema.
- DataTypes array of DataTypes names for the schema.
- RelationalOperators array of RelationalOperators names for the schema.
- AGG array of AGG names for the schema.
- SpatialDimensions array of SpatialDimensions names for the schema.
- SpatialFunction array of SpatialFunction names for the schema.

Classes
- Binds returns a RDF in a list, the list will always only contain one element, The list is only used in the returnQuery function that is located in the Query class.
- EndGroupBy Similar to Binds it return a RDF in a list. The class has an adds and remove function to insert variables into the groupby rdf, when the function returnEndGroupBy is called is generate the RDF and adds all the variables that is added to that object.
- Filters used for filters it returns a list of RDF, only one RDF is always in the list and only the first parameter is used of the RDF, this is done to make it compatible with the merging process in ClsQuery.
- GroupBy same functionality as filters.
- Levels return an object that has three variables startLevel, endLevel and levels this is used in generating the roll up path.
- Operator holds all information for the operators to generate the necessary code for that operator.
- Path generate the path, used by operators.
- ClsQuery holds all operators and call generatequery on all operators on its list, and merges the queries together.
- RDF simple functionality to work with RDFs
- RDFHandler holds a collection of RDFs
- Select is in charge is generating the select statements used in the operators.
Global Functions
- createHTML.js Handles all generation of HTML elements, also have some limited logic like buttons.
- utility/loadSchema.js in charge of loading the schema used to generate the menus according to the schema.
- map.js used in S-Slice to create the map.

### Changing schema
Some refactoring is needed in order to make it more flexible, the loadSchema may have some errors on new schema since it was only tested with two schemas. Likewise the global variables SpatialAggregation, TopologicalRelations, NumericOperations, DataTypes, RelationalOperators, AGG, SpatialDimensions and SpatialFunction need to be removed and generate with the schema and all places where the old variables were used need to be replaced with the newly generated ones.

### Chaning endpoint
The url for the endpoint is located in utility used by jQyery $.post .

### Chaning google map id
If the map for some reason stops working it is highly like that it because the map id access token has run out, and therefor needs to be replaced. The access token is located in map.js in the function initializeMap when we set the tileLayer. Optimally a function need to be created that fetches a new access token from google maps to not encounter this problem again.



