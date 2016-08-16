<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="coda about">
<div class="wrapper row1">
  <header id="header" class="clear">
    <?php include '../logo.html';?>
    <?php include '../menu.html';?>
  </header>
</div>
<!-- content -->
<div class="wrapper row2">
  <div id="container" class="clear">
  <?php include 'headline.html';?>
    <!-- content body -->
    <div id="content">
      <!-- main content -->
      <section>
        <article>
          <h2>Abstract</h2>
          <p>More and more RDF data is exposed on the Web via SPARQL endpoints. With the recent SPARQL 1.1 standard, these datasets can be queried in novel and more powerful ways, e.g., complex analysis tasks involving grouping and aggregation, and even data from multiple SPARQL endpoints, can now be formulated in a single query. This enables Business Intelligence applications that access data from federated web sources and can combine it with local data. However, as both aggregate and federated queries have become available only recently, state-of-the-art systems lack sophisticated optimization techniques that facilitate efficient execution of such queries over large datasets. To overcome these shortcomings, we propose a set of query processing strategies and the associated Cost-based Optimizer for Distributed Aggregate queries (CoDA) for executing aggregate SPARQL queries over federations of SPARQL endpoints. Our comprehensive experiments show that CoDA significantly improves performance over current state-of-the-art systems.</p>
            <p><i>Authors: Dilshod Ibragimov, Katja Hose, Torben Bach Pedersen, and Esteban Zimanyi</i></p>
        </article>
        <br>
        <article>
          <h2>Queries</h2>
          <p> SSB defines 13 queries. They represent 4 "prototypical" queries with different selectivity factors. 
		  A brief description of the queries is given in a table below. 
		  We converted all 13 queries defined into SPARQL and used the SERVICE keyword to query endpoints for all triple patterns. </p>
		  <table border="1">
			  
			  <tr><th>Query Prototypes</th> <th>Query No</th> <th>Query Parameters for Various Selectivities</th> 
			  </tr>
			  <tr>
				  <td rowspan="3" style="width:170px">Prototype 1. Amount of revenue increase that would have resulted from eliminating	certain company-wide discounts.</td>
				  <td align="center">Q1.1</td>
				  <td>Discounts 1, 2, and 3 for quantities less than 25 shipped in 1993</td>
			  </tr>
			  <tr>
				  <td align="center">Q1.2</td>
				  <td>Discounts 1, 2, and 3 for quantities less than 25 shipped in 01/1993</td>
			  </tr>
			  <tr>
				  <td align="center">Q1.3</td>
				  <td>Discounts 5, 6, and 7 for quantities less than 35 shipped in week 6 of 1993</td>
			  </tr>
			  <tr>
				  <td rowspan="3" style="width:170px">Prototype 2. Revenue for some product classes, for suppliers in a certain region, grouped by more restrictive product classes and all years.</td>
				  <td align="center">Q2.1</td>
				  <td>Revenue for 'MFGR#12' category, for suppliers in America</td>
			  </tr>
			  <tr>
				  <td align="center">Q2.2</td>
				  <td>Revenue for brands 'MFGR#2221' to 'MFGR#2228', for suppliers in Asia</td>
			  </tr>
			  <tr>
				  <td align="center">Q2.3</td>
				  <td>Revenue for brand 'MFGR#2239' for suppliers in Europe</td>
			  </tr>
			  <tr>
				  <td rowspan="4" style="width:170px">Prototype 3. Revenue for some product classes, for suppliers in a certain region, grouped by more restrictive product classes and all years.</td>
				  <td align="center">Q3.1</td>
				  <td>For Asian suppliers and customers in 1992-1997</td>
			  </tr>
			  <tr>
				  <td align="center">Q3.2</td>
				  <td>For US suppliers and customers in 1992-1997</td>
			  </tr>
			  <tr>
				  <td align="center">Q3.3</td>
				  <td>For specific UK cities suppliers and customers in 1992-1997</td>
			  </tr>
			  <tr>
				  <td align="center">Q3.4</td>
				  <td>For specific UK cities suppliers and customers in 12/1997</td>
			  </tr>
			  <tr>
				  <td rowspan="3" style="width:170px" valign="top">Prototype 4. Aggregate profit, measured by subtracting revenue from supply cost.</td>
				  <td align="center">Q4.1</td>
				  <td>For American suppliers and customers for manufacturers 'MFGR#1' or 'MFGR#2' in 1992</td>
			  </tr>
			  <tr>
				  <td align="center">Q4.2</td>
				  <td>For American suppliers and customers for manufacturers 'MFGR#1' or 'MFGR#2' in 1997-1998</td>
			  </tr>
			  <tr>
				  <td align="center">Q4.3</td>
				  <td>For American customers and US suppliers for category 'MFGR#14' in 1997-1998</td>
			  </tr>
		  </table>
          <ul>
            <li>SPARQL Queries with one SERVICE Endpoint: <a href="queries/queries_1se.txt" target="_blank">one SERVICE endpoint</a></li>
			<li>SPARQL Queries with two SERVICE Endpoint: <a href="queries/queries_2se.txt" target="_blank">two SERVICE endpoint</a></li>
			<li>SPARQL Queries with three SERVICE Endpoint: <a href="queries/queries_3se.txt" target="_blank">three SERVICE endpoint</a></li>
          </ul>
        </article>
        <article>
          <h2>Datasets</h2>
          <p>The data in SSB is generated
as relational data. We used different
scale factors (1 to 5 - 6M to 30M observations) to generated multiple
datasets of different sizes. We translated
the datasets into RDF using a
vocabulary that strongly resembles the
SSB tabular structure. For example, a lineorder
tuple is represented as a starshaped
set of triples where the subject
(URI) is linked via a property (e.g., rdfh:lo_orderdate) to a an object
(e.g., rdfh:lo_orderdate_19931201) which in turn can be subject of another
star-shaped graph. Values such as quantity and discount are connected to lineorder entities
as literals. A simplified schema of the RDF structure is illustrated in the figure below.
Converted datasets contain 110,5M (scale factor 1) to 547,5M (scale factor 5) triples</p>
		  <img src="images/SSB_RDF_Schema.png" alt="SSB RDF Schema" width="328" height="240" />
          <ul>
            <li>SSB Scale Factor 1: <a href="http://extbi.lab.aau.dk/ssb_sf1.zip" target="_blank">SSB RDF SF1 (300 MB)</a></li>
			<li>SSB Scale Factor 2: <a href="http://extbi.lab.aau.dk/ssb_sf2.zip" target="_blank">SSB RDF SF2 (600 MB)</a></li>
			<li>SSB Scale Factor 3: <a href="http://extbi.lab.aau.dk/ssb_sf3.zip" target="_blank">SSB RDF SF3 (900 MB)</a></li>
			<li>SSB Scale Factor 4: <a href="http://extbi.lab.aau.dk/ssb_sf4.zip" target="_blank">SSB RDF SF4 (1.2 GB)</a></li>
			<li>SSB Scale Factor 5: <a href="http://extbi.lab.aau.dk/ssb_sf5.zip" target="_blank">SSB RDF SF5 (1.5 GB)</a></li>
          </ul>
        </article>

      </section>
      <!-- ########################################################################################## -->
      <!-- ########################################################################################## -->
      <!-- ########################################################################################## -->
      <!-- ########################################################################################## -->
      
    </div>
    <!-- right column -->
    <aside id="right_column">
      <?php include '../topics.html';?>
      <?php include 'resources.html';?>
      <!-- /nav -->

      <h2 class="title">Conference</h2>
      <section class="">
        <address>
        ESWC 2015 : The 12th ESWC Conference<br>
        <a href="http://2015.eswc-conferences.org/about-eswc2015">http://2015.eswc-conferences.org</a><br>
         Research Track
        </address>
      </section>
      

      <h2 class="title">Contact</h2>
      <section class="last">
        <address>
        Dilshod Ibragimov<br>
        Email: <a>diib [at] cs.aau.dk </a>
        </address>
      </section>

      <!-- /section -->
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