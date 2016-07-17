<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
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
      <section>
        <article>
          <h2>Abstract</h2>
          <p>The Semantic Web has drawn the attention of data enthusiasts, and also inspired the exploitation and design of multidimensional data warehouses in an unconventional way. Traditional data warehouses operate over static data. However multidimensional(MD) data modeling approach can be dynamically extended by defining both the schema and instances of MD data as RDF graphs. Importance and plausibility of MD data warehouses over RDF is widely studied yet none of the works support a spatially enhanced MD model on the SW. Spatial support in DWs is a desirable feature for enhanced analysis. In this paper we propose to utilize the spatial dimension of the cube by adding spatial object type and topological relationships to the existing QB4OLAP vocabulary. Thus we can implement spatial and metric analysis on spatial members along with OLAP operations. In our contribution, we describe a set of spatial OLAP - SOLAP operations, demonstrate a spatially extended meta-model as QB4SOLAP and apply on a use case scenario. Finally, we conclude the paper showing how these SOLAP queries can be expressed in SPARQL.</p>
            <p><i>Authors: Nuref&#351;an G&#252;r, Katja Hose, Torben Bach Pedersen and Esteban Zim&#225;nyi</i></p>
        </article>
        <br>
         <article>
          <h2>QB4SOLAP Meta-Model</h2><br>
          <a href="/QB4SOLAP/images/qb4solap.pdf">
          <img src="/QB4SOLAP/images/qb4solap.png" alt="RDF Vocabulary" style="width:466px;height:554px">
          </a>
        </article>
        <br>

          <h2>RDF Files</h2>
          <p>RDF files for the QB4SOLAP vocabulary, use case schema, and intances can be found in the following links</p>
          <ul>
            <li>QB4SOLAP RDF Vocabulary: <a href="/QB4SOLAP/data/qb4solap.ttl" target="_blank">TTL (13K)</a></li>
			<li>Use Case Schema Triples: <a href="/QB4SOLAP/data/gnw_qb4solap_schema.ttl" target="_blank">TTL (24K)</a></li>
            <li>Use Case Instance Triples: <a href="/QB4SOLAP/data/gnw_instance.7z" target="_blank">ZIP (98K)</a></li>

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