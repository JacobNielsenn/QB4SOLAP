# QB4SOLAP

## Install Virtuoso on Windows 10
First download the files from the homepage located here. This guide uses Virtuoso 7.2.4 64-bit version which can be found under pre-built binaries for Windows on their homepage.
Unpack the files to a location of your choice. For simplicity, this guide stores the unpacked files at “C:\Virtuoso”.
To start the virtuoso through command prompt you will need to first setup the Environment Variables, which can be done by opening up the Control Panel -> System -> Advanced system settings -> Environment Variables. In the bottom of the new windows the System variables is located select the variable Path and double click on it, this opens up another windows, now we need to add two new variables paths this is done by clicking on the button New, and type/copy in the following path “C:\Virtuoso\virtuoso-opensource\bin” and repeat for “C:\Virtuoso\virtuoso-opensource\lib”. After that close the windows and open up the command prompt and make sure you run it as administrator, then type in “virtuoso-t -?” and a list of commands should appear, If not, that means that the environment variables have not been setup correctly.

## Start/Close Server
Open command prompt as administrator and navigate to the database folder by typing “cd C:\Virtuoso\virtuoso-opensource\database”, afterwards type “virtuoso-t -f” to start the server. To close the server hold ctrl while pressing c.

After starting the server you will now be able to interact with virtuoso through the web based GUI virtuoso provides, by open your internet browser and typing “http://localhost:8890” in the web address. Here you will be able to see some documentation about virtuoso along with some tutorials and other stuff.

## Uploading Schemas and Data to Virtuoso
Go to your virtuoso website (http://localhost:8890) and click on Conductor which is located in the upper left side of the website. The default login and password is dba and are suggest that you change though the GUI which is located here; System Admin -> User Accounts, a list of accounts will then be shown and to edit the password of the dba click the edit button related to dba account.

When you are logged in, go to Linked Data -> Quad Store Upload. Here you will be able to upload your schema and data sets. The named IRI Graph for shema should be “http://qb4solap.org/cubes/schema/geonorthwind#” and for the datasets use “http://qb4solap.org/cubes/instance/geonorthwind#”.
