<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="swod about">
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
          <p>In today's data-driven world, analytical querying, typically based on the data cube concept, is the cornerstone of answering important business questions and making data-driven decisions. Traditionally, the underlying analytical data was mostly internal to the organization and stored in relational data ware houses and data cubes. Today, external data sources are essential for analytics and, as the Semantic Web gains popularity, more and more external sources are available in native RDF. With the recent SPARQL 1.1 standard, performing analytical queries over RDF data sources has finally become feasible. However, unlike their relational counterparts, RDF data cubes stores lack optimizations that enable fast querying. In this paper, we present an approach to optimizing RDF data cubes that is based on three novel cube patterns that optimize RDF data cubes, as well as associated algorithms that transform the RDF data cube. An extensive experimental evaluation shows that the approach allows trading additional storage and/or load times in return for significantly increased query performance. We further provide guidelines for which patterns to apply for specific scenarios and systems.  </p>
          <p><i>Authors: Kim A. Jakobsen, Alex B. Andersen, Katja Hose, and Torben Bach Pedersen</i></p>
        </article>
        <br>
        <article>
          <h2>TPC-H relational diagram</h2>
          <a href="/images/tpch.png"><img style="max-width: 630px;"  alt="The TPC-H relational diagram" src="/images/tpch.png"></a>
        </article>
        <article>
          <h2>Dataset</h2>
  <table class="data-table">
    <tr>
        <th class="border-right border-bottom"></th>
        <th class="border-bottom">Scale 0.1</th>
        <th class="border-bottom">Scale 0.2</th>
        <th class="border-bottom">Scale 0.3</th>
        <th class="border-bottom">Scale 0.5</th>
    </tr>
    <tr>
        <td class="border-right">Snowflake</td>
        <td><a href="http://extbi.lab.aau.dk/snowpatterncube_0.1.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/snowpatterncube_0.2.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/snowpatterncube_0.3.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/snowpatterncube_0.5.tar.gz">Download</a></td>
    </tr>
    <tr>
        <td class="border-right">Star</td>
        <td><a href="http://extbi.lab.aau.dk/starpatterncube_0.1.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/starpatterncube_0.2.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/starpatterncube_0.3.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/starpatterncube_0.5.tar.gz">Download</a></td>
    </tr>
    <tr>
        <td class="border-right">Denormalized</td>
        <td><a href="http://extbi.lab.aau.dk/denormpatterncube_0.1.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/denormpatterncube_0.2.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/denormpatterncube_0.3.tar.gz">Download</a></td>
        <td><a href="http://extbi.lab.aau.dk/denormpatterncube_0.5.tar.gz">Download</a></td>
    </tr>
</table>
        </article>
    </div>
    <!-- right column -->
    <aside id="right_column">
      <?php include '../topics.html';?>
      <?php include 'resources.html';?>
      <?php include 'conference.html';?>
      <?php include 'contact.html';?>
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


