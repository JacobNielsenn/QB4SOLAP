<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="experiments swod">
<div class="wrapper row1">
  <header id="header" class="clear">
    <?php include '../logo.html';?>
    <?php include '../menu.html';?>
  </header>
</div>
<div class="wrapper row2">
  <div id="container" class="clear">
    <?php include 'headline.html';?>
    <!-- content body -->
    <div id="content">
      <!-- main content -->



      <section>
        <article class="more-bottom-padding">
          <h2>Repositories</h2>
          <p>The inplementaiton and the code for running the experiments can be access at the the two GitHub project linked below. </p> 
          <ul>
            <li><p class="no-top-margin">SWOD algorithm implementation <a href="https://github.com/abondoa/swod">GibHub</a> </p></li>
            <li><p class="no-top-margin">Tools for running the experiments <a href="https://github.com/kimajakobsen/swodTools">GibHub</a> </p></li>
          </ul>
        </article>
        <article class="more-bottom-padding">
          <h2>SWOD Implementation</h2>
          <p>This program generates a serie of SPARQL construct queries that create the snowflake pattern and fully denormalized pattern cubes.</p> 
          <p>This Java program use Apache Maven to manage dependencies</p>
          <p>The SWOD Tools project contains generated SPARQL queries, thus it is not nessary to run the SWOD program in order to run the experiments</p>
        </article>
        <article class="more-bottom-padding">
          <h2>SWOD Tools</h2>
          <p>These tools will allow you to generate the TPC-H data in triples (generate.sh), load the data into Virtuoso and Apache Jena (load.sh), run the TPC-H queries on the triple stores (query.sh), and analyse the results by comparing the queries (extractQueryTimes.py, compareResults.py).</p>
          <p>All scripts are written in bash and python, this might result in some problem on windows systems.</p> 
          <p>The batch scripts takes a series of "sources" as input, these modular configurations files are located in the "source" folder. Be aware the these configuration files need to be set up manually before running any of the programs. </p>
          <p>The python scripts have a help flag (--help) that displays the allowed parameters. </p>
        </article>
        <article class="more-bottom-padding">
          <h2>Workflow</h2>
          <ol>
            <li><p>Download and install the following progarm</p></li>
              <ul>
                <li><a href="http://sourceforge.net/projects/bibm/">BIBM</a></li>
                <li><a href="http://www.l3s.de/~minack/rdf2rdf/">rdf2rdf</a></li>
                <li><a href="http://www.tpc.org/tpch/dbgen-download-request.asp">TPC-H DBGen</a></li>
              </ul>
            <li><p>Create configuration files (source files) that match your system (source/machine/) and wanted configuration (scale factor etc.)</p></li>
            <li><p>Generate or download the dataset </p></li>
            <ul>
                <li><p>Generation requires Virtuoso for running the construct queries</p></li>
              </ul>
            <li><p>Install Virtuoso or Apache Jena</p></li>
            <li><p>Load the data into the Jena TDB or Virtuoso by using the appropriate configuration files</p></li>
            <li><p>Change the querymix configuration (source/) to mach which queries you want to execute, run the querymix.sh program to propagate these settings.</p></li>
            <li><p>Run the query.sh script with the appropiate configuration files to start the experiments</p></li>
            <li><p>Use the extractQueryTimes.py on the generated logfiles (logs/) to extract and aggregate the query times. </p></li>
            <li><p>The experiments can now be compare using the compareResults.py script</p></li> 
          </ol>
          <p>Feel free to post bug report and ask questions</p> 
        </article>
      </section>
    </div>
    <aside id="right_column">
      <?php include '../topics.html';?>
      <?php include 'resources.html';?>
      <!-- /nav -->
      <?php include 'conference.html';?>
      <?php include 'contact.html';?>
    </aside>

  </div>
</div>
<!-- footer -->
<div class="wrapper row3">
  <?php include '../footer.html';?>
  <?php include '../analytics.html';?>
</div>
</body>
</html>