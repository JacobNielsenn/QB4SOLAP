<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="future QB4SOLAP">
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
        <article>
        <h2>Prefix</h2>
          <pre>
prefix agri: &lt;http://extbi.lab.aau.dk/ontology/agriculture/>
prefix bus: &lt;http://extbi.lab.aau.dk/ontology/business/>
prefix wgs: &lt;http://www.w3c.org/2003/01/geo/wgs84_pos#>
prefix xsd: &lt;http://http://www.w3.org/2001/XMLSchema#>
          </pre>

        </article>

        <article>
          <h2>Aggregated Query Template 1</h2>
            <pre>

SELECT ?crop COUNT(*) as ?cnt
FROM &lt;http://extbi.lab.aau.dk/resource/agriculture&gt;
WHERE {
  ?field agri:produces ?crop .
  ?field wgs:long ?long .
  ?field wgs:lat ?lat .
  FILTER(xsd:double(?long) > 10 && xsd:double(?long) < 11 &&
    xsd:double(?lat) > 54 && xsd:double(?lat) < 55) .
}
GROUP BY ?crop
			</pre>
        </article>
        <article>
          <h2>Aggregated Query Template 2</h2>
            <pre>

SELECT ?application SUM(?area) as ?totalArea
FROM &lt;http://extbi.lab.aau.dk/resource/agriculture>
WHERE {
  ?fieldBlock agri:application ?application .
  ?fieldBlock agri:nettoareal ?area .
}
GROUP BY ?application

			</pre>
        </article>
        <article>
          <h2>Aggregated Query Template 3</h2>
            <pre>

SELECT ?format COUNT(*) as ?cnt
FROM &lt;http://extbi.lab.aau.dk/resource/business>
WHERE {
  ?company bus:hasFormat ?format .
  ?company bus:hasActivity &lt;http://extbi.lab.aau.dk/resource/business/activity/description/dyrkning_af_groentsager_og_meloner%2C_roedder_og_rodknolde#this> .
}
GROUP BY ?format
ORDER BY DESC(?cnt)

			</pre>
        </article>
        <article>
          <h2>Aggregated Query Template 4</h2>
            <pre>

SELECT ?activity COUNT(*) as ?cnt
FROM &lt;http://extbi.lab.aau.dk/resource/business>
WHERE {
  ?productionUnit bus:hasActivity ?activity .
  ?productionUnit bus:officialAddress ?address .
  ?address bus:positionedAt ?addressFeature .
  &lt;http://extbi.lab.aau.dk/resource/business/municipality/aalborg#this> bus:contains ?addressFeature .
}
GROUP BY ?activity
			</pre>
        </article>
        <article>
          <h2>Aggregated Query Template 5</h2>
            <pre>

SELECT ?participant ?format COUNT(*) as ?cnt
FROM &lt;http://extbi.lab.aau.dk/resource/agriculture>
FROM &lt;http://extbi.lab.aau.dk/resource/business>
WHERE {
  ?company bus:owns ?orgField .
  ?participant bus:participatingIn ?company .
  ?company bus:hasFormat ?format .
}
GROUP BY ?participant ?format
			</pre>
        </article>
        <article>
          <h2>Normal Query Template 1</h2>
            <pre>
 
SELECT ?name ?phone 
FROM &lt;http://extbi.lab.aau.dk/resource/agriculture>
FROM &lt;http://extbi.lab.aau.dk/resource/business>
WHERE {
  ?company bus:owns ?orgField .
  ?field owl:sameAs ?orgField .
  ?field agri:produces &lt;http://extbi.lab.aau.dk/resource/agriculture/crop/vaarbyg_1#this> .
  ?company bus:name ?name .
  ?company bus:telephoneNumberIdentifier ?phone .
}
			</pre>
        </article>
        <article>
          <h2>Normal Query Template 2</h2>
            <pre>

SELECT ?name ?address
FROM &lt;http://extbi.lab.aau.dk/resource/business>
WHERE {
  &lt;http://extbi.lab.aau.dk/resource/business/company/10024862#this> bus:postalAddress ?address .
  &lt;http://extbi.lab.aau.dk/resource/business/company/10024862#this> bus:name ?name .
};
			</pre>
        </article>
        <article>
          <h2>Normal Query Template 3</h2>
            <pre>

SELECT ?company ?area
FROM &lt;http://extbi.lab.aau.dk/resource/agriculture>
FROM &lt;http://extbi.lab.aau.dk/resource/business>
WHERE {
  ?company bus:owns ?orgField .
  ?orgField agri:area ?area .
  FILTER (xsd:double(?area) >= 2) .
}

			</pre>
        </article>
        <article>
          <h2>Normal Query Template 4</h2>
            <pre>

SELECT ?field ?crop
FROM &lt;http://extbi.lab.aau.dk/resource/agriculture>
WHERE {
  ?field agri:produces ?crop .
  ?field wgs:long ?long .
  ?field wgs:lat ?lat .
  FILTER(xsd:double(?long) > 10 && xsd:double(?long) < 11 &&
    xsd:double(?lat) > 54 && xsd:double(?lat) < 55) .
  FILTER NOT EXISTS { ?field owl:sameAs ?organicField .
                      ?organicField a agri:OrganicField . }
}
			</pre>
        </article>
        <article>
          <h2>Normal Query Template 5</h2>
            <pre>

SELECT ?participantName ?companyName ?phone
FROM &lt;http://extbi.lab.aau.dk/resource/agriculture>
FROM &lt;http://extbi.lab.aau.dk/resource/business>
WHERE {
  ?company bus:owns &lt;http://extbi.lab.aau.dk/resource/agriculture/organicfield/154652_15-0#this> .
  ?company bus:telephoneNumberIdentifier ?phone .
  ?company bus:name ?companyName .
  ?participant bus:participatingIn ?company .
  ?participant bus:name ?participantName .
}
			</pre>
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