<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="aggview about">
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
          <p>More and more RDF data is exposed on the Web via SPARQL endpoints and analyzed using SPARQL aggregate queries. As data volumes grow, standard SPARQL query processing techniques can no longer support sufficiently fast query response times. For this, relational databases use materialized views, which precompute aggregate query results and reuse these for query answering, but relational techniques do not support RDF specifics such as data incompleteness, the schemaless nature of RDF, and the need to support implicit (derived) information. 
This paper proposes an RDF-based approach to selecting an appropriate set of materialized RDF views and using these for effective SPARQL query rewriting, which handles these RDF specifics. Specifically, we propose an RDF-specific cost model, a view definition syntax, and algorithms for view selection and query rewriting. The experimental evaluation shows that the approach can improve query response time by more than an order of magnitude and handles RDF specifics well.</p>
            <p><i>Authors: Dilshod Ibragimov, Katja Hose, Torben Bach Pedersen, and Esteban Zimanyi</i></p>
        </article>
        <br>
        <article>
          <h2>SSB Queries</h2>
          <p> SSB defines 13 queries. They represent 4 "prototypical" queries with different selectivity factors. 
		  A brief description of the queries is given in a table below. 
		  We converted all 13 queries defined into SPARQL </p>
		  <table border="1">
			  
			  <tr><th>Query Prototypes</th> <th>Query No</th> <th>Query Parameters for Various Selectivities</th> 
			  </tr>
			  <tr>
				  <td rowspan="3" style="width:170px">Prototype 1. Amount of revenue increase that would have resulted from eliminating	certain company-wide discounts.</td>
				  <td align="center">Q1</td>
				  <td>Discounts 1, 2, and 3 for quantities less than 25 shipped in 1993</td>
			  </tr>
			  <tr>
				  <td align="center">Q2</td>
				  <td>Discounts 1, 2, and 3 for quantities less than 25 shipped in 01/1993</td>
			  </tr>
			  <tr>
				  <td align="center">Q3</td>
				  <td>Discounts 5, 6, and 7 for quantities less than 35 shipped in week 6 of 1993</td>
			  </tr>
			  <tr>
				  <td rowspan="3" style="width:170px">Prototype 2. Revenue for some product classes, for suppliers in a certain region, grouped by more restrictive product classes and all years.</td>
				  <td align="center">Q4</td>
				  <td>Revenue for 'MFGR#12' category, for suppliers in America</td>
			  </tr>
			  <tr>
				  <td align="center">Q5</td>
				  <td>Revenue for brands 'MFGR#2221' to 'MFGR#2228', for suppliers in Asia</td>
			  </tr>
			  <tr>
				  <td align="center">Q6</td>
				  <td>Revenue for brand 'MFGR#2239' for suppliers in Europe</td>
			  </tr>
			  <tr>
				  <td rowspan="4" style="width:170px">Prototype 3. Revenue for some product classes, for suppliers in a certain region, grouped by more restrictive product classes and all years.</td>
				  <td align="center">Q7</td>
				  <td>For Asian suppliers and customers in 1992-1997</td>
			  </tr>
			  <tr>
				  <td align="center">Q8</td>
				  <td>For US suppliers and customers in 1992-1997</td>
			  </tr>
			  <tr>
				  <td align="center">Q9</td>
				  <td>For specific UK cities suppliers and customers in 1992-1997</td>
			  </tr>
			  <tr>
				  <td align="center">Q10</td>
				  <td>For specific UK cities suppliers and customers in 12/1997</td>
			  </tr>
			  <tr>
				  <td rowspan="3" style="width:170px" valign="top">Prototype 4. Aggregate profit, measured by subtracting revenue from supply cost.</td>
				  <td align="center">Q11</td>
				  <td>For American suppliers and customers for manufacturers 'MFGR#1' or 'MFGR#2'</td>
			  </tr>
			  <tr>
				  <td align="center">Q12</td>
				  <td>For American suppliers and customers for manufacturers 'MFGR#1' or 'MFGR#2' in 1997-1998</td>
			  </tr>
			  <tr>
				  <td align="center">Q13</td>
				  <td>For American customers and US suppliers for category 'MFGR#14' in 1997-1998</td>
			  </tr>
		  </table>
          <ul>
			<li>Cube schema: <a href="queries/SSBCube_Complete.txt" target="_blank">QB4OLAP Schema</a></li>
            <li>Original SPARQL queries: <a href="queries/SSB_Queries.txt" target="_blank">Original Queries</a></li>
			<li>View queries: <a href="queries/SSB_Views.txt" target="_blank">View Queries</a></li>
			<li>Example of rewritten SPARQL query <a href="queries/SSB_Queries_over_Views.txt" target="_blank">Rewritten Queries</a></li>
          </ul>
		  
		  <h2>LUBM Queries</h2>
          <p> We defined in SPARQL 6 analytical queries involving grouping over several classification dimensions. We use <em>COUNT</em> aggregation in all queries. 
		  These queries aggregate over number of courses offered by departments, number of courses taken by students, number of graduate courses in each department, 
		  number of courses taught by professors in each department, etc.
		  A brief description of the queries is given in a table below.</p>
		  <table border="1">
			  
			  <tr><th>Query No</th> <th>Query Description</th> 
			  </tr>
			  <tr>
				  <td align="center">Q1</td>
				  <td>Count student courses: the number of courses students have taken, classified by the student’s department, university, and advisor</td>
			  </tr>
			  <tr>
				  <td align="center">Q2</td>
				  <td>Count faculty member courses: the number of courses faculty members teach, classified by the department which they are a member of and the university the department is in</td>
			  </tr>
			  <tr>
				  <td align="center">Q3</td>
				  <td>Count research assistant courses: the number of courses research assistants have taken, classified by the department they work for, the university of that department, and their advisor.</td>
			  </tr>
			  <tr>
				  <td align="center">Q4</td>
				  <td>Count graduate courses taught by professors : the number of graduate courses professors teach, classified by the professor's department and the university for which she works.</td>
			  </tr>
			  <tr>
				  <td align="center">Q5</td>
				  <td>Count department courses: the number of courses offered by department, classified by the department and the university of the department.</td>
			  </tr>
			  <tr>
				  <td align="center">Q6</td>
				  <td>Count the number of final exams a faculty member should grade if every course's final evaluation is an exam, classified by faculty, her department, the university for which she works.</td>
			  </tr>
		  </table>
          <ul>
            <li>Cube schema: <a href="queries/LUBMCube_Complete_V4.txt" target="_blank">QB4OLAP Schema</a></li>
			<li>Original SPARQL queries: <a href="queries/LUBM_Queries.txt" target="_blank">Original Queries</a></li>
			<li>View queries: <a href="queries/LUBM_Views.txt" target="_blank">View Queries</a></li>
			<li>Example of rewritten SPARQL query <a href="queries/LUBM_Queries_over_Views.txt" target="_blank">Rewritten Queries</a></li>
          </ul>
        </article>
        <article>
          <h2>Datasets</h2>
          <p>The SSB benchmark provides a data generator that generates relational data. It generates data for 4 dimensions (Parts, Customers, Suppliers, and Dates) and transactional data (observations).
		  We then translated the datasets into the RDF multidimensional representation (QB4OLAP) introducing incompleteness to this dataset as well, as illustrated in the figure below. 
		  In this dataset, a lineorder item is represented as an observation described by dimensions, where a subject (observation) is connected to objects (dimensions) via certain predicates.
		  Every connected dimension object is, in turn, defined as a path-shaped graph. Hierarchies in dimensions are connected via the <it>skos:broader</it> predicate. 
		  Measures (represented as ovals) are connected directly to observations. We changed the data generator so that  approx. 30%  of the information that relates suppliers to their corresponding
		  cities in the Supplier dimension (and parts to their brands in the Part dimension) is missing. Instead, we connected suppliers with missing city information directly to their 
		  respective nations (<it>ssb:s\_nation</it>) and parts with missing brand information we connected directly to the next level in the hierarchy -- categories (<it>ssb:p\_category</it>).
		  Thus, in the roll-up path Supplier -> City -> Nation -> Region the City level is incomplete. The Part dimension is affected in the level Brand (Part -> Brand -> Category -> Manufacturer).
		  In our experiments, we used different scaling factors (1,2,3) to obtain datasets of different sizes. Observations and all dimensional data are stored in separate graphs -- 
		  one for each dimension (parts, customers, suppliers, and dates) and one for observations. 
		  </p>
		  <img src="images/ssb_incomplete.png" alt="SSB RDF Schema" width="490" height="174" />
          <ul>
            <li>SSB RDF Scale Factor 1: <a href="http://164.15.78.105:8080/files/lineorders_ssb_sf1_sp_7.zip" target="_blank">RDF SF1 (330 MB)</a></li>
			<li>SSB RDF Scale Factor 2: <a href="http://164.15.78.105:8080/files/lineorders_ssb_sf2_sp_7.zip" target="_blank">RDF SF2 (670 MB)</a></li>
			<li>SSB RDF Scale Factor 3: <a href="http://164.15.78.105:8080/files/lineorders_ssb_sf3_sp_7.zip" target="_blank">RDF SF3 (1000 MB)</a></li>
          </ul>
		  
		  <p>LUBM features an ontology for the university domain; it creates synthetic OWL data scalable to an arbitrary size. The dataset describes universities, departments, students, 
		  professors, publication, courses, etc. We decided to build our data cube and corresponding queries on the information related to courses. In particular, we are interested in 
		  knowing the number of courses offered by departments, the type of the courses, the number of students taking courses, etc. 
		  
		  To introduce incompleteness in the data we changed the data generator so that approx. 30% of the information that relates staff to courses is missing. Instead, we introduced 
		  information about the department that offers these courses (<it>lubm:offeringDepartment</it>). In such settings, counting the number of courses offered by departments becomes 
		  more challenging since the roll-up path Course -> Staff -> Department needs to be complemented by the roll-up path Course -> Department and the aggregation of courses by 
		  Department cannot be answered by the results of the aggregation by Staff. A simplified schema of the data structure is presented in the figure below. 
		  </p>
		  <img src="images/lubm_schema.png" alt="LUBM RDF Schema" width="490" height="174" />
          
        </article>
		 <article>
          <h2>Application</h2>
          <p>The source code of the application used for selecting views can be found <a href="http://extbi.lab.aau.dk/rdfmatviewselect.zip" target="_blank">here</a>.
		  </p>		  
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
        Currently under submission.
        <!--JIST 2014 : The 4th Joint International Semantic Technology Conference<br>
        <a href="http://language-semantic.org/jist2014/">http://language-semantic.org/jist2014/</a><br>
        Short Paper-->
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