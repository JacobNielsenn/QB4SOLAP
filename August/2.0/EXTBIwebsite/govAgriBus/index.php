<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="about govAgriBus">
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
      <section>
        <article>
          <h2>Abstract</h2>
          <p>Recent advances in Semantic Web technologies have led to a
            growing popularity of the (Linked) Open Data movement. Only recently,
            the Danish government has joined the movement and published several
            data sets - formerly only accessible for a fee - as Open Data in various
            formats, such as CSV and text files. These raw data sets are difficult to
            process automatically and combine with other data sources on the Web.
            Hence, our goal is to convert such data into RDF and make it available to
            a broader range of users and applications as Linked Open Data. In this
            paper, we discuss our experiences based on the particularly interesting
            use case of agricultural data as agriculture is one of the most important
            industries in Denmark. We describe the process of converting the data
            and discuss the particular problems that we encountered with respect
            to the considered data sets. We additionally evaluate our result based
            on several queries that could not be answered based on existing sources
            before.</p>
            <p><i>Authors: Alex B. Andersen, Nurefsan G&#252;r, Katja Hose, Kim A. Jakobsen, and Torben Bach Pedersen</i></p>
        </article>
        <br>
         <article>
          <h2>Ontology</h2>
          <a href="/images/OntologyFinal.pdf">
          <img src="/images/OntologyFinal-1.png" alt="RDF Ontology" style="width:630px;height:414px">
          </a>
        </article>
        <br>
        <article>
          <h2>LOD Cloud</h2>
          <a href="/images/LinkedOpenDataCloud2014.png">
          <img src="/images/LinkedOpenDataCloud2014_medium.png" alt="The 'GovAgriBus Denmark' dataset in the LOD cloud" style="width:630px;height:414px">
          </a>
        </article>
        <article>
          <h2>Datahub.io Data</h2>
          <p>Since the Datahub.io is not able to store the entire dataset we make it available here:</p>
          <ul>
            <li>Ontology: <a href="http://extbi.lab.aau.dk/govagribus-denmark.owl" target="_blank">OWL (45K)</a></li>
            <li>Void statistics: <a href="http://extbi.lab.aau.dk/govagribus-denmark_void.n3" target="_blank">N3 (1.5K)</a></li>
            <li>Dataset: <a href="http://extbi.lab.aau.dk/govagribus-denmark.rdf" target="_blank">RDF (5.2G)</a>, <a href="http://extbi.lab.aau.dk/govagribus-denmark.rdf.tgz" target="_blank">tar.gz (455M)</a> </li>
            <li>Readme: <a href="README.txt" target="_blank">txt (3K)</a></li> 
          </ul>
        </article>
      </section>
    </div>
    <!-- right column -->
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