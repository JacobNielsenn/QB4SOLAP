<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<title>EXTBI</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" href="../styles/layout.css" type="text/css">
<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
</head>
<body class="queries swod">
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
<details>
<summary>Virtuoso queries Snowflake pattern</summary>
      <section>
        <article>
        <h2>Prefix</h2>
          <pre>
prefix xsd: &lt;http://www.w3.org/2001/XMLSchema#> 
prefix ltpch: &lt;http://extbi.lab.aau.dk/ontology/ltpch/>
          </pre>
        </article>
        <article>
          <h2>Query 1</h2>
            <pre>
select
  ?l_returnflag 
  ?l_linestatus 
  (sum(xsd:decimal(?l_linequantity)) as ?sum_qty) 
  (ROUND(sum(xsd:decimal(?l_lineextendedprice))*100)/100 as ?sum_base_price)
  (ROUND(sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount)))*100)/100 as ?sum_disc_price)
  (ROUND(sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))*(1 + xsd:decimal(?l_linetax)))*100)/100 as ?sum_charge)
  (ROUND(avg(xsd:decimal(?l_linequantity))*100)/100 as ?avg_qty)
  (ROUND(avg(xsd:decimal(?l_lineextendedprice))*100)/100 as ?avg_price)
  (ROUND(avg(xsd:decimal(?l_linediscount))*100)/100 as ?avg_disc)  
  (count(1) as ?count_order)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_returnflag ?l_returnflag ;
       ltpch:l_linestatus ?l_linestatus ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linetax ?l_linetax ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:l_linediscount ?l_linediscount .
   filter (xsd:boolean(xsd:dateTime(?l_shipdate) <= xsd:dateTime(bif:dateadd ("day", -%DELTA%, "1998-12-01"^^xsd:date))))
} 
group by
  ?l_returnflag
  ?l_linestatus
order by
  ?l_returnflag
  ?l_linestatus


			</pre>
        </article>
        <article>
          <h2>Query 2</h2>
            <pre>
select
  ?s_acctbal,
  ?s_name,
  ?nation_name,
  ?p_partkey,
  ?p_mfgr,
  ?s_address,
  ?s_phone,
  ?s_comment
where {
  ?ps a ltpch:partsupp;
      ltpch:ps_has_supplier ?supp;
      ltpch:ps_has_part ?part ;
      ltpch:ps_supplycost ?minsc .
  ?supp a ltpch:supplier ;
     ltpch:s_acctbal ?s_acctbal ;
   ltpch:s_name ?s_name ;
     ltpch:s_has_nation ?s_has_nation ;
     ltpch:s_address ?s_address ;
     ltpch:s_phone ?s_phone ;
     ltpch:s_comment ?s_comment .
  ?s_has_nation ltpch:n_name ?nation_name ;
     ltpch:n_has_region ?s_has_region .
  ?s_has_region ltpch:r_name "%REGION%" .
  ?part a ltpch:part ;
      ltpch:p_partkey ?p_partkey ;
      ltpch:p_mfgr ?p_mfgr ;
      ltpch:p_size "%SIZE%" ;
      ltpch:p_type ?p_type .
  { select ?part min(?s_cost) as ?minsc
    where {
        ?ps a ltpch:partsupp;
            ltpch:ps_has_part ?part;
            ltpch:ps_has_supplier ?ms;
            ltpch:ps_supplycost ?s_cost .
        ?ms ltpch:s_has_nation ?m_has_nation .
        ?m_has_nation ltpch:n_has_region ?m_has_region .
        ?m_has_region ltpch:r_name "%REGION%" .
      } 
    }
    filter (?p_type like "%%TYPE%") 
  }
order by
  desc (?s_acctbal)
  ?nation_name
  ?s_name
  ?p_partkey
limit 100


			</pre>
        </article>
        <article>
          <h2>Query 3</h2>
            <pre>

select
  ?o_orderkey
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?o_orderdate
  ?o_shippriority
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
    ltpch:l_lineextendedprice ?l_lineextendedprice ;
    ltpch:l_linediscount ?l_linediscount ;
    ltpch:l_has_order ?ord ;
    ltpch:l_shipdate ?l_shipdate .
  ?ord ltpch:o_orderdate ?o_orderdate ;
    ltpch:o_shippriority ?o_shippriority ;
    ltpch:o_orderkey ?o_orderkey ;
    ltpch:o_has_customer ?cust .
  ?cust ltpch:c_mktsegment ?c_mktsegment .
  filter ((xsd:dateTime(?o_orderdate) < xsd:dateTime("%DATE%"^^xsd:date)) &&
    (xsd:dateTime(?l_shipdate) > xsd:dateTime("%DATE%"^^xsd:date)) &&
    (?c_mktsegment = "%SEGMENT%") ) 
}
group by
  ?o_orderkey
  ?o_orderdate
  ?o_shippriority
order by
  desc (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
  ?o_orderdate
limit 10

			</pre>
        </article>
        <article>
          <h2>Query 4</h2>
            <pre>

select
  ?o_orderpriority
  (count(*) as ?order_count)
where  
{
  {
    select distinct
      ?o_orderpriority
      ?ord
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_commitdate ?l_commitdate ;
          ltpch:l_receiptdate ?l_receiptdate .
      ?ord ltpch:o_orderpriority ?o_orderpriority ;
           ltpch:o_orderdate ?o_orderdate .
      filter (
        (xsd:boolean(xsd:dateTime(?l_commitdate) < xsd:dateTime(?l_receiptdate))) &&
        (xsd:boolean(xsd:dateTime(?o_orderdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date))) &&
        (xsd:boolean(xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date))))
      )
    }
  }
}
group by
  ?o_orderpriority
order by
  ?o_orderpriority


			</pre>
        </article>
        <article>
          <h2>Query 5</h2>
            <pre>

select
  ?nation
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where  {
   ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_has_partsupplier ?ps ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount .
    ?ord ltpch:o_has_customer ?cust ;
         ltpch:o_orderdate ?o_orderdate .
    ?ps ltpch:ps_has_supplier ?supp .
    ?supp ltpch:s_has_nation ?s_nation .
    ?s_nation ltpch:n_has_region ?s_region ;
              ltpch:n_name ?nation .
    ?s_region ltpch:r_name ?r_name .
    ?cust ltpch:c_has_nation ?c_nation.
    filter ((?c_nation = ?s_nation) &&
      (xsd:dateTime(?o_orderdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("year", 1,"%YEAR%-01-01" ^^xsd:date))) &&
      (?r_name = "%REGION%") ) 
  }
group by
  ?nation
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))

			</pre>
        </article>
        <article>
          <h2>Query 6</h2>
            <pre>
 
select
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_shipdate ?l_shipdate .
    filter ( (xsd:dateTime(?l_shipdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date))) &&
      (xsd:decimal(?l_linediscount) >= %DISCOUNT% - 0.01) &&
      (xsd:decimal(?l_linediscount) <= %DISCOUNT% + 0.01) &&
      (xsd:decimal(?l_linequantity) < %QUANTITY%) ) 
}


			</pre>
        </article>
        <article>
          <h2>Query 7</h2>
            <pre>

select 
  ?supp_nation 
  ?cust_nation 
  ?li_year
  (sum (?volume) as ?revenue)
where {
  {
    select
      ?supp_nation
      ?cust_nation
      ?li_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_has_order ?ord ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount .
      ?ord ltpch:o_has_customer ?cust .
      ?cust ltpch:c_has_nation ?custn .
      ?custn ltpch:n_name ?cust_nation .
      ?ps ltpch:ps_has_supplier ?supp .
      ?supp ltpch:s_has_nation ?suppn .
      ?suppn ltpch:n_name ?supp_nation .
      BIND (SUBSTR(STR(?l_shipdate), 1,4) as ?li_year)
      filter ((
        (?cust_nation = "%NATION1%" && ?supp_nation = "%NATION2%") ||
        (?cust_nation = "%NATION2%" && ?supp_nation = "%NATION1%") ) &&
        (xsd:dateTime(?l_shipdate) >= xsd:dateTime("1995-01-01"^^xsd:date)) &&
        (xsd:dateTime(?l_shipdate) <= xsd:dateTime("1996-12-31"^^xsd:date)) ) 
      } 
   } 
}
group by
  ?supp_nation
  ?cust_nation
  ?li_year
order by
  ?supp_nation
  ?cust_nation
  ?li_year

			</pre>
        </article>
        <article>
          <h2>Query 8</h2>
            <pre>

select
  ?o_year
  ((?sum1 / ?sum2) as ?mkt_share)
where {
  { select
    ?o_year
    (sum (?volume * bif:equ (?nation, "%NATION%")) as ?sum1)
    (sum (?volume) as ?sum2)
    where {
      { select
           ((YEAR (xsd:dateTime(?o_orderdate))) as ?o_year)
           ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
           ?nation
         where {
           ?li qb:dataSet ltpch:lineitemCube ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_has_order ?ord ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_lineextendedprice ?l_lineextendedprice ;
               ltpch:l_linediscount ?l_linediscount .
           ?ps ltpch:ps_has_supplier ?s_supplier .
           ?s_supplier ltpch:s_has_nation ?n2 .
           ?n2 ltpch:n_name ?nation .
           ?ps ltpch:ps_has_part ?part .
           ?part ltpch:p_type ?type .
           ?ord ltpch:o_orderdate ?o_orderdate ;
             ltpch:o_has_customer ?c_customer .
           ?c_customer ltpch:c_has_nation ?n_nation .
           ?n_nation ltpch:n_has_region ?r_region .
           ?r_region ltpch:r_name ?region.
           filter ((xsd:dateTime(?o_orderdate) >= xsd:dateTime("1995-01-01"^^xsd:date)) &&
             (xsd:dateTime(?o_orderdate) <= xsd:dateTime("1996-12-31"^^xsd:date) &&
              ?region = "%REGION%" &&
              ?type = "%TYPE%") 
           ) 
        } 
      } 
    }
    group by
      ?o_year 
  } 
}
order by
  ?o_year


			</pre>
        </article>
        <article>
          <h2>Query 9</h2>
            <pre>

select
  ?nation
  ?o_year
  (sum(?amount) as ?sum_profit)
where {
  { select
      ?nation
      ?o_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) - xsd:decimal(?ps_supplycost) * xsd:decimal(?l_linequantity)) as ?amount)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_has_partsupplier ?ps ;
          ltpch:l_linequantity ?l_linequantity ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount .
      ?ps ltpch:ps_has_part ?part ;
          ltpch:ps_has_supplier ?supp .
      ?supp ltpch:s_has_nation ?s_nation .
      ?s_nation ltpch:n_name ?nation .
      ?ord ltpch:o_orderdate ?o_orderdate .
      ?ps ltpch:ps_supplycost ?ps_supplycost .
      ?part ltpch:p_name ?p_name .
      filter (REGEX (?p_name, "%COLOR%"))
      BIND (SUBSTR(STR(?o_orderdate), 1,4) as ?o_year)
    } 
  } 
}
group by
  ?nation
  ?o_year
order by
  ?nation
  desc (?o_year)

			</pre>
        </article>
        <article>
          <h2>Query 10</h2>
            <pre>

select
  ?c_custkey
  ?c_companyName
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
      ltpch:l_returnflag ?l_returnflag ;
      ltpch:l_has_order ?ord ;
      ltpch:l_lineextendedprice ?l_lineextendedprice ;
      ltpch:l_linediscount ?l_linediscount .
  ?ord ltpch:o_has_customer ?cust ;
       ltpch:o_orderdate ?o_orderdate .
  ?cust ltpch:c_address ?c_address ;
      ltpch:c_phone ?c_phone ;
      ltpch:c_comment ?c_comment ;
      ltpch:c_acctbal ?c_acctbal ;
      ltpch:c_custkey ?c_custkey ;
      ltpch:c_has_nation ?c_nation ;
      ltpch:c_name ?c_companyName .
   ?c_nation ltpch:n_name ?nation .
   filter ((xsd:boolean(xsd:dateTime(?o_orderdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date))) &&
      (xsd:boolean(xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date)))) &&
      (xsd:boolean(?l_returnflag = "R")) 
   ) 
}
group by
  ?c_custkey
  ?c_companyName
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
limit 20

			</pre>
        </article>
        <article>
          <h2>Query 11</h2>
          <pre>
select
  ?bigpspart,
  ?bigpsvalue
where {
      { select
          ?bigpspart,
          sum(xsd:decimal(?b_supplycost) * xsd:decimal(?b_availqty)) as ?bigpsvalue
        where
          {
            ?bigps a ltpch:partsupp ;
                   ltpch:ps_has_part ?bigpspart ;
                   ltpch:ps_supplycost ?b_supplycost ;
                   ltpch:ps_availqty ?b_availqty ;
                   ltpch:ps_has_supplier ?b_supplier .
            ?b_supplier ltpch:s_has_nation ?b_nation .
            ?b_nation ltpch:n_name "%NATION%" .
          }
      }
    filter (?bigpsvalue > (
      select
        (sum(xsd:decimal(?t_supplycost) * xsd:decimal(?t_availqty) * %FRACTION%)) as ?threshold
      where
        {
          ?thr_ps a ltpch:partsupp ;
                  ltpch:ps_has_part ?t_part ;
                  ltpch:ps_supplycost ?t_supplycost ;
                  ltpch:ps_availqty ?t_availqty ;
                  ltpch:ps_has_supplier ?t_supplier .
          ?t_supplier ltpch:s_has_nation ?t_nation .
          ?t_nation ltpch:n_name "%NATION%" .
        }
      )
    )
  }
order by
  desc (?bigpsvalue)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 12</h2>
          <pre>
select
  ?l_shipmode
  (sum (
    bif:__or (
      bif:equ (?o_orderpriority, "1-URGENT"),
      bif:equ (?o_orderpriority, "2-HIGH") ) ) as ?high_line_count)
  (sum (1 -
    bif:__or (
      bif:equ (?o_orderpriority, "1-URGENT"),
      bif:equ (?o_orderpriority, "2-HIGH") ) ) as ?low_line_count)
where  {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_commitdate ?l_commitdate ;
       ltpch:l_receiptdate ?l_receiptdate ;
       ltpch:l_shipmode ?l_shipmode ;
       ltpch:l_shipdate ?l_shipdate .
    ?ord ltpch:o_orderpriority ?o_orderpriority .
    filter (xsd:boolean(?l_shipmode in ("%SHIPMODE1%", "%SHIPMODE2%")) &&
      (xsd:boolean(xsd:dateTime(?l_commitdate) < xsd:dateTime(?l_receiptdate))) &&
      (xsd:boolean(xsd:dateTime(?l_shipdate) < xsd:dateTime(?l_commitdate))) &&
      (xsd:boolean(xsd:dateTime(?l_receiptdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date))) &&
      (xsd:boolean(xsd:dateTime(?l_receiptdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date)))) )
  }
group by
  ?l_shipmode
order by
  ?l_shipmode

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 13</h2>
          <pre>
select
  ?c_count
  (count(1) as ?custdist)
where {
    { select
        ?c_custkey
        (count (?ord) as ?c_count)
      where
        {
          ?cust ltpch:c_custkey ?c_custkey .
           optional {
             ?ord a ltpch:orders ;
                  ltpch:o_has_customer ?cust ;
                  ltpch:o_comment ?o_comment .
              filter (!( REGEX (?o_comment , "%WORD1%.*%WORD2%" ) ) ) . 
          }
        }
      group by 
        ?c_custkey
    }
  }
group by
  ?c_count
order by
  desc (count(1))
  desc (?c_count)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 14</h2>
          <pre>
select
       (100 * sum(bif:equ(bif:LEFT(?p_type, 5), "PROMO") * xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))  / sum(xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))) as ?promo_revenue
where
{

    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:ps_has_part ?part .
    ?part ltpch:p_type ?p_type .
    filter (xsd:dateTime(?l_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date) &&
      (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd("month", 1, "%MONTH%-01"^^xsd:date))) )
}

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 15</h2>
          <pre>
select
  ?s_suppkey
  ?s_name
  ?s_address
  ?s_phone
  ?total_revenue
where  {
    ?supplier a ltpch:supplier ;
        ltpch:s_suppkey ?s_suppkey ;
        ltpch:s_name ?s_name ;
        ltpch:s_address ?s_address ;
        ltpch:s_phone ?s_phone .
    { select
          ?supplier
          (sum(xsd:decimal(?l_extendedprice) * (1 - xsd:decimal(?l_discount))) as ?total_revenue)
       where {
            ?li1 qb:dataSet ltpch:lineitemCube ;
                 ltpch:l_shipdate ?l_shipdate ;
                 ltpch:l_lineextendedprice ?l_extendedprice ;
                 ltpch:l_linediscount ?l_discount ;
                 ltpch:l_has_partsupplier ?ps1 .
            ?ps1 ltpch:ps_has_supplier ?supplier .
            filter (
                xsd:dateTime(?l_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date) &&
                xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date)) )
        }
      group by
        ?supplier
      }
      { select (max (?l2_total_revenue) as ?maxtotal)
        where {
            { select
                  ?supplier2
                  (sum(xsd:decimal(?l2_extendedprice) * (1 - xsd:decimal(?l2_discount))) as ?l2_total_revenue)
               where {
                    ?li2 qb:dataSet ltpch:lineitemCube ;
                      ltpch:l_shipdate ?l2_shipdate ;
                      ltpch:l_lineextendedprice ?l2_extendedprice ;
                      ltpch:l_linediscount ?l2_discount ;
                       ltpch:l_has_partsupplier ?ps2 .
                  ?ps2 ltpch:ps_has_supplier ?supplier2 .
                    filter (
                        xsd:dateTime(?l2_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date) &&
                        xsd:dateTime(?l2_shipdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date)) )
               }
               group by 
                ?supplier2
            }
        }
    }
    filter (?total_revenue = ?maxtotal)
}
order by
  ?supplier

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 16</h2>
          <pre>
select
  ?p_brand,
  ?p_type,
  ?p_size,
  (count(distinct ?supp)) as ?supplier_cnt
where {
    ?ps a ltpch:partsupp ;
        ltpch:ps_has_part ?part ;
        ltpch:ps_has_supplier ?supp .
    ?part ltpch:p_brand ?p_brand ;
        ltpch:p_type ?p_type ;
        ltpch:p_size ?p_size .    
    filter (
      (?p_brand != "%BRAND%") &&
      !(?p_type like "%TYPE%%") &&
      (xsd:integer(?p_size) in (%SIZE1%, %SIZE2%, %SIZE3%, %SIZE4%, %SIZE5%, %SIZE6%, %SIZE7%, %SIZE8%))
    )
    filter NOT EXISTS {
       ?supp a ltpch:supplier;
             ltpch:s_comment ?badcomment .
       filter (?badcomment like "%Customer%Complaints%") 
    }
  }
group by
  ?p_brand
  ?p_type
  ?p_size
order by
  desc ((count(distinct ?supp)))
  ?p_brand
  ?p_type
  ?p_size

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 17</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice)) / 7.0) as ?avg_yearly)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:ps_has_part ?part .
    ?part ltpch:p_brand ?p_brand ;
          ltpch:p_container ?p_container .
          {
            select 
              ?part
              ((0.2 * avg(xsd:decimal(?l2_linequantity))) as ?threshold)
            where { 
              ?li2  a ltpch:lineitem ;
                    ltpch:l_linequantity ?l2_linequantity ; 
                    ltpch:l_has_partsupplier ?ps2 .
              ?ps2  ltpch:ps_has_part ?part .
          } 
          group by
            ?part
        }
    filter (xsd:decimal(?l_linequantity) < ?threshold && REGEX(?p_brand,"%BRAND%","i") && ?p_container = "%CONTAINER%") 
}

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 18</h2>
          <pre>
select
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
   (sum(xsd:decimal(?l_linequantity)) as ?l_quantity)
where {
    ?li qb:dataSet ltpch:lineitemCube  ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_order ?ord .
    ?ord ltpch:o_orderkey ?o_orderkey ;
         ltpch:o_orderdate ?o_orderdate ;
         ltpch:o_ordertotalprice ?o_ordertotalprice ;
         ltpch:o_has_customer ?cust .
    ?cust ltpch:c_custkey ?c_custkey ;
          ltpch:c_name ?c_name .  
    { select 
         ?sum_order 
         (sum (xsd:decimal(?l2_linequantity)) as ?sum_q)
       where {
           ?li2 qb:dataSet ltpch:lineitemCube ;
                ltpch:l_linequantity ?l2_linequantity ;
                ltpch:l_has_order ?sum_order .
       }
       group by ?sum_order 
    } .
    filter (?sum_order = ?ord && xsd:decimal(?sum_q) > xsd:decimal(%QUANTITY%))
}
group by
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
order by
  desc (?o_ordertotalprice)
  ?o_orderdate
limit 100

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 19</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_shipmode ?l_shipmode ;
        ltpch:l_shipinstruct ?l_shipinstruct ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:ps_has_part ?part .
     ?part ltpch:p_brand ?p_brand ;
          ltpch:p_size ?p_size ;
          ltpch:p_container ?p_container .
     
     filter (?l_shipmode in ("AIR", "AIR REG") &&
      ?l_shipinstruct = "DELIVER IN PERSON" &&
      ( ( (REGEX(?p_brand,"^%BRAND1%$","i")) &&
          (?p_container in ("SM CASE", "SM BOX", "SM PACK", "SM PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY1%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY1% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 5) ) ||
        ( (REGEX(?p_brand,"^%BRAND2%$","i")) &&
          (?p_container in ("MED BAG", "MED BOX", "MED PKG", "MED PACK")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY2%) && 
          (xsd:integer(?l_linequantity) <= %QUANTITY2% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 10) ) ||
        ( (REGEX(?p_brand,"^%BRAND3%$","i")) &&
          (?p_container in ("LG CASE", "LG BOX", "LG PACK", "LG PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY3%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY3% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 15) ) ) )
  }

          </pre>
        </article>
        <article>
          <h2>Query 20</h2>
          <pre>
select
  ?s_name
  ?s_address
where
{
  ?supp ltpch:s_name ?s_name ;
        ltpch:s_address ?s_address .
  { 
    select distinct 
      ?supp 
    where 
    {
      ?big_ps ltpch:ps_has_part ?part ;
              ltpch:ps_availqty ?big_ps_availqty ;
              ltpch:ps_has_supplier ?supp .
      ?supp ltpch:s_has_nation ?s_nation .
      ?s_nation ltpch:n_name ?n_name .
      ?part ltpch:p_name ?p_name . 
      filter (REGEX (?p_name , "^%COLOR%") && 
              ?n_name = "%NATION%" && 
              xsd:decimal(?big_ps_availqty) > ?qty_threshold)
      {
        select 
          ((0.5 * sum(xsd:decimal(?l_linequantity))) as ?qty_threshold)
          ?big_ps
        where
        {
          ?li qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l_shipdate ;
              ltpch:l_linequantity ?l_linequantity ;
              ltpch:l_has_partsupplier ?big_ps .
          filter ((xsd:dateTime(?l_shipdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
            (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date)))
          )
        }
        group by 
          ?big_ps
      }
    } 
  }
}
order by ?s_name

          </pre>
        </article>
        <article>
          <h2>Query 21</h2>
          <pre>
select
    ?s_name
    (count(1) as ?numwait)
where {
          ?li1 qb:dataSet ltpch:lineitemCube;
              ltpch:l_receiptdate ?l1_receiptdate ;
              ltpch:l_commitdate ?l1_commitdate ;
              ltpch:l_has_partsupplier ?ps ;
              ltpch:l_has_order ?ord .
          ?ps ltpch:ps_has_supplier ?supp .
          ?supp ltpch:s_name ?s_name ;
               ltpch:s_has_nation ?s_nation .
          ?ord ltpch:o_orderstatus ?orderstatus .
          ?s_nation ltpch:n_name ?name
          filter ( 
            xsd:boolean(xsd:dateTime(?l1_receiptdate) > xsd:dateTime(?l1_commitdate)) && 
            ?name = "%NATION%" && 
            ?orderstatus = "F"
            ) 
          filter exists {
            ?li2 ltpch:l_has_order ?ord ;
                 ltpch:l_has_partsupplier ?ps2 .
            ?ps2 ltpch:ps_has_supplier ?supp2 .
            filter (?supp != ?supp2)
          }
          filter not exists {
              ?li3 ltpch:l_has_order ?ord ;
                   ltpch:l_receiptdate ?l3_receiptdate ;
                   ltpch:l_commitdate ?l3_commitdate ;
                   ltpch:l_has_partsupplier ?ps3 .
              ?ps3 ltpch:ps_has_supplier ?supp3 .
              filter (
                 xsd:boolean(xsd:dateTime(?l3_receiptdate) > xsd:dateTime(?l3_commitdate)) &&
                 ?supp3 != ?supp
              )
         }
       }
group by
   ?s_name
order by
    desc (count(1))
    ?s_name
limit 100

          </pre>
        </article>
        <article>
          <h2>Query 22</h2>
          <pre>
select
  (bif:LEFT (?c_phone, 2)) as ?cntrycode,
  (count (1)) as ?numcust,
  sum (xsd:decimal(?c_acctbal)) as ?totacctbal
where {
    ?cust a ltpch:customer ;
      ltpch:c_acctbal ?c_acctbal ;
      ltpch:c_phone ?c_phone .
      {
        select (avg (xsd:decimal(?c_acctbal2))) as ?acctbal_threshold
          where
            {
              ?cust2 a ltpch:customer ;
                 ltpch:c_acctbal ?c_acctbal2 ;
                 ltpch:c_phone ?c_phone2 .
              filter ((xsd:decimal(?c_acctbal2) > 0.00) &&
                bif:LEFT (?c_phone2, 2) in (%COUNTRY_CODE_SET%) )
            }
      }
    filter (
      bif:LEFT (?c_phone, 2) in (%COUNTRY_CODE_SET%) &&
      (xsd:decimal(?c_acctbal) > ?acctbal_threshold )
    ) 
    filter not exists { ?ord ltpch:o_has_customer ?cust }
  }
group by (bif:LEFT (?c_phone, 2))
order by (bif:LEFT (?c_phone, 2))

          </pre>
        </article>
      </section>
</details>
<details>
<summary>Virtuoso queries star pattern</summary>
      <section>
        <article>
        <h2>Prefix</h2>
          <pre>
prefix xsd: &lt;http://www.w3.org/2001/XMLSchema#> 
prefix ltpch: &lt;http://extbi.lab.aau.dk/ontology/ltpch/>
          </pre>
        </article>
        <article>
          <h2>Query 1</h2>
            <pre>
select
  ?l_returnflag 
  ?l_linestatus 
  (sum(xsd:decimal(?l_linequantity)) as ?sum_qty) 
  (ROUND(sum(xsd:decimal(?l_lineextendedprice))*100)/100 as ?sum_base_price)
  (ROUND(sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount)))*100)/100 as ?sum_disc_price)
  (ROUND(sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))*(1 + xsd:decimal(?l_linetax)))*100)/100 as ?sum_charge)
  (ROUND(avg(xsd:decimal(?l_linequantity))*100)/100 as ?avg_qty)
  (ROUND(avg(xsd:decimal(?l_lineextendedprice))*100)/100 as ?avg_price)
  (ROUND(avg(xsd:decimal(?l_linediscount))*100)/100 as ?avg_disc)  
  (count(1) as ?count_order)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_returnflag ?l_returnflag ;
       ltpch:l_linestatus ?l_linestatus ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linetax ?l_linetax ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:l_linediscount ?l_linediscount .
   filter (xsd:dateTime(?l_shipdate) <= xsd:dateTime(bif:dateadd ("day", -%DELTA%, "1998-12-01"^^xsd:date)))
} 
group by
  ?l_returnflag
  ?l_linestatus
order by
  ?l_returnflag
  ?l_linestatus

            </pre>
        </article>
        <article>
          <h2>Query 2</h2>
            <pre>
select
  ?s_acctbal,
  ?s_name,
  ?nation_name,
  ?p_partkey,
  ?p_mfgr,
  ?s_address,
  ?s_phone,
  ?s_comment
where {
  ?ps ltpch:supplier_acctbal ?s_acctbal ;
    ltpch:supplier_name ?s_name ;
    ltpch:partsupplier_supplycost ?minsc ;    
    ltpch:supplier_address ?s_address ;
    ltpch:supplier_phone ?s_phone ;
    ltpch:supplier_comment ?s_comment ;
    ltpch:nation_name ?nation_name ;
    ltpch:region_name "%REGION%" ;
    ltpch:part_partkey ?p_partkey ;
    ltpch:part_mfgr ?p_mfgr ;
    ltpch:part_size ?size ;
    ltpch:part_type ?p_type .
  FILTER (?size = str(%SIZE%) && contains(?p_type, "%TYPE%"))
  { select ?p_partkey  min(?s_cost) as ?minsc
    where {
        ?ps ltpch:part_partkey ?p_partkey;
            ltpch:partsupplier_supplycost ?s_cost ;
            ltpch:region_name ?region2 .
            filter (?region2 = "%REGION%")
      } 
    }
     
  }
order by
  desc (?s_acctbal)
  ?nation_name
  ?s_name
  ?p_partkey
limit 100


            </pre>
        </article>
        <article>
          <h2>Query 3</h2>
            <pre>
select
  ?o_orderkey
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?o_orderdate
  ?o_shippriority
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
    ltpch:l_lineextendedprice ?l_lineextendedprice ;
    ltpch:l_linediscount ?l_linediscount ;
    ltpch:l_has_order ?ord ;
    ltpch:l_shipdate ?l_shipdate .
  ?ord ltpch:order_orderdate ?o_orderdate ;
    ltpch:order_shippriority ?o_shippriority ;
    ltpch:order_orderkey ?o_orderkey ;
    ltpch:customer_mktsegment ?c_mktsegment .
  filter ((xsd:dateTime(?o_orderdate) < xsd:dateTime("%DATE%"^^xsd:date)) &&
    (xsd:dateTime(?l_shipdate) > xsd:dateTime("%DATE%"^^xsd:date)) &&
    (?c_mktsegment = "%SEGMENT%") ) 
}
group by
  ?o_orderkey
  ?o_orderdate
  ?o_shippriority
order by
  desc (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
  ?o_orderdate
limit 10
            </pre>
        </article>
        <article>
          <h2>Query 4</h2>
            <pre>
select
  ?o_orderpriority
  (count(*) as ?order_count)
where  
{
  {
    select distinct
      ?o_orderpriority
      ?ord
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_commitdate ?l_commitdate ;
          ltpch:l_receiptdate ?l_receiptdate .
      ?ord ltpch:order_orderpriority ?o_orderpriority ;
           ltpch:order_orderdate ?o_orderdate .
      filter (
        (xsd:boolean(xsd:dateTime(?l_commitdate) < xsd:dateTime(?l_receiptdate))) &&
        (xsd:boolean(xsd:dateTime(?o_orderdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date))) &&
        (xsd:boolean(xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date))))
      )
    }
  }
}
group by
  ?o_orderpriority
order by
  ?o_orderpriority

            </pre>
        </article>
        <article>
          <h2>Query 5</h2>
            <pre>
select
  ?nation
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where  {
   ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_has_partsupplier ?ps ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount .
    ?ord ltpch:order_orderdate ?o_orderdate ;
         ltpch:nation_name ?c_nation .
    ?ps ltpch:nation_name ?nation ;
        ltpch:region_name ?r_name .
    
    filter ((?c_nation = ?nation) &&
      (xsd:dateTime(?o_orderdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("year", 1,"%YEAR%-01-01" ^^xsd:date))) &&
      (?r_name = "%REGION%") ) 
  }
group by
  ?nation
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))

            </pre>
        </article>
        <article>
          <h2>Query 6</h2>
            <pre>
select
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_shipdate ?l_shipdate .
    filter ( (xsd:dateTime(?l_shipdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date))) &&
      (xsd:decimal(?l_linediscount) >= %DISCOUNT% - 0.01) &&
      (xsd:decimal(?l_linediscount) <= %DISCOUNT% + 0.01) &&
      (xsd:decimal(?l_linequantity) < %QUANTITY%) ) 
}

            </pre>
        </article>
        <article>
          <h2>Query 7</h2>
            <pre>
select 
  ?supp_nation 
  ?cust_nation 
  ?li_year
  (sum (xsd:decimal(?volume)) as ?revenue)
where {
  {
    select
      ?supp_nation
      ?cust_nation
      ?li_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_has_order ?ord ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount .
      ?ord ltpch:nation_name ?cust_nation .
      ?ps ltpch:nation_name ?supp_nation .
      BIND (SUBSTR(STR(?l_shipdate), 1,4) as ?li_year)
      filter ((
        (?cust_nation = "%NATION1%" && ?supp_nation = "%NATION2%") ||
        (?cust_nation = "%NATION2%" && ?supp_nation = "%NATION1%") ) &&
        (xsd:dateTime(?l_shipdate) >= xsd:dateTime("1995-01-01"^^xsd:date)) &&
        (xsd:dateTime(?l_shipdate) <= xsd:dateTime("1996-12-31"^^xsd:date)) ) 
      } 
   } 
}
group by
  ?supp_nation
  ?cust_nation
  ?li_year
order by
  ?supp_nation
  ?cust_nation
  ?li_year
            </pre>
        </article>
        <article>
          <h2>Query 8</h2>
            <pre>
select
  ?o_year
  ((?sum1 / ?sum2) as ?mkt_share)
where {
  { select
    ?o_year
    (sum (?volume * bif:equ (?nation, "%NATION%")) as ?sum1)
    (sum (?volume) as ?sum2)
    where {
      { select
           ((YEAR(xsd:dateTime(?o_orderdate))) as ?o_year)
           ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
           ?nation
         where {
           ?li qb:dataSet ltpch:lineitemCube ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_has_order ?ord ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_lineextendedprice ?l_lineextendedprice ;
               ltpch:l_linediscount ?l_linediscount .
           ?ps ltpch:nation_name ?nation ;
               ltpch:part_type ?type .
           ?ord ltpch:order_orderdate ?o_orderdate ;
                ltpch:region_name ?region .
           filter ((xsd:dateTime(?o_orderdate) >= xsd:dateTime("1995-01-01"^^xsd:date)) &&
             (xsd:dateTime(?o_orderdate) <= xsd:dateTime("1996-12-31"^^xsd:date) &&
              ?region = "%REGION%" &&
              ?type = "%TYPE%") 
           ) 
        } 
      } 
    }
    group by
      ?o_year 
  } 
}
order by
  ?o_year

            </pre>
        </article>
        <article>
          <h2>Query 9</h2>
            <pre>
select
  ?nation
  ?o_year
  (sum(?amount) as ?sum_profit)
where {
  { select
      ?nation
      ?o_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) - xsd:decimal(?ps_supplycost) * xsd:decimal(?l_linequantity)) as ?amount)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_has_partsupplier ?ps ;
          ltpch:l_linequantity ?l_linequantity ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount .
      ?ps ltpch:nation_name ?nation ;
          ltpch:partsupplier_supplycost ?ps_supplycost ;
          ltpch:part_name ?p_name .
      ?ord ltpch:order_orderdate ?o_orderdate .
      filter (REGEX (?p_name, "%COLOR%"))
      BIND (SUBSTR(STR(?o_orderdate), 1,4) as ?o_year)
    } 
  } 
}
group by
  ?nation
  ?o_year
order by
  ?nation
  desc (?o_year)

            </pre>
        </article>
        <article>
          <h2>Query 10</h2>
            <pre>
select
  ?c_custkey
  ?c_companyName
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
      ltpch:l_returnflag ?l_returnflag ;
      ltpch:l_has_order ?ord ;
      ltpch:l_lineextendedprice ?l_lineextendedprice ;
      ltpch:l_linediscount ?l_linediscount .
  ?ord ltpch:order_orderdate ?o_orderdate ;
      ltpch:customer_address ?c_address ;
      ltpch:customer_phone ?c_phone ;
      ltpch:customer_comment ?c_comment ;
      ltpch:customer_acctbal ?c_acctbal ;
      ltpch:customer_custkey ?c_custkey ;
      ltpch:customer_name ?c_companyName ;
      ltpch:nation_name ?nation .
   filter ((xsd:dateTime(?o_orderdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date)) &&
      (xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date))) &&
      (?l_returnflag = "R") 
   ) 
}
group by
  ?c_custkey
  ?c_companyName
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
limit 20

            </pre>
        </article>
        <article>
          <h2>Query 11</h2>
          <pre>
select
  ?bigpspart,
  ?bigpsvalue
where {
      { select
          ?bigpspart,
          sum(xsd:decimal(?b_supplycost) * xsd:decimal(?b_availqty)) as ?bigpsvalue
        where
          {
            ?bigps ltpch:part_partkey ?bigpspart ;
                  ltpch:partsupplier_supplycost ?b_supplycost ;
                  ltpch:partsupplier_availqty ?b_availqty ;
                  ltpch:nation_name "%NATION%" .
          }
      }
    filter (?bigpsvalue > (
        select
          (sum(xsd:decimal(?t_supplycost) * xsd:decimal(?t_availqty)) * %FRACTION%) as ?threshold
        where
          {
            ?thr_ps ltpch:partsupplier_supplycost ?t_supplycost ;
                    ltpch:partsupplier_availqty ?t_availqty ;
                    ltpch:nation_name "%NATION%" .
          }
    ))
  }
order by
  desc (?bigpsvalue)
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 12</h2>
          <pre>
select
  ?l_shipmode
  (sum (
    bif:__or (
      bif:equ (?o_orderpriority, "1-URGENT"),
      bif:equ (?o_orderpriority, "2-HIGH") ) ) as ?high_line_count)
  (sum (1 -
    bif:__or (
      bif:equ (?o_orderpriority, "1-URGENT"),
      bif:equ (?o_orderpriority, "2-HIGH") ) ) as ?low_line_count)
where  {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_commitdate ?l_commitdate ;
       ltpch:l_receiptdate ?l_receiptdate ;
       ltpch:l_shipmode ?l_shipmode ;
       ltpch:l_shipdate ?l_shipdate .
    ?ord ltpch:order_orderpriority ?o_orderpriority .
    filter (xsd:boolean(?l_shipmode in ("%SHIPMODE1%", "%SHIPMODE2%")) &&
      (xsd:boolean(xsd:dateTime(?l_commitdate) < xsd:dateTime(?l_receiptdate))) &&
      (xsd:boolean(xsd:dateTime(?l_shipdate) < xsd:dateTime(?l_commitdate))) &&
      (xsd:boolean(xsd:dateTime(?l_receiptdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date))) &&
      (xsd:boolean(xsd:dateTime(?l_receiptdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date)))) )
  }
group by
  ?l_shipmode
order by
  ?l_shipmode

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 13</h2>
          <pre>
select
  ?c_count
  (count(1) as ?custdist)
where {
    { select
        ?c_custkey
        (count (?o_comment) as ?c_count)
      where
        {
          ?ord ltpch:customer_custkey ?c_custkey .
           optional {
             ?ord ltpch:order_comment ?o_comment .
              filter (!( REGEX (?o_comment , "%WORD1%.*%WORD2%" ) ) ) . 
          }
        }
      group by 
        ?c_custkey
    }
  }
group by
  ?c_count
order by
  desc (count(1))
  desc (?c_count)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 14</h2>
          <pre>
select
  ((100 * ?sum1 / ?sum2 ) as ?promo_revenue)
where
{
  select 
    (sum (
          bif:equ(SUBSTR(?p_type, 1, 5), "PROMO") *
          xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) ) as ?sum1)
    (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) ) as ?sum2)
  where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:l_has_partsupplier ?part .
      ?part ltpch:part_type ?p_type .
      filter ((xsd:dateTime(?l_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date)) &&
        (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd("month", 1, "%MONTH%-01"^^xsd:date))) )
  }
}
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 15</h2>
          <pre>
select distinct
  ?s_suppkey
  ?s_name
  ?s_address
  ?s_phone
  ?total_revenue
where  
{
  ?partsupp ltpch:supplier_suppkey ?s_suppkey ;
            ltpch:supplier_name ?s_name ;
            ltpch:supplier_address ?s_address ;
            ltpch:supplier_phone ?s_phone .
  { 
    select
        ?s_suppkey
        ((sum(xsd:decimal(?l_extendedprice) * (1 - xsd:decimal(?l_discount)))) as ?total_revenue)
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:l_lineextendedprice ?l_extendedprice ;
          ltpch:l_linediscount ?l_discount ;
          ltpch:l_has_partsupplier ?ps .
      ?ps ltpch:supplier_suppkey ?s_suppkey .
      filter (
          xsd:dateTime(?l_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date) &&
          xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date)) )
    }
    group by
      ?s_suppkey
  } .
  { 
    select 
      (max (?l2_total_revenue) as ?maxtotal)
    where 
    {
      { 
        select
          ((sum(xsd:decimal(?l2_extendedprice) * (1 - xsd:decimal(?l2_discount)))) as ?l2_total_revenue)
        where 
        {
          ?li2 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l2_shipdate ;
              ltpch:l_lineextendedprice ?l2_extendedprice ;
              ltpch:l_linediscount ?l2_discount ;
              ltpch:l_has_partsupplier ?ps2 .
          ?ps2 ltpch:supplier_suppkey ?s_suppkey2 .
          filter (
              xsd:dateTime(?l2_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date) &&
              xsd:dateTime(?l2_shipdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date)) )
        }
        group by
          ?s_suppkey2
      }
    }
  }
  filter (?total_revenue = ?maxtotal)
}
order by
  ?s_suppkey

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 16</h2>
          <pre>
select
  ?p_brand,
  ?p_type,
  ?p_size,
  (count(distinct ?supp)) as ?supplier_cnt
where {
    ?ps ltpch:part_brand ?p_brand ;
        ltpch:part_type ?p_type ;
        ltpch:part_size ?p_size ;   
        ltpch:supplier_suppkey ?supp .    
    filter (
      (?p_brand != "%BRAND%") &&
      !(?p_type like "%TYPE%%") &&
      (xsd:integer(?p_size) in (%SIZE1%, %SIZE2%, %SIZE3%, %SIZE4%, %SIZE5%, %SIZE6%, %SIZE7%, %SIZE8%))
    )
    filter NOT EXISTS {
       ?ps ltpch:supplier_comment ?badcomment .
       filter (?badcomment like "%Customer%Complaints%") 
    }
  }
group by
  ?p_brand
  ?p_type
  ?p_size
order by
  desc ((count(distinct ?supp)))
  ?p_brand
  ?p_type
  ?p_size

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 17</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice)) / 7.0) as ?avg_yearly)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:part_partkey ?p_partkey.
    {
      select 
        ?p_partkey
        ((0.2 * avg(xsd:decimal(?l2_linequantity))) as ?threshold)
      where { 
        ?li2  a ltpch:lineitem ;
              ltpch:l_linequantity ?l2_linequantity ; 
              ltpch:l_has_partsupplier ?ps2 .
        ?ps2 ltpch:part_partkey ?p_partkey ;
              ltpch:part_container ?p_container ;
              ltpch:part_brand ?p_brand  .
        filter (REGEX(?p_brand,"%BRAND%","i") && ?p_container = "%CONTAINER%" ) 
      }
      group by
        ?p_partkey
    }
    filter (xsd:decimal(?l_linequantity) < xsd:decimal(?threshold)) 
}

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 18</h2>
          <pre>
select
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
   (sum(xsd:decimal(?l_linequantity)) as ?l_quantity)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_order ?ord .
    ?ord ltpch:order_orderkey ?o_orderkey ;
        ltpch:order_orderdate ?o_orderdate ;
        ltpch:order_ordertotalprice ?o_ordertotalprice ;
        ltpch:customer_custkey ?c_custkey ;
        ltpch:customer_name ?c_name .
    { 
      select 
        ?ord 
        (sum (xsd:decimal(?l2_linequantity)) as ?sum_q)
      where 
      {
        ?li2 a ltpch:lineitem ;
             ltpch:l_linequantity ?l2_linequantity ;
             ltpch:l_has_order ?ord .
      }
      group by
        ?ord
    } .
    filter (?sum_q > %QUANTITY%)
}
group by
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
order by
  desc (?o_ordertotalprice)
  ?o_orderdate
limit 100

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 19</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_shipmode ?l_shipmode ;
        ltpch:l_shipinstruct ?l_shipinstruct .
     ?ps ltpch:part_brand ?p_brand ;
          ltpch:part_size ?p_size ;
          ltpch:part_container ?p_container .
     

     filter (?l_shipmode in ("AIR", "AIR REG") &&
      ?l_shipinstruct = "DELIVER IN PERSON" &&
      ( ( (REGEX(?p_brand,"^%BRAND1%$","i")) &&
          (?p_container in ("SM CASE", "SM BOX", "SM PACK", "SM PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY1%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY1% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 5) ) ||
        ( (REGEX(?p_brand,"^%BRAND2%$","i")) &&
          (?p_container in ("MED BAG", "MED BOX", "MED PKG", "MED PACK")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY2%) && 
          (xsd:integer(?l_linequantity) <= %QUANTITY2% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 10) ) ||
        ( (REGEX(?p_brand,"^%BRAND3%$","i")) &&
          (?p_container in ("LG CASE", "LG BOX", "LG PACK", "LG PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY3%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY3% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 15) ) ) )
  }

          </pre>
        </article>
        <article>
          <h2>Query 20</h2>
          <pre>
select distinct
  ?s_name
  ?s_address
where
{
  ?supp ltpch:supplier_name ?s_name ;
        ltpch:supplier_suppkey ?suppkey ;
        ltpch:supplier_address ?s_address .
  { 
    select 
      distinct ?suppkey 
    where 
    {
      ?big_ps ltpch:partsupplier_availqty ?big_ps_availqty ;
              ltpch:supplier_suppkey ?suppkey ;
              ltpch:nation_name ?n_name ;
        ltpch:supplier_suppkey ?suppkey ;
        ltpch:part_partkey ?partkey ;
              ltpch:part_name ?p_name . 
      FILTER(REGEX (?p_name , "^%COLOR%") && ?n_name = "%NATION%") .
      {
        select 
          ?partkey ?suppkey
          ((0.5 * sum(xsd:decimal(?l_linequantity))) as ?qty_threshold)
        where
        {
          ?li qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l_shipdate ;
              ltpch:l_linequantity ?l_linequantity ;
              ltpch:l_has_partsupplier ?big_ps .
          ?big_ps ltpch:part_partkey ?partkey ;
              ltpch:supplier_suppkey ?suppkey .
          FILTER ((xsd:dateTime(?l_shipdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
            (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date)))
          )
        } 
        group by
          ?partkey ?suppkey
      } .
      FILTER(xsd:decimal(?big_ps_availqty) > ?qty_threshold) .
    } 
  }
}
order by ?s_name

          </pre>
        </article>
        <article>
          <h2>Query 21</h2>
          <pre>
select
    ?s_name
    ((count(1)) as ?numwait)
where {
         ?li1 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_receiptdate ?l1_receiptdate ;
              ltpch:l_commitdate ?l1_commitdate ;
              ltpch:l_has_partsupplier ?ps ;
              ltpch:l_has_order ?ord .
         ?ps ltpch:supplier_name ?s_name ;
             ltpch:supplier_suppkey ?suppkey ;
             ltpch:nation_name ?n_name .
         ?ord ltpch:order_orderstatus ?o_orderstatus .
         filter ( xsd:boolean(xsd:dateTime(?l1_receiptdate) > xsd:dateTime(?l1_commitdate)) && ?n_name = "%NATION%" && ?o_orderstatus = "F")
         filter exists {
              ?li2 ltpch:l_has_order ?ord ;
                   ltpch:l_has_partsupplier ?ps2 .
              ?ps2 ltpch:supplier_suppkey ?suppkey2 .
              filter (?suppkey != ?suppkey2)
         }
         filter not exists {
              ?li3 ltpch:l_has_order ?ord ;
                   ltpch:l_receiptdate ?l3_receiptdate ;
                   ltpch:l_commitdate ?l3_commitdate ;
                   ltpch:l_has_partsupplier ?ps3 .
              ?ps3 ltpch:supplier_suppkey ?suppkey3 .
              filter (
                 xsd:boolean(xsd:dateTime(?l3_receiptdate) > xsd:dateTime(?l3_commitdate)) &&
                 ?suppkey3 != ?suppkey
              )
         }
       }
group by
   ?s_name
order by
    desc (count(1))
    ?s_name
limit 100
          </pre>
        </article>
        <article>
          <h2>Query 22</h2>
          <pre>
select
  (bif:LEFT (?c_phone, 2)) as ?cntrycode,
  (count (1)) as ?numcust,
  sum (xsd:decimal(?c_acctbal)) as ?totacctbal
where {
    ?cust ltpch:customer_acctbal ?c_acctbal ;
      ltpch:customer_phone ?c_phone .
      {
select (avg (?acctbal2)) as ?acctbal_threshold
where {      
select (avg (xsd:decimal(?c_acctbal2))) as ?acctbal2
          where
            {
              ?cust2 ltpch:customer_acctbal ?c_acctbal2 ;
                     ltpch:customer_custkey ?custkey2 ;
                 ltpch:customer_phone ?c_phone2 .
              filter ((xsd:decimal(?c_acctbal2) > 0.00) &&
                bif:LEFT (?c_phone2, 2) in (%COUNTRY_CODE_SET%) )
            }
          group by ?custkey2
      }
}
    filter (
      bif:LEFT (?c_phone, 2) in (%COUNTRY_CODE_SET%) &&
      (xsd:decimal(?c_acctbal) > ?acctbal_threshold )
    ) 
    filter not exists { ?cust ltpch:order_orderkey ?orderkey }
  }
group by (bif:LEFT (?c_phone, 2))
order by (bif:LEFT (?c_phone, 2))


          </pre>
        </article>
      </section>
</details>
<details>
<summary>Virtuoso queries denormalized pattern</summary>
      <section>
        <article>
        <h2>Prefix</h2>
          <pre>
prefix xsd: &lt;http://www.w3.org/2001/XMLSchema#> 
prefix ltpch: &lt;http://extbi.lab.aau.dk/ontology/ltpch/>
          </pre>
        </article>
        <article>
          <h2>Query 1</h2>
            <pre>
select
  ?l_returnflag 
  ?l_linestatus 
  (sum(xsd:decimal(?l_linequantity)) as ?sum_qty) 
  (ROUND(sum(xsd:decimal(?l_lineextendedprice))*100)/100 as ?sum_base_price)
  (ROUND(sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount)))*100)/100 as ?sum_disc_price)
  (ROUND(sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))*(1 + xsd:decimal(?l_linetax)))*100)/100 as ?sum_charge)
  (ROUND(avg(xsd:decimal(?l_linequantity))*100)/100 as ?avg_qty)
  (ROUND(avg(xsd:decimal(?l_lineextendedprice))*100)/100 as ?avg_price)
  (ROUND(avg(xsd:decimal(?l_linediscount))*100)/100 as ?avg_disc) 
  (count(1) as ?count_order)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_returnflag ?l_returnflag ;
       ltpch:l_linestatus ?l_linestatus ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linetax ?l_linetax ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:l_linediscount ?l_linediscount .
   filter (xsd:boolean(xsd:dateTime(?l_shipdate) <= xsd:dateTime(bif:dateadd ("day", -%DELTA%, "1998-12-01"^^xsd:date))))
} 
group by
  ?l_returnflag
  ?l_linestatus
order by
  ?l_returnflag
  ?l_linestatus

            </pre>
        </article>
        <article>
          <h2>Query 2</h2>
            <pre>
select distinct
  ?s_acctbal,
  ?s_name,
  ?nation_name,
  ?p_partkey,
  ?p_mfgr,
  ?s_address,
  ?s_phone,
  ?s_comment
where {
  ?ps ltpch:partsupplier_supplier_acctbal ?s_acctbal ;
    ltpch:partsupplier_supplier_name ?s_name ;
    ltpch:partsupplier_partsupplier_supplycost ?minsc ;    
    ltpch:partsupplier_supplier_address ?s_address ;
    ltpch:partsupplier_supplier_phone ?s_phone ;
    ltpch:partsupplier_supplier_comment ?s_comment ;
    ltpch:partsupplier_nation_name ?nation_name ;
    ltpch:partsupplier_region_name "%REGION%" ;
    ltpch:partsupplier_part_partkey ?p_partkey ;
    ltpch:partsupplier_part_mfgr ?p_mfgr ;
    ltpch:partsupplier_part_size ?size ;
    ltpch:partsupplier_part_type ?p_type .
FILTER (?size = str(%SIZE%) && contains(?p_type, "%TYPE%"))
{ select ?p_partkey  min(?s_cost) as ?minsc
    where {
        ?ps ltpch:partsupplier_part_partkey ?p_partkey;
            ltpch:partsupplier_partsupplier_supplycost ?s_cost ;
            ltpch:partsupplier_region_name ?region2 .
            filter (?region2 = "%REGION%")
      } 
    }
     
  }
order by
  desc (?s_acctbal)
  ?nation_name
  ?s_name
  ?p_partkey
limit 100


            </pre>
        </article>
        <article>
          <h2>Query 3</h2>
            <pre>
select
  ?o_orderkey
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?o_orderdate
  ?o_shippriority
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
    ltpch:l_lineextendedprice ?l_lineextendedprice ;
    ltpch:l_linediscount ?l_linediscount ;
    ltpch:l_shipdate ?l_shipdate ;
    ltpch:order_order_orderdate ?o_orderdate ;
    ltpch:order_order_shippriority ?o_shippriority ;
    ltpch:order_order_orderkey ?o_orderkey ;
    ltpch:order_customer_mktsegment ?c_mktsegment .
  filter ((xsd:dateTime(?o_orderdate) < xsd:dateTime("%DATE%"^^xsd:date)) &&
    (xsd:dateTime(?l_shipdate) > xsd:dateTime("%DATE%"^^xsd:date)) &&
    (?c_mktsegment = "%SEGMENT%") ) 
}
group by
  ?o_orderkey
  ?o_orderdate
  ?o_shippriority
order by
  desc (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
  ?o_orderdate
limit 10
            </pre>
        </article>
        <article>
          <h2>Query 4</h2>
            <pre>
select
  ?o_orderpriority
  (count(*) as ?order_count)
where  
{
  {
    select distinct
      ?o_orderpriority
      ?ordkey
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_commitdate ?l_commitdate ;
          ltpch:l_receiptdate ?l_receiptdate ;
          ltpch:order_order_orderpriority ?o_orderpriority ;
          ltpch:order_order_orderkey ?ordkey ;
          ltpch:order_order_orderdate ?o_orderdate .
      filter (
        (xsd:boolean(xsd:dateTime(?l_commitdate) < xsd:dateTime(?l_receiptdate))) &&
        (xsd:boolean(xsd:dateTime(?o_orderdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date))) &&
        (xsd:boolean(xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date))))
      )
    }
  }
}
group by
  ?o_orderpriority
order by
  ?o_orderpriority
            </pre>
        </article>
        <article>
          <h2>Query 5</h2>
            <pre>
select
  ?nation
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where  {
   ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:order_order_orderdate ?o_orderdate ;
       ltpch:order_nation_name ?c_nation ;
       ltpch:partsupplier_nation_name ?nation ;
       ltpch:partsupplier_region_name ?r_name .
    
    filter ((?c_nation = ?nation) &&
      (xsd:dateTime(?o_orderdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("year", 1,"%YEAR%-01-01" ^^xsd:date))) &&
      (?r_name = "%REGION%") ) 
  }
group by
  ?nation
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))

            </pre>
        </article>
        <article>
          <h2>Query 6</h2>
            <pre>
select
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_shipdate ?l_shipdate .
    filter ( (xsd:dateTime(?l_shipdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date))) &&
      (xsd:decimal(?l_linediscount) >= %DISCOUNT% - 0.01) &&
      (xsd:decimal(?l_linediscount) <= %DISCOUNT% + 0.01) &&
      (xsd:decimal(?l_linequantity) < %QUANTITY%) ) 
}

            </pre>
        </article>
        <article>
          <h2>Query 7</h2>
            <pre>
select 
  ?supp_nation 
  ?cust_nation 
  ?li_year
  (sum (xsd:decimal(?volume)) as ?revenue)
where {
  {
    select
      ?supp_nation
      ?cust_nation
      ?li_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:order_nation_name ?cust_nation ;
        ltpch:partsupplier_nation_name ?supp_nation .
      BIND (SUBSTR(STR(?l_shipdate), 1,4) as ?li_year)
      filter ((
        (?cust_nation = "%NATION1%" && ?supp_nation = "%NATION2%") ||
        (?cust_nation = "%NATION2%" && ?supp_nation = "%NATION1%") ) &&
        (xsd:dateTime(?l_shipdate) >= xsd:dateTime("1995-01-01"^^xsd:date)) &&
        (xsd:dateTime(?l_shipdate) <= xsd:dateTime("1996-12-31"^^xsd:date)) ) 
      } 
   } 
}
group by
  ?supp_nation
  ?cust_nation
  ?li_year
order by
  ?supp_nation
  ?cust_nation
  ?li_year

            </pre>
        </article>
        <article>
          <h2>Query 8</h2>
            <pre>
select
  ?o_year
  ((?sum1 / ?sum2) as ?mkt_share)
where {
  { select
    ?o_year
    (sum (?volume * bif:equ (?nation, "%NATION%")) as ?sum1)
    (sum (?volume) as ?sum2)
    where {
      { select
           ((YEAR(xsd:dateTime(?o_orderdate))) as ?o_year)
           ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
           ?nation
         where {
           ?li qb:dataSet ltpch:lineitemCube ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_lineextendedprice ?l_lineextendedprice ;
               ltpch:l_linediscount ?l_linediscount ;
               ltpch:partsupplier_nation_name ?nation ;
               ltpch:partsupplier_part_type ?type ;
               ltpch:order_order_orderdate ?o_orderdate ;
               ltpch:order_region_name ?region .
           filter ((xsd:dateTime(?o_orderdate) >= xsd:dateTime("1995-01-01"^^xsd:date)) &&
             (xsd:dateTime(?o_orderdate) <= xsd:dateTime("1996-12-31"^^xsd:date) &&
              ?region = "%REGION%" &&
              ?type = "%TYPE%") 
           ) 
        } 
      } 
    }
    group by
      ?o_year 
  } 
}
order by
  ?o_year

            </pre>
        </article>
        <article>
          <h2>Query 9</h2>
            <pre>
select
  ?nation
  ?o_year
  (sum(?amount) as ?sum_profit)
where {
  { select
      ?nation
      ?o_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) - xsd:decimal(?ps_supplycost) * xsd:decimal(?l_linequantity)) as ?amount)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_linequantity ?l_linequantity ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount ;
          ltpch:partsupplier_nation_name ?nation ;
          ltpch:partsupplier_partsupplier_supplycost ?ps_supplycost ;
          ltpch:partsupplier_part_name ?p_name ;
          ltpch:order_order_orderdate ?o_orderdate .
      filter (REGEX (?p_name, "%COLOR%"))
      BIND (SUBSTR(STR(?o_orderdate), 1,4) as ?o_year)
    } 
  } 
}
group by
  ?nation
  ?o_year
order by
  ?nation
  desc (?o_year)

            </pre>
        </article>
        <article>
          <h2>Query 10</h2>
            <pre>
select
  ?c_custkey
  ?c_companyName
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
      ltpch:l_returnflag ?l_returnflag ;
      ltpch:l_lineextendedprice ?l_lineextendedprice ;
      ltpch:l_linediscount ?l_linediscount ;
      ltpch:order_order_orderdate ?o_orderdate ;
      ltpch:order_customer_address ?c_address ;
      ltpch:order_customer_phone ?c_phone ;
      ltpch:order_customer_comment ?c_comment ;
      ltpch:order_customer_acctbal ?c_acctbal ;
      ltpch:order_customer_custkey ?c_custkey ;
      ltpch:order_customer_name ?c_companyName ;
      ltpch:order_nation_name ?nation .
   filter ((xsd:dateTime(?o_orderdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date)) &&
      (xsd:dateTime(?o_orderdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date))) &&
      (?l_returnflag = "R") 
   ) 
}
group by
  ?c_custkey
  ?c_companyName
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
limit 20

            </pre>
        </article>
        <article>
          <h2>Query 11</h2>
          <pre>
select
  ?bigpspart,
  ?bigpsvalue
where {
      { 
        select
          ?bigpspart,
          sum(xsd:decimal(?b_supplycost) * xsd:decimal(?b_availqty)) as ?bigpsvalue
        where
          {
            {
              select
                ?bigpspart,
                ?b_supplycost,
                ?b_availqty
              where
                {
                  ?bigps ltpch:partsupplier_part_partkey ?bigpspart ;
                        ltpch:partsupplier_partsupplier_supplycost ?b_supplycost ;
                        ltpch:partsupplier_supplier_suppkey ?b_suppkey ;
                        ltpch:partsupplier_partsupplier_availqty ?b_availqty ;
                        ltpch:partsupplier_nation_name "%NATION%" .
                }
              group by ?bigpspart ?b_suppkey 
            }
          }
      }
    filter (?bigpsvalue > (
        select
          (sum(xsd:decimal(?t_supplycost) * xsd:decimal(?t_availqty)) * %FRACTION%) as ?threshold
        where
          {
             {
              select
                ?t_partkey,
                ?t_supplycost,
                ?t_availqty
              where
                {
                  ?thr_ps ltpch:partsupplier_partsupplier_supplycost ?t_supplycost ;
                    ltpch:partsupplier_partsupplier_availqty ?t_availqty ;
                    ltpch:partsupplier_nation_name "%NATION%" ;
                    ltpch:partsupplier_supplier_suppkey ?t_suppkey ;
                    ltpch:partsupplier_part_partkey ?t_partkey .
                }
              group by ?t_partkey ?t_suppkey 
            }
          }
    ))
  }
order by
  desc (?bigpsvalue)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 12</h2>
          <pre>
select
  ?l_shipmode
  (sum (
      bif:__or (
        bif:equ (?o_orderpriority, "1-URGENT"),
        bif:equ (?o_orderpriority, "2-HIGH") ) ) as ?high_line_count)
  (sum (1 -
      bif:__or (
        bif:equ (?o_orderpriority, "1-URGENT"),
        bif:equ (?o_orderpriority, "2-HIGH") ) ) as ?low_line_count)
where  {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_commitdate ?l_commitdate ;
       ltpch:l_receiptdate ?l_receiptdate ;
       ltpch:l_shipmode ?l_shipmode ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:order_order_orderpriority ?o_orderpriority .
    filter (xsd:boolean(?l_shipmode in ("%SHIPMODE1%", "%SHIPMODE2%")) &&
      (xsd:boolean(xsd:dateTime(?l_commitdate) < xsd:dateTime(?l_receiptdate))) &&
      (xsd:boolean(xsd:dateTime(?l_shipdate) < xsd:dateTime(?l_commitdate))) &&
      (xsd:boolean(xsd:dateTime(?l_receiptdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date))) &&
      (xsd:boolean(xsd:dateTime(?l_receiptdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date)))) )
  }
group by
  ?l_shipmode
order by
  ?l_shipmode

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 13</h2>
          <pre>
select
  ?c_count
  (count(1) as ?custdist)
where {
    { select
        ?c_custkey
        (count (distinct ?o_comment) as ?c_count)
      where
        {
          ?li2 ltpch:order_customer_custkey ?c_custkey .
           optional {
             ?li2 ltpch:order_order_comment ?o_comment .
              filter (!( REGEX (?o_comment , "%WORD1%.*%WORD2%" ) ) ) . 
          }
        }
      group by 
        ?c_custkey
      order by 
        ?c_custkey
    }
  }
group by
  ?c_count
order by
  desc (count(1))
  desc (?c_count)


          </pre>
        </article>
        </article>
        <article>
          <h2>Query 14</h2>
          <pre>
select
  ((100 * ?sum1 / ?sum2 ) as ?promo_revenue)
where
{
  select 
    (sum (
          bif:equ(SUBSTR(?p_type, 1, 5), "PROMO") *
          xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) ) as ?sum1)
    (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) ) as ?sum2)
  where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:partsupplier_part_type ?p_type .
      filter ((xsd:dateTime(?l_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date)) &&
        (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd("month", 1, "%MONTH%-01"^^xsd:date))) )
  }
}
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 15</h2>
          <pre>
select distinct
  ?s_suppkey
  ?s_name
  ?s_address
  ?s_phone
  ?total_revenue
where  
{
  ?partsupp ltpch:partsupplier_supplier_suppkey ?s_suppkey ;
            ltpch:partsupplier_supplier_name ?s_name ;
            ltpch:partsupplier_supplier_address ?s_address ;
            ltpch:partsupplier_supplier_phone ?s_phone .
  { 
    select
        ?s_suppkey
        ((sum(xsd:decimal(?l_extendedprice) * (1 - xsd:decimal(?l_discount)))) as ?total_revenue)
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:l_lineextendedprice ?l_extendedprice ;
          ltpch:l_linediscount ?l_discount ;
          ltpch:l_has_partsupplier ?ps ;
          ltpch:partsupplier_supplier_suppkey ?s_suppkey .
      filter (
          xsd:dateTime(?l_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date) &&
          xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date)) )
    }
    group by
      ?s_suppkey
  } .
  { 
    select 
      (max (?l2_total_revenue) as ?maxtotal)
    where 
    {
      { 
        select
          ((sum(xsd:decimal(?l2_extendedprice) * (1 - xsd:decimal(?l2_discount)))) as ?l2_total_revenue)
        where 
        {
          ?li2 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l2_shipdate ;
              ltpch:l_lineextendedprice ?l2_extendedprice ;
              ltpch:l_linediscount ?l2_discount ;
              ltpch:l_has_partsupplier ?ps2 ;
              ltpch:partsupplier_supplier_suppkey ?s_suppkey2 .
          filter (
              xsd:dateTime(?l2_shipdate) >= xsd:dateTime("%MONTH%-01"^^xsd:date) &&
              xsd:dateTime(?l2_shipdate) < xsd:dateTime(bif:dateadd ("month", 3, "%MONTH%-01"^^xsd:date)) )
        }
        group by
          ?s_suppkey2
      }
    }
  }
  filter (?total_revenue = ?maxtotal)
}
order by
  ?s_suppkey

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 16</h2>
          <pre>
select
  ?p_brand,
  ?p_type,
  ?p_size,
  (count(distinct ?supp)) as ?supplier_cnt
where {
    ?ps ltpch:partsupplier_part_brand ?p_brand ;
        ltpch:partsupplier_part_type ?p_type ;
        ltpch:partsupplier_part_size ?p_size ;   
        ltpch:partsupplier_supplier_suppkey ?supp .    
    filter (
      (?p_brand != "%BRAND%") &&
      !(?p_type like "%TYPE%%") &&
      (xsd:integer(?p_size) in (%SIZE1%, %SIZE2%, %SIZE3%, %SIZE4%, %SIZE5%, %SIZE6%, %SIZE7%, %SIZE8%))
    )
    filter NOT EXISTS {
       ?ps ltpch:partsupplier_supplier_comment ?badcomment .
       filter (?badcomment like "%Customer%Complaints%") 
    }
  }
group by
  ?p_brand
  ?p_type
  ?p_size
order by
  desc ((count(distinct ?supp)))
  ?p_brand
  ?p_type
  ?p_size

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 17</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice)) / 7.0) as ?avg_yearly)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:partsupplier_part_partkey ?p_partkey.
    {
      select 
        ?p_partkey
        ((0.2 * avg(xsd:decimal(?l2_linequantity))) as ?threshold)
      where { 
        ?li2  a ltpch:lineitem ;
              ltpch:l_linequantity ?l2_linequantity ; 
              ltpch:partsupplier_part_partkey ?p_partkey ;
              ltpch:partsupplier_part_container ?p_container ;
              ltpch:partsupplier_part_brand ?p_brand  .
        filter (REGEX(?p_brand,"%BRAND%","i") && ?p_container = "%CONTAINER%" ) 
      }
      group by
        ?p_partkey
    }
    filter (xsd:decimal(?l_linequantity) < xsd:decimal(?threshold)) 
}

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 18</h2>
          <pre>
select
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
   (sum(xsd:decimal(?l_linequantity)) as ?l_quantity)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:order_order_orderkey ?o_orderkey ;
        ltpch:order_order_orderdate ?o_orderdate ;
        ltpch:order_order_ordertotalprice ?o_ordertotalprice ;
        ltpch:order_customer_custkey ?c_custkey ;
        ltpch:order_customer_name ?c_name .
    { 
      select 
        ?o_orderkey 
        (sum (xsd:decimal(?l2_linequantity)) as ?sum_q)
      where 
      {
        ?li2 a ltpch:lineitem ;
             ltpch:l_linequantity ?l2_linequantity ;
             ltpch:order_order_orderkey ?o_orderkey .
      }
      group by
        ?o_orderkey
    } .
    filter (?sum_q > %QUANTITY%)
}
group by
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
order by
  desc (?o_ordertotalprice)
  ?o_orderdate
limit 100

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 19</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_shipmode ?l_shipmode ;
        ltpch:l_shipinstruct ?l_shipinstruct ;
        ltpch:partsupplier_part_brand ?p_brand ;
        ltpch:partsupplier_part_size ?p_size ;
        ltpch:partsupplier_part_container ?p_container .
     filter (?l_shipmode in ("AIR", "AIR REG") &&
      ?l_shipinstruct = "DELIVER IN PERSON" &&
      ( ( (REGEX(?p_brand,"^%BRAND1%$","i")) &&
          (?p_container in ("SM CASE", "SM BOX", "SM PACK", "SM PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY1%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY1% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 5) ) ||
        ( (REGEX(?p_brand,"^%BRAND2%$","i")) &&
          (?p_container in ("MED BAG", "MED BOX", "MED PKG", "MED PACK")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY2%) && 
          (xsd:integer(?l_linequantity) <= %QUANTITY2% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 10) ) ||
        ( (REGEX(?p_brand,"^%BRAND3%$","i")) &&
          (?p_container in ("LG CASE", "LG BOX", "LG PACK", "LG PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY3%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY3% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 15) ) ) )
  }

          </pre>
        </article>
        <article>
          <h2>Query 20</h2>
          <pre>
select distinct
  ?s_name
  ?s_address
where
{
  ?supp ltpch:partsupplier_supplier_name ?s_name ;
        ltpch:partsupplier_supplier_suppkey ?suppkey ;
        ltpch:partsupplier_supplier_address ?s_address .
  { 
    select 
      distinct ?suppkey 
    where 
    {
      ?li ltpch:partsupplier_partsupplier_availqty ?big_ps_availqty ;
              ltpch:partsupplier_supplier_suppkey ?suppkey ;
        ltpch:partsupplier_part_partkey ?partkey ;
              ltpch:partsupplier_nation_name ?n_name ;
              ltpch:partsupplier_part_name ?p_name . 
      FILTER(REGEX (?p_name , "^%COLOR%") && ?n_name = "%NATION%") .
      {
        select 
          ?suppkey ?partkey
          ((0.5 * sum(xsd:decimal(?l_linequantity))) as ?qty_threshold)
        where
        {
          ?li qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l_shipdate ;
        ltpch:partsupplier_supplier_suppkey ?suppkey ;
        ltpch:partsupplier_part_partkey ?partkey ;
              ltpch:l_linequantity ?l_linequantity .
          FILTER ((xsd:dateTime(?l_shipdate) >= xsd:dateTime("%YEAR%-01-01"^^xsd:date)) &&
            (xsd:dateTime(?l_shipdate) < xsd:dateTime(bif:dateadd ("year", 1, "%YEAR%-01-01"^^xsd:date)))
          )
        } 
        group by
          ?suppkey ?partkey
      } .
      FILTER(xsd:decimal(?big_ps_availqty) > ?qty_threshold) .
    } 
  }
}
order by ?s_name


          </pre>
        </article>
        <article>
          <h2>Query 21</h2>
          <pre>
select
    ?s_name
    ((count(1)) as ?numwait)
where {
         ?li1 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_receiptdate ?l1_receiptdate ;
              ltpch:l_commitdate ?l1_commitdate ;
              ltpch:partsupplier_supplier_name ?s_name ;
              ltpch:partsupplier_supplier_suppkey ?suppkey ;
              ltpch:partsupplier_nation_name ?n_name ;
              ltpch:order_order_orderkey ?orderkey ;
              ltpch:order_order_orderstatus ?o_orderstatus .
         filter ( 
          xsd:boolean(xsd:dateTime(?l1_receiptdate) > xsd:dateTime(?l1_commitdate)) && 
          ?n_name = "%NATION%" && 
          ?o_orderstatus = "F"
          )
         filter exists {
              ?li2 ltpch:order_order_orderkey ?orderkey ;
                   ltpch:partsupplier_supplier_suppkey ?suppkey2 .
              filter (?suppkey != ?suppkey2)
         }
         filter not exists {
              ?li3 ltpch:order_order_orderkey ?orderkey ;
                   ltpch:l_receiptdate ?l3_receiptdate ;
                   ltpch:l_commitdate ?l3_commitdate ;
                   ltpch:partsupplier_supplier_suppkey ?suppkey3 .
              filter (
                 xsd:boolean(xsd:dateTime(?l3_receiptdate) > xsd:dateTime(?l3_commitdate)) &&
                 ?suppkey3 != ?suppkey
              )
         }
       }
group by
   ?s_name
order by
    desc (count(1))
    ?s_name
limit 100

          </pre>
        </article>
        <article>
          <h2>Query 22</h2>
          <pre>
select
  (bif:LEFT (?c_phone, 2)) as ?cntrycode,
  (count (1)) as ?numcust,
  sum (xsd:decimal(?c_acctbal)) as ?totacctbal
where {
    ?cust ltpch:order_customer_acctbal ?c_acctbal ;
      ltpch:order_customer_phone ?c_phone .
    {
select (avg(?acctbal2)) as ?acctbal_threshold
where
{
    select (avg (xsd:decimal(?c_acctbal2))) as ?acctbal2
          where
            {
              ?li ltpch:order_customer_acctbal ?c_acctbal2 ;
                 ltpch:order_customer_custkey ?custkey2 ;
                 ltpch:order_customer_phone ?c_phone2 .
              filter ((xsd:decimal(?c_acctbal2) > 0.00) &&
                bif:LEFT (?c_phone2, 2) in (%COUNTRY_CODE_SET%) )
            }
          group by ?custkey2
} 
   }
    filter (
      bif:LEFT (?c_phone, 2) in (%COUNTRY_CODE_SET%) &&
      (xsd:decimal(?c_acctbal) > ?acctbal_threshold )
    ) 
    filter not exists { ?cust ltpch:order_order_orderkey ?orderkey }
  }
group by (bif:LEFT (?c_phone, 2))
order by (bif:LEFT (?c_phone, 2))


          </pre>
        </article>
      </section>
</details>
<details>
<summary>Jena queries snowflake pattern</summary>
      <section>
        <article>
        <h2>Prefix</h2>
          <pre>
prefix xsd: &lt;http://www.w3.org/2001/XMLSchema#> 
prefix ltpch: &lt;http://extbi.lab.aau.dk/ontology/ltpch/>
          </pre>
        </article>
        <article>
          <h2>Query 1</h2>
            <pre>
select
  ?l_returnflag 
  ?l_linestatus 
  (sum(xsd:decimal(?l_linequantity)) as ?sum_qty) 
  (sum(xsd:decimal(?l_lineextendedprice)) as ?sum_base_price) 
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?sum_disc_price) 
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))*(1 + xsd:decimal(?l_linetax))) as ?sum_charge) 
  (avg(xsd:decimal(?l_linequantity)) as ?avg_qty) 
  (avg(xsd:decimal(?l_lineextendedprice)) as ?avg_price) 
  (avg(xsd:decimal(?l_linediscount)) as ?avg_disc) 
  (count(1) as ?count_order)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_returnflag ?l_returnflag ;
       ltpch:l_linestatus ?l_linestatus ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linetax ?l_linetax ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:l_linediscount ?l_linediscount .
    filter (xsd:date(?l_shipdate) <= ("1998-12-01"^^xsd:date + "-P%DELTA%D"^^xsd:duration))
} 
group by
  ?l_returnflag
  ?l_linestatus
order by
  ?l_returnflag
  ?l_linestatus
            </pre>
        </article>
        <article>
          <h2>Query 2</h2>
            <pre>
select
  ?s_acctbal
  ?s_name
  ?nation_name
  ?p_partkey
  ?p_mfgr
  ?s_address
  ?s_phone
  ?s_comment
where {
  ?ps a ltpch:partsupp;
      ltpch:ps_has_supplier ?supp;
      ltpch:ps_has_part ?part ;
      ltpch:ps_supplycost ?minsc .
  ?supp a ltpch:supplier ;
     ltpch:s_acctbal ?s_acctbal ;
   ltpch:s_name ?s_name ;
     ltpch:s_has_nation ?s_has_nation ;
     ltpch:s_address ?s_address ;
     ltpch:s_phone ?s_phone ;
     ltpch:s_comment ?s_comment .
  ?s_has_nation ltpch:n_name ?nation_name ;
     ltpch:n_has_region ?s_has_region .
  ?s_has_region ltpch:r_name "%REGION%" .
  ?part a ltpch:part ;
      ltpch:p_partkey ?p_partkey ;
      ltpch:p_mfgr ?p_mfgr ;
      ltpch:p_size ?size ;
      ltpch:p_type ?p_type .
  FILTER (?size = str(%SIZE%) && fn:contains(?p_type, "%TYPE%"))
  { select ?part (min(?s_cost) as ?minsc)
    where {
        ?ps a ltpch:partsupp;
            ltpch:ps_has_part ?part;
            ltpch:ps_has_supplier ?ms;
            ltpch:ps_supplycost ?s_cost .
        ?ms ltpch:s_has_nation ?m_has_nation .
        ?m_has_nation ltpch:n_has_region ?m_has_region .
        ?m_has_region ltpch:r_name "%REGION%" .
      } 
      group by ?part 
    }
    
  }
order by
  desc (?s_acctbal)
  ?nation_name
  ?s_name
  ?p_partkey
limit 100


            </pre>
        </article>
        <article>
          <h2>Query 3</h2>
            <pre>
select
  ?o_orderkey
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?o_orderdate
  ?o_shippriority
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
    ltpch:l_lineextendedprice ?l_lineextendedprice ;
    ltpch:l_linediscount ?l_linediscount ;
    ltpch:l_has_order ?ord ;
    ltpch:l_shipdate ?l_shipdate .
  ?ord ltpch:o_orderdate ?o_orderdate ;
    ltpch:o_shippriority ?o_shippriority ;
    ltpch:o_orderkey ?o_orderkey ;
    ltpch:o_has_customer ?cust .
  ?cust ltpch:c_mktsegment ?c_mktsegment .
  filter ((xsd:date(?o_orderdate) < "%DATE%"^^xsd:date) &&
    (xsd:date(?l_shipdate) > "%DATE%"^^xsd:date) &&
    (?c_mktsegment = "%SEGMENT%") ) 
}
group by
  ?o_orderkey
  ?o_orderdate
  ?o_shippriority
order by
  desc (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
  ?o_orderdate
limit 10

            </pre>
        </article>
        <article>
          <h2>Query 4</h2>
            <pre>
select
  ?o_orderpriority
  (count(*) as ?order_count)
where  
{
  {
    select distinct
      ?o_orderpriority
      ?ord
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_commitdate ?l_commitdate ;
          ltpch:l_receiptdate ?l_receiptdate .
      ?ord ltpch:o_orderpriority ?o_orderpriority ;
           ltpch:o_orderdate ?o_orderdate .
      filter (
        (xsd:date(?l_commitdate) < xsd:date(?l_receiptdate)) &&
        (xsd:date(?o_orderdate) >= "%MONTH%-01"^^xsd:date) &&
        (xsd:date(?o_orderdate) < ("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration))
      )
    }
  }
}
group by
  ?o_orderpriority
order by
  ?o_orderpriority
            </pre>
        </article>
        <article>
          <h2>Query 5</h2>
            <pre>
select
  ?nation
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where  {
   ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_has_partsupplier ?ps ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount .
    ?ord ltpch:o_has_customer ?cust ;
         ltpch:o_orderdate ?o_orderdate .
    ?ps ltpch:ps_has_supplier ?supp .
    ?supp ltpch:s_has_nation ?s_nation .
    ?s_nation ltpch:n_has_region ?s_region ;
              ltpch:n_name ?nation .
    ?s_region ltpch:r_name ?r_name .
    ?cust ltpch:c_has_nation ?c_nation.
    filter ((?c_nation = ?s_nation) &&
      (xsd:date(?o_orderdate) >= "%YEAR%-01-01"^^xsd:date) &&
      (xsd:date(?o_orderdate) < ("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) &&
      (?r_name = "%REGION%") ) 
  }
group by
  ?nation
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))

            </pre>
        </article>
        <article>
          <h2>Query 6</h2>
            <pre>
select
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_shipdate ?l_shipdate .
    filter ( (xsd:date(?l_shipdate) >= xsd:date("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:date(?l_shipdate) < xsd:date("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration )) &&
      (xsd:decimal(?l_linediscount) >= %DISCOUNT% - 0.01) &&
      (xsd:decimal(?l_linediscount) <= %DISCOUNT% + 0.01) &&
      (xsd:decimal(?l_linequantity) < %QUANTITY%) ) 
}

            </pre>
        </article>
        <article>
          <h2>Query 7</h2>
            <pre>
select 
  ?supp_nation 
  ?cust_nation 
  ?li_year
  (sum (?volume) as ?revenue)
where {
  {
    select
      ?supp_nation
      ?cust_nation
      ?li_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_has_order ?ord ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount .
      ?ord ltpch:o_has_customer ?cust .
      ?cust ltpch:c_has_nation ?custn .
      ?custn ltpch:n_name ?cust_nation .
      ?ps ltpch:ps_has_supplier ?supp .
      ?supp ltpch:s_has_nation ?suppn .
      ?suppn ltpch:n_name ?supp_nation .
      BIND (SUBSTR(STR(?l_shipdate), 1,4) as ?li_year)
      filter (xsd:boolean(
        (?cust_nation = "%NATION1%" && ?supp_nation = "%NATION2%") ||
        (?cust_nation = "%NATION2%" && ?supp_nation = "%NATION1%") ) &&
        xsd:boolean(xsd:date(?l_shipdate) >= xsd:date("1995-01-01"^^xsd:date)) &&
        xsd:boolean(xsd:date(?l_shipdate) <= xsd:date("1996-12-31"^^xsd:date)) ) 
      } 
   } 
}
group by
  ?supp_nation
  ?cust_nation
  ?li_year
order by
  ?supp_nation
  ?cust_nation
  ?li_year

            </pre>
        </article>
        <article>
          <h2>Query 8</h2>
            <pre>
select
  ?o_year
  ((?sum1 / ?sum2) as ?mkt_share)
where {
  { select
    ?o_year
    (sum (?volume * xsd:integer(fn:starts-with(?nation, "%NATION%"))) as ?sum1)
    (sum (?volume) as ?sum2)
    where {
      { select
           ((YEAR (xsd:date(?o_orderdate))) as ?o_year)
           ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
           ?nation
         where {
           ?li qb:dataSet ltpch:lineitemCube ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_has_order ?ord ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_lineextendedprice ?l_lineextendedprice ;
               ltpch:l_linediscount ?l_linediscount .
           ?ps ltpch:ps_has_supplier ?s_supplier .
           ?s_supplier ltpch:s_has_nation ?n2 .
           ?n2 ltpch:n_name ?nation .
           ?ps ltpch:ps_has_part ?part .
           ?part ltpch:p_type ?type .
           ?ord ltpch:o_orderdate ?o_orderdate ;
             ltpch:o_has_customer ?c_customer .
           ?c_customer ltpch:c_has_nation ?n_nation .
           ?n_nation ltpch:n_has_region ?r_region .
           ?r_region ltpch:r_name ?region.
           filter ((xsd:date(?o_orderdate) >= xsd:date("1995-01-01"^^xsd:date)) &&
             (xsd:date(?o_orderdate) <= xsd:date("1996-12-31"^^xsd:date)) &&
              (?region = "%REGION%") &&
              (?type = "%TYPE%") 
           ) 
        } 
      } 
    }
    group by
      ?o_year 
  } 
}
order by
  ?o_year

            </pre>
        </article>
        <article>
          <h2>Query 9</h2>
            <pre>
select
  ?nation
  ?o_year
  (sum(?amount) as ?sum_profit)
where {
  { select
      ?nation
      ?o_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) - xsd:decimal(?ps_supplycost) * xsd:decimal(?l_linequantity)) as ?amount)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_has_partsupplier ?ps ;
          ltpch:l_linequantity ?l_linequantity ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount .
      ?ps ltpch:ps_has_part ?part ;
          ltpch:ps_has_supplier ?supp .
      ?supp ltpch:s_has_nation ?s_nation .
      ?s_nation ltpch:n_name ?nation .
      ?ord ltpch:o_orderdate ?o_orderdate .
      ?ps ltpch:ps_supplycost ?ps_supplycost .
      ?part ltpch:p_name ?p_name .
      filter (REGEX (?p_name, "%COLOR%"))
      BIND (SUBSTR(STR(?o_orderdate), 1,4) as ?o_year)
    } 
  } 
}
group by
  ?nation
  ?o_year
order by
  ?nation
  desc (?o_year)

            </pre>
        </article>
        <article>
          <h2>Query 10</h2>
            <pre>
select
  ?c_custkey
  ?c_companyName
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
      ltpch:l_returnflag ?l_returnflag ;
      ltpch:l_has_order ?ord ;
      ltpch:l_lineextendedprice ?l_lineextendedprice ;
      ltpch:l_linediscount ?l_linediscount .
  ?ord ltpch:o_has_customer ?cust ;
       ltpch:o_orderdate ?o_orderdate .
  ?cust ltpch:c_address ?c_address ;
      ltpch:c_phone ?c_phone ;
      ltpch:c_comment ?c_comment ;
      ltpch:c_acctbal ?c_acctbal ;
      ltpch:c_custkey ?c_custkey ;
      ltpch:c_has_nation ?c_nation ;
      ltpch:c_name ?c_companyName .
   ?c_nation ltpch:n_name ?nation .
   filter ((xsd:date(?o_orderdate) >= xsd:date("%MONTH%-01"^^xsd:date)) &&
      (xsd:date(?o_orderdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration)) &&
      (?l_returnflag = "R") 
   ) 
}
group by
  ?c_custkey
  ?c_companyName
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
limit 20

            </pre>
        </article>
        <article>
          <h2>Query 11</h2>
          <pre>
select
  ?bigpspart
  ?bigpsvalue
where {
      { select
          ?bigpspart
          (sum(xsd:decimal(?b_supplycost) * xsd:decimal(?b_availqty)) as ?bigpsvalue)
        where
          {
            ?bigps a ltpch:partsupp ;
                   ltpch:ps_has_part ?bigpspart ;
                   ltpch:ps_supplycost ?b_supplycost ;
                   ltpch:ps_availqty ?b_availqty ;
                   ltpch:ps_has_supplier ?b_supplier .
            ?b_supplier ltpch:s_has_nation ?b_nation .
            ?b_nation ltpch:n_name "%NATION%" .
          }
          group by ?bigpspart
      }
      {
       select
          (sum(xsd:decimal(?t_supplycost) * xsd:decimal(?t_availqty)) * %FRACTION% as ?threshold)
        where
          {
            ?thr_ps a ltpch:partsupp ;
                    ltpch:ps_supplycost ?t_supplycost ;
                    ltpch:ps_availqty ?t_availqty ;
                    ltpch:ps_has_supplier ?t_supplier .
            ?t_supplier ltpch:s_has_nation ?t_nation .
            ?t_nation ltpch:n_name "%NATION%" .
          }
      }
      filter (?bigpsvalue > ?threshold )
  }
order by
  desc (?bigpsvalue)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 12</h2>
          <pre>
select
  ?l_shipmode
  (sum (
      xsd:integer(fn:starts-with(?o_orderpriority, "1-URGENT") ||
      fn:starts-with(?o_orderpriority, "2-HIGH") ) ) as ?high_line_count)
  (sum (1 -
      xsd:integer(fn:starts-with(?o_orderpriority, "1-URGENT") ||
      fn:starts-with(?o_orderpriority, "2-HIGH") ) ) as ?low_line_count)
where  {
    
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_commitdate ?l_commitdate ;
       ltpch:l_receiptdate ?l_receiptdate ;
       ltpch:l_shipmode ?l_shipmode ;
       ltpch:l_shipdate ?l_shipdate .
    ?ord ltpch:o_orderpriority ?o_orderpriority .
    filter (?l_shipmode in ("%SHIPMODE1%", "%SHIPMODE2%") &&
      (xsd:date(?l_commitdate) < xsd:date(?l_receiptdate)) &&
      (xsd:date(?l_shipdate) < xsd:date(?l_commitdate)) &&
      (xsd:date(?l_receiptdate) >= "%YEAR%-01-01"^^xsd:date) &&
      (xsd:date(?l_receiptdate) < ("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) )
  }
group by
  ?l_shipmode
order by
  ?l_shipmode

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 13</h2>
          <pre>
select
  ?c_count
  (count(1) as ?custdist)
where {
    { select
        ?c_custkey
        (count (?ord) as ?c_count)
      where
        {
          ?cust ltpch:c_custkey ?c_custkey .
           optional {
             ?ord a ltpch:orders ;
                  ltpch:o_has_customer ?cust ;
                  ltpch:o_comment ?o_comment .
              filter (!( REGEX (?o_comment , "%WORD1%.*%WORD2%" ) ) ) . 
          }
        }
      group by 
        ?c_custkey
    }
  }
group by
  ?c_count
order by
  desc (count(1))
  desc (?c_count)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 14</h2>
          <pre>
select
       ((100 * sum(xsd:integer(fn:starts-with(?p_type, "PROMO")) * xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))  / sum(xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))) as ?promo_revenue)
where
{

    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:ps_has_part ?part .
    ?part ltpch:p_type ?p_type .
    filter (xsd:date(?l_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
      (xsd:date(?l_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P1M"^^xsd:duration)) )

}

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 15</h2>
          <pre>
select
  ?s_suppkey
  ?s_name
  ?s_address
  ?s_phone
  ?total_revenue
where  {
    ?supplier a ltpch:supplier ;
        ltpch:s_suppkey ?s_suppkey ;
        ltpch:s_name ?s_name ;
        ltpch:s_address ?s_address ;
        ltpch:s_phone ?s_phone .
    { select
          ?supplier
          (sum(xsd:decimal(?l_extendedprice) * (1 - xsd:decimal(?l_discount))) as ?total_revenue)
       where {
            ?li1 qb:dataSet ltpch:lineitemCube ;
                 ltpch:l_shipdate ?l_shipdate ;
                 ltpch:l_lineextendedprice ?l_extendedprice ;
                 ltpch:l_linediscount ?l_discount ;
                 ltpch:l_has_partsupplier ?ps1 .
            ?ps1 ltpch:ps_has_supplier ?supplier .
            filter (
                xsd:date(?l_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
                xsd:date(?l_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration) )
        }
      group by
        ?supplier
      }
      { select (max (?l2_total_revenue) as ?maxtotal)
        where {
            { select
                  ?supplier2
                  (sum(xsd:decimal(?l2_extendedprice) * (1 - xsd:decimal(?l2_discount))) as ?l2_total_revenue)
               where {
                    ?li2 qb:dataSet ltpch:lineitemCube ;
                      ltpch:l_shipdate ?l2_shipdate ;
                      ltpch:l_lineextendedprice ?l2_extendedprice ;
                      ltpch:l_linediscount ?l2_discount ;
                       ltpch:l_has_partsupplier ?ps2 .
                  ?ps2 ltpch:ps_has_supplier ?supplier2 .
                    filter (
                        xsd:date(?l2_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
                        xsd:date(?l2_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration) )
               }
               group by 
                ?supplier2
            }
        }
    }
    filter (?total_revenue = ?maxtotal)
}
order by
  ?supplier

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 16</h2>
          <pre>
select
  ?p_brand
  ?p_type
  ?p_size
  (count(distinct ?supp) as ?supplier_cnt)
where {
    ?ps a ltpch:partsupp ;
        ltpch:ps_has_part ?part ;
        ltpch:ps_has_supplier ?supp .
    ?part ltpch:p_brand ?p_brand ;
        ltpch:p_type ?p_type ;
        ltpch:p_size ?p_size .    
    filter (
      (?p_brand != "%BRAND%") &&
      !(fn:starts-with(?p_type,"%TYPE%")) &&
      (xsd:integer(?p_size) in (%SIZE1%, %SIZE2%, %SIZE3%, %SIZE4%, %SIZE5%, %SIZE6%, %SIZE7%, %SIZE8%))
    )
    filter NOT EXISTS {
       ?supp a ltpch:supplier;
             ltpch:s_comment ?badcomment .
       filter ( fn:matches (?badcomment ,"Customer.*Complaints") )
    }
  }
group by
  ?p_brand
  ?p_type
  ?p_size
order by
  desc ((count(distinct ?supp)))
  ?p_brand
  ?p_type
  ?p_size

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 17</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice)) / 7.0) as ?avg_yearly)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:ps_has_part ?part .
    ?part ltpch:p_brand ?p_brand ;
          ltpch:p_container ?p_container .
          {
            select 
              ?part
              ((0.2 * avg(xsd:decimal(?l2_linequantity))) as ?threshold)
            where { 
              ?li2  a ltpch:lineitem ;
                    ltpch:l_linequantity ?l2_linequantity ; 
                    ltpch:l_has_partsupplier ?ps2 .
              ?ps2  ltpch:ps_has_part ?part .
          } 
          group by
            ?part
        }
    filter (xsd:decimal(?l_linequantity) < ?threshold && REGEX(?p_brand,"%BRAND%","i") && ?p_container = "%CONTAINER%") 
}
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 18</h2>
          <pre>
select
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
   (sum(xsd:decimal(?l_linequantity)) as ?l_quantity)
where {
    ?li qb:dataSet ltpch:lineitemCube  ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_order ?ord .
    ?ord ltpch:o_orderkey ?o_orderkey ;
         ltpch:o_orderdate ?o_orderdate ;
         ltpch:o_ordertotalprice ?o_ordertotalprice ;
         ltpch:o_has_customer ?cust .
    ?cust ltpch:c_custkey ?c_custkey ;
          ltpch:c_name ?c_name .  
    { select 
         ?ord 
         (sum (xsd:decimal(?l2_linequantity)) as ?sum_q)
       where {
           ?li2 qb:dataSet ltpch:lineitemCube ;
                ltpch:l_linequantity ?l2_linequantity ;
                ltpch:l_has_order ?ord .
       }
       group by ?ord 
    } .
    filter (xsd:decimal(?sum_q) > xsd:decimal(%QUANTITY%))
}
group by
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
order by
  desc (?o_ordertotalprice)
  ?o_orderdate
limit 100

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 19</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_shipmode ?l_shipmode ;
        ltpch:l_shipinstruct ?l_shipinstruct ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:ps_has_part ?part .
     ?part ltpch:p_brand ?p_brand ;
          ltpch:p_size ?p_size ;
          ltpch:p_container ?p_container .
     
     filter (?l_shipmode in ("AIR", "AIR REG") &&
      ?l_shipinstruct = "DELIVER IN PERSON" &&
      ( ( (REGEX(?p_brand,"^%BRAND1%$","i")) &&
          (?p_container in ("SM CASE", "SM BOX", "SM PACK", "SM PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY1%) &&
          (xsd:integer(?l_linequantity) <= (%QUANTITY1% + 10)) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 5) ) ||
        ( (REGEX(?p_brand,"^%BRAND2%$","i")) &&
          (?p_container in ("MED BAG", "MED BOX", "MED PKG", "MED PACK")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY2%) && 
          (xsd:integer(?l_linequantity) <= (%QUANTITY2% + 10)) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 10) ) ||
        ( (REGEX(?p_brand,"^%BRAND3%$","i")) &&
          (?p_container in ("LG CASE", "LG BOX", "LG PACK", "LG PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY3%) &&
          (xsd:integer(?l_linequantity) <= (%QUANTITY3% + 10)) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 15) ) ) )
  }

          </pre>
        </article>
        <article>
          <h2>Query 20</h2>
          <pre>
select
  ?s_name
  ?s_address
where
{
  ?supp ltpch:s_name ?s_name ;
        ltpch:s_address ?s_address .
  { 
    select distinct 
      ?supp 
    where 
    {
      ?big_ps ltpch:ps_has_part ?part ;
              ltpch:ps_availqty ?big_ps_availqty ;
              ltpch:ps_has_supplier ?supp .
      ?supp ltpch:s_has_nation ?s_nation .
      ?s_nation ltpch:n_name ?n_name .
      ?part ltpch:p_name ?p_name . 
      filter (REGEX (?p_name , "^%COLOR%") && 
              ?n_name = "%NATION%" && 
              xsd:decimal(?big_ps_availqty) > ?qty_threshold)
      {
        select 
          ((0.5 * sum(xsd:decimal(?l_linequantity))) as ?qty_threshold)
          ?big_ps
        where
        {
          ?li qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l_shipdate ;
              ltpch:l_linequantity ?l_linequantity ;
              ltpch:l_has_partsupplier ?big_ps .
          filter ((xsd:date(?l_shipdate) >= xsd:date("%YEAR%-01-01"^^xsd:date)) &&
            (xsd:date(?l_shipdate) < xsd:date("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration))
          )
        }
        group by 
          ?big_ps
      }
    } 
  }
}
order by ?s_name

          </pre>
        </article>
        <article>
          <h2>Query 21</h2>
          <pre>
select
    ?s_name
    (count(1) as ?numwait)
where {
          ?li1 qb:dataSet ltpch:lineitemCube;
              ltpch:l_receiptdate ?l1_receiptdate ;
              ltpch:l_commitdate ?l1_commitdate ;
              ltpch:l_has_partsupplier ?ps ;
              ltpch:l_has_order ?ord .
          ?ps ltpch:ps_has_supplier ?supp .
          ?supp ltpch:s_name ?s_name ;
               ltpch:s_has_nation ?s_nation .
          ?ord ltpch:o_orderstatus ?orderstatus .
          ?s_nation ltpch:n_name ?name
          filter ( 
            xsd:date(?l1_receiptdate) > xsd:date(?l1_commitdate) && 
            ?name = "%NATION%" && 
            ?orderstatus = "F"
            ) 
          filter exists {
            ?li2 ltpch:l_has_order ?ord ;
                 ltpch:l_has_partsupplier ?ps2 .
            ?ps2 ltpch:ps_has_supplier ?supp2 .
            filter (?supp != ?supp2)
          }
          filter not exists {
              ?li3 ltpch:l_has_order ?ord ;
                   ltpch:l_receiptdate ?l3_receiptdate ;
                   ltpch:l_commitdate ?l3_commitdate ;
                   ltpch:l_has_partsupplier ?ps3 .
              ?ps3 ltpch:ps_has_supplier ?supp3 .
              filter (
                 xsd:date(?l3_receiptdate) > xsd:date(?l3_commitdate) &&
                 ?supp3 != ?supp
              )
         }
       }
group by
   ?s_name
order by
    desc (count(1))
    ?s_name
limit 100

          </pre>
        </article>
        <article>
          <h2>Query 22</h2>
          <pre>
select
  ?cntrycode
  (count (1) as ?numcust)
  (sum (xsd:decimal(?c_acctbal)) as ?totacctbal)
where {
    ?cust a ltpch:customer ;
      ltpch:c_acctbal ?c_acctbal ;
      ltpch:c_phone ?c_phone .
      BIND (fn:substring(?c_phone,0, 3) as ?cntrycode)
    {
      select (avg (xsd:decimal(?c_acctbal2)) as ?acctbal_threshold)
          where
            {
              ?cust2 a ltpch:customer ;
                 ltpch:c_acctbal ?c_acctbal2 ;
                 ltpch:c_phone ?c_phone2 .
              filter ((xsd:decimal(?c_acctbal2) > 0.00) &&
                fn:substring(?c_phone2,0, 3) in (%COUNTRY_CODE_SET%) )
            }
    }
    filter (
      ?cntrycode in (%COUNTRY_CODE_SET%) &&
      (xsd:decimal(?c_acctbal) > ?acctbal_threshold ) )
    filter not exists { ?ord ltpch:o_has_customer ?cust }
  }
group by ?cntrycode
order by ?cntrycode

          </pre>
        </article>
      </section>
</details>
<details>
<summary>Jena queries star pattern</summary>
      <section>
        <article>
        <h2>Prefix</h2>
          <pre>
prefix xsd: &lt;http://www.w3.org/2001/XMLSchema#> 
prefix ltpch: &lt;http://extbi.lab.aau.dk/ontology/ltpch/>
          </pre>
        </article>
        <article>
          <h2>Query 1</h2>
            <pre>
              select
  ?l_returnflag 
  ?l_linestatus 
  (sum(xsd:decimal(?l_linequantity)) as ?sum_qty) 
  (sum(xsd:decimal(?l_lineextendedprice)) as ?sum_base_price) 
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?sum_disc_price) 
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))*(1 + xsd:decimal(?l_linetax))) as ?sum_charge) 
  (avg(xsd:decimal(?l_linequantity)) as ?avg_qty) 
  (avg(xsd:decimal(?l_lineextendedprice)) as ?avg_price) 
  (avg(xsd:decimal(?l_linediscount)) as ?avg_disc) 
  (count(1) as ?count_order)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_returnflag ?l_returnflag ;
       ltpch:l_linestatus ?l_linestatus ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linetax ?l_linetax ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:l_linediscount ?l_linediscount .
    filter (xsd:date(?l_shipdate) <= ("1998-12-01"^^xsd:date + "-P%DELTA%D"^^xsd:duration))
} 
group by
  ?l_returnflag
  ?l_linestatus
order by
  ?l_returnflag
  ?l_linestatus

            </pre>
        </article>
        <article>
          <h2>Query 2</h2>
            <pre>
select
  ?s_acctbal
  ?s_name
  ?nation_name
  ?p_partkey
  ?p_mfgr
  ?s_address
  ?s_phone
  ?s_comment
where {
  ?ps ltpch:supplier_acctbal ?s_acctbal ;
    ltpch:supplier_name ?s_name ;
    ltpch:supplier_address ?s_address ;
    ltpch:supplier_phone ?s_phone ;
    ltpch:supplier_comment ?s_comment ;
    ltpch:nation_name ?nation_name ;
    ltpch:region_name "%REGION%" ;
    ltpch:part_partkey ?p_partkey ;
    ltpch:part_mfgr ?p_mfgr ;
    ltpch:part_size ?size ;
    ltpch:part_type ?p_type .
  FILTER (?size = str(%SIZE%) && fn:contains(?p_type, "%TYPE%"))
  { select ?p_partkey  (min(?s_cost) as ?minsc)
    where {
        ?ps ltpch:part_partkey ?p_partkey;
            ltpch:partsupplier_supplycost ?s_cost ;
            ltpch:region_name ?region2 .
            filter (?region2 = "%REGION%")
      } 
      group by ?p_partkey 
    }
     
  }
order by
  desc (?s_acctbal)
  ?nation_name
  ?s_name
  ?p_partkey
limit 100

            </pre>
        </article>
        <article>
          <h2>Query 3</h2>
            <pre>
select
  ?o_orderkey
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?o_orderdate
  ?o_shippriority
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
    ltpch:l_lineextendedprice ?l_lineextendedprice ;
    ltpch:l_linediscount ?l_linediscount ;
    ltpch:l_has_order ?ord ;
    ltpch:l_shipdate ?l_shipdate .
  ?ord ltpch:order_orderdate ?o_orderdate ;
    ltpch:order_shippriority ?o_shippriority ;
    ltpch:order_orderkey ?o_orderkey ;
    ltpch:customer_mktsegment ?c_mktsegment .
  filter ((xsd:date(?o_orderdate) < "%DATE%"^^xsd:date) &&
    (xsd:date(?l_shipdate) > "%DATE%"^^xsd:date) &&
    (?c_mktsegment = "%SEGMENT%") ) 
}
group by
  ?o_orderkey
  ?o_orderdate
  ?o_shippriority
order by
  desc (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
  ?o_orderdate
limit 10

            </pre>
        </article>
        <article>
          <h2>Query 4</h2>
            <pre>
select
  ?o_orderpriority
  (count(*) as ?order_count)
where  
{
  {
    select distinct
      ?o_orderpriority
      ?ordkey
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_commitdate ?l_commitdate ;
          ltpch:l_receiptdate ?l_receiptdate .
      ?ord ltpch:order_orderpriority ?o_orderpriority ;
           ltpch:order_orderkey ?ordkey ;
           ltpch:order_orderdate ?o_orderdate .
      filter (
        (xsd:date(?l_commitdate) < xsd:date(?l_receiptdate)) &&
        (xsd:date(?o_orderdate) >= "%MONTH%-01"^^xsd:date) &&
        (xsd:date(?o_orderdate) < ("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration))
      )
    }
  }
}
group by
  ?o_orderpriority
order by
  ?o_orderpriority

            </pre>
        </article>
        <article>
          <h2>Query 5</h2>
            <pre>
select
  ?nation
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where  {
   ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_has_partsupplier ?ps ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount .
    ?ord ltpch:order_orderdate ?o_orderdate ;
         ltpch:nation_name ?c_nation .
    ?ps ltpch:nation_name ?nation ;
        ltpch:region_name ?r_name .
    
    filter ((?c_nation = ?nation) &&
      (xsd:date(?o_orderdate) >= "%YEAR%-01-01"^^xsd:date) &&
      (xsd:date(?o_orderdate) < ("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) &&
      (?r_name = "%REGION%") ) 
  }
group by
  ?nation
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))

            </pre>
        </article>
        <article>
          <h2>Query 6</h2>
            <pre>
select
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_shipdate ?l_shipdate .
    filter ( (xsd:date(?l_shipdate) >= ("%YEAR%-01-01"^^xsd:date)) &&
      (xsd:date(?l_shipdate) < xsd:date("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) &&
      (xsd:decimal(?l_linediscount) >= %DISCOUNT% - 0.01) &&
      (xsd:decimal(?l_linediscount) <= %DISCOUNT% + 0.01) &&
      (xsd:decimal(?l_linequantity) < %QUANTITY%) ) 
}

            </pre>
        </article>
        <article>
          <h2>Query 7</h2>
            <pre>
select 
  ?supp_nation 
  ?cust_nation 
  ?li_year
  (sum (xsd:decimal(?volume)) as ?revenue)
where {
  {
    select
      ?supp_nation
      ?cust_nation
      ?li_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_has_order ?ord ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount .
      ?ord ltpch:nation_name ?cust_nation .
      ?ps ltpch:nation_name ?supp_nation .
      BIND (SUBSTR(STR(?l_shipdate), 1,4) as ?li_year)
filter (xsd:boolean(
        (?cust_nation = "%NATION1%" && ?supp_nation = "%NATION2%") ||
        (?cust_nation = "%NATION2%" && ?supp_nation = "%NATION1%") ) &&
        xsd:boolean(xsd:date(?l_shipdate) >= xsd:date("1995-01-01"^^xsd:date)) &&
        xsd:boolean(xsd:date(?l_shipdate) <= xsd:date("1996-12-31"^^xsd:date)) )  
      } 
   } 
}
group by
  ?supp_nation
  ?cust_nation
  ?li_year
order by
  ?supp_nation
  ?cust_nation
  ?li_year

            </pre>
        </article>
        <article>
          <h2>Query 8</h2>
            <pre>
select
  ?o_year
  ((?sum1 / ?sum2) as ?mkt_share)
where {
  { select
    ?o_year
    (sum (?volume * xsd:integer(fn:starts-with(?nation, "%NATION%"))) as ?sum1)
    (sum (?volume) as ?sum2)
    where {
      { select
           ((YEAR(xsd:date(?o_orderdate))) as ?o_year)
           ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
           ?nation
         where {
           ?li qb:dataSet ltpch:lineitemCube ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_has_order ?ord ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_lineextendedprice ?l_lineextendedprice ;
               ltpch:l_linediscount ?l_linediscount .
           ?ps ltpch:nation_name ?nation ;
               ltpch:part_type ?type .
           ?ord ltpch:order_orderdate ?o_orderdate ;
                ltpch:region_name ?region .
           filter ((xsd:date(?o_orderdate) >= "1995-01-01"^^xsd:date) &&
             (xsd:date(?o_orderdate) <= "1996-12-31"^^xsd:date) &&
              (?region = "%REGION%") &&
              (?type = "%TYPE%") 
           ) 
        } 
      } 
    }
    group by
      ?o_year 
  } 
}
order by
  ?o_year

            </pre>
        </article>
        <article>
          <h2>Query 9</h2>
            <pre>
select
  ?nation
  ?o_year
  (sum(?amount) as ?sum_profit)
where {
  { select
      ?nation
      ?o_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) - xsd:decimal(?ps_supplycost) * xsd:decimal(?l_linequantity)) as ?amount)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_has_order ?ord ;
          ltpch:l_has_partsupplier ?ps ;
          ltpch:l_linequantity ?l_linequantity ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount .
      ?ps ltpch:nation_name ?nation ;
          ltpch:partsupplier_supplycost ?ps_supplycost ;
          ltpch:part_name ?p_name .
      ?ord ltpch:order_orderdate ?o_orderdate .
      filter (REGEX (?p_name, "%COLOR%"))
      BIND (SUBSTR(STR(?o_orderdate), 1,4) as ?o_year)
    } 
  } 
}
group by
  ?nation
  ?o_year
order by
  ?nation
  desc (?o_year)
            </pre>
        </article>
        <article>
          <h2>Query 10</h2>
            <pre>
select
  ?c_custkey
  ?c_companyName
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
      ltpch:l_returnflag ?l_returnflag ;
      ltpch:l_has_order ?ord ;
      ltpch:l_lineextendedprice ?l_lineextendedprice ;
      ltpch:l_linediscount ?l_linediscount .
  ?ord ltpch:order_orderdate ?o_orderdate ;
      ltpch:customer_address ?c_address ;
      ltpch:customer_phone ?c_phone ;
      ltpch:customer_comment ?c_comment ;
      ltpch:customer_acctbal ?c_acctbal ;
      ltpch:customer_custkey ?c_custkey ;
      ltpch:customer_name ?c_companyName ;
      ltpch:nation_name ?nation .
   filter ((xsd:date(?o_orderdate) >= xsd:date("%MONTH%-01"^^xsd:date)) &&
      (xsd:date(?o_orderdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration)) &&
      (?l_returnflag = "R") 
   ) }
group by
  ?c_custkey
  ?c_companyName
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
limit 20

            </pre>
        </article>
        <article>
          <h2>Query 11</h2>
          <pre>
select
  ?bigpspart
  ?bigpsvalue
where {
      { select
          ?bigpspart
          (sum(xsd:decimal(?b_supplycost) * xsd:decimal(?b_availqty)) as ?bigpsvalue)
        where
          {
            ?bigps ltpch:part_partkey ?bigpspart ;
                  ltpch:partsupplier_supplycost ?b_supplycost ;
                  ltpch:partsupplier_availqty ?b_availqty ;
                  ltpch:nation_name "%NATION%" .
          }
          group by ?bigpspart
      }
      {
        select
          (sum(xsd:decimal(?t_supplycost) * xsd:decimal(?t_availqty)) * %FRACTION% as ?threshold)
        where
          {
            ?thr_ps ltpch:partsupplier_supplycost ?t_supplycost ;
                    ltpch:partsupplier_availqty ?t_availqty ;
                    ltpch:nation_name "%NATION%" .
          }
      }
      filter (?bigpsvalue > ?threshold )
  }
order by
  desc (?bigpsvalue)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 12</h2>
          <pre>
select
  ?l_shipmode
  (sum (
      xsd:integer(fn:starts-with(?o_orderpriority, "1-URGENT") ||
      fn:starts-with(?o_orderpriority, "2-HIGH") ) ) as ?high_line_count)
  (sum (1 -
      xsd:integer(fn:starts-with(?o_orderpriority, "1-URGENT") ||
      fn:starts-with(?o_orderpriority, "2-HIGH") ) ) as ?low_line_count)
where  {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_has_order ?ord ;
       ltpch:l_commitdate ?l_commitdate ;
       ltpch:l_receiptdate ?l_receiptdate ;
       ltpch:l_shipmode ?l_shipmode ;
       ltpch:l_shipdate ?l_shipdate .
    ?ord ltpch:order_orderpriority ?o_orderpriority .
    filter (?l_shipmode in ("%SHIPMODE1%", "%SHIPMODE2%") &&
      (xsd:date(?l_commitdate) < xsd:date(?l_receiptdate)) &&
      (xsd:date(?l_shipdate) < xsd:date(?l_commitdate)) &&
      (xsd:date(?l_receiptdate) >= "%YEAR%-01-01"^^xsd:date) &&
      (xsd:date(?l_receiptdate) < ("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) )
  }
group by
  ?l_shipmode
order by
  ?l_shipmode

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 13</h2>
          <pre>
select
  ?c_count
  (count(1) as ?custdist)
where {
    { select
        ?c_custkey
        (count (?o_comment) as ?c_count)
      where
        {
          ?ord ltpch:customer_custkey ?c_custkey .
           optional {
             ?ord ltpch:order_comment ?o_comment .
              filter (!( REGEX (?o_comment , "%WORD1%.*%WORD2%" ) ) ) . 
          }
        }
      group by 
        ?c_custkey
    }
  }
group by
  ?c_count
order by
  desc (count(1))
  desc (?c_count)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 14</h2>
          <pre>
  select 
    ((100 * sum(xsd:integer(fn:starts-with(?p_type, "PROMO")) * xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))  / sum(xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))) as ?promo_revenue)
  where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:l_has_partsupplier ?part .
      ?part ltpch:part_type ?p_type .
      filter (xsd:date(?l_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
      (xsd:date(?l_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P1M"^^xsd:duration)) )
  }

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 15</h2>
          <pre>
select distinct
  ?s_suppkey
  ?s_name
  ?s_address
  ?s_phone
  ?total_revenue
where  
{
  ?partsupp ltpch:supplier_suppkey ?s_suppkey ;
            ltpch:supplier_name ?s_name ;
            ltpch:supplier_address ?s_address ;
            ltpch:supplier_phone ?s_phone .
  { 
    select
        ?s_suppkey
        ((sum(xsd:decimal(?l_extendedprice) * (1 - xsd:decimal(?l_discount)))) as ?total_revenue)
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:l_lineextendedprice ?l_extendedprice ;
          ltpch:l_linediscount ?l_discount ;
          ltpch:l_has_partsupplier ?ps .
      ?ps ltpch:supplier_suppkey ?s_suppkey .
      filter (
                xsd:date(?l_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
                xsd:date(?l_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration) )
    }
    group by
      ?s_suppkey
  } .
  { 
    select 
      (max (?l2_total_revenue) as ?maxtotal)
    where 
    {
      { 
        select
          ((sum(xsd:decimal(?l2_extendedprice) * (1 - xsd:decimal(?l2_discount)))) as ?l2_total_revenue)
        where 
        {
          ?li2 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l2_shipdate ;
              ltpch:l_lineextendedprice ?l2_extendedprice ;
              ltpch:l_linediscount ?l2_discount ;
              ltpch:l_has_partsupplier ?ps2 .
          ?ps2 ltpch:supplier_suppkey ?s_suppkey2 .
          filter (
                        xsd:date(?l2_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
                        xsd:date(?l2_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration) )
        }
        group by
          ?s_suppkey2
      }
    }
  }
  filter (?total_revenue = ?maxtotal)
}
order by
  ?s_suppkey

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 16</h2>
          <pre>
select
  ?p_brand
  ?p_type
  ?p_size
  (count(distinct ?supp) as ?supplier_cnt)
where {
    ?ps ltpch:part_brand ?p_brand ;
        ltpch:part_type ?p_type ;
        ltpch:part_size ?p_size ;   
        ltpch:supplier_suppkey ?supp .    
    filter (
      (?p_brand != "%BRAND%") &&
      !(fn:starts-with(?p_type,"%TYPE%")) &&
      (xsd:integer(?p_size) in (%SIZE1%, %SIZE2%, %SIZE3%, %SIZE4%, %SIZE5%, %SIZE6%, %SIZE7%, %SIZE8%))
    )
    filter NOT EXISTS {
       ?supp a ltpch:supplier;
             ltpch:s_comment ?badcomment .
       filter ( fn:matches (?badcomment ,"Customer.*Complaints") )
    }
  }
group by
  ?p_brand
  ?p_type
  ?p_size
order by
  desc ((count(distinct ?supp)))
  ?p_brand
  ?p_type
  ?p_size

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 17</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice)) / 7.0) as ?avg_yearly)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_partsupplier ?ps .
    ?ps ltpch:part_partkey ?p_partkey.
    {
      select 
        ?p_partkey
        ((0.2 * avg(xsd:decimal(?l2_linequantity))) as ?threshold)
      where { 
        ?li2  a ltpch:lineitem ;
              ltpch:l_linequantity ?l2_linequantity ; 
              ltpch:l_has_partsupplier ?ps2 .
        ?ps2 ltpch:part_partkey ?p_partkey ;
              ltpch:part_container ?p_container ;
              ltpch:part_brand ?p_brand  .
      }
      group by
        ?p_partkey
    }
    filter (xsd:decimal(?l_linequantity) < ?threshold && REGEX(?p_brand,"%BRAND%","i") && ?p_container = "%CONTAINER%") 
}
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 18</h2>
          <pre>
select
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
   (sum(xsd:decimal(?l_linequantity)) as ?l_quantity)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_order ?ord .
    ?ord ltpch:order_orderkey ?o_orderkey ;
        ltpch:order_orderdate ?o_orderdate ;
        ltpch:order_ordertotalprice ?o_ordertotalprice ;
        ltpch:customer_custkey ?c_custkey ;
        ltpch:customer_name ?c_name .
    { 
      select 
        ?ord 
        (sum (xsd:decimal(?l2_linequantity)) as ?sum_q)
      where 
      {
        ?li2 a ltpch:lineitem ;
             ltpch:l_linequantity ?l2_linequantity ;
             ltpch:l_has_order ?ord .
      }
      group by
        ?ord
    } .
    filter (xsd:decimal(?sum_q) > xsd:decimal(%QUANTITY%))
}
group by
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
order by
  desc (?o_ordertotalprice)
  ?o_orderdate
limit 100

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 19</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_shipmode ?l_shipmode ;
        ltpch:l_shipinstruct ?l_shipinstruct .
     ?ps ltpch:part_brand ?p_brand ;
          ltpch:part_size ?p_size ;
          ltpch:part_container ?p_container .
     

     filter (?l_shipmode in ("AIR", "AIR REG") &&
      ?l_shipinstruct = "DELIVER IN PERSON" &&
      ( ( (REGEX(?p_brand,"^%BRAND1%$","i")) &&
          (?p_container in ("SM CASE", "SM BOX", "SM PACK", "SM PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY1%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY1% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 5) ) ||
        ( (REGEX(?p_brand,"^%BRAND2%$","i")) &&
          (?p_container in ("MED BAG", "MED BOX", "MED PKG", "MED PACK")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY2%) && 
          (xsd:integer(?l_linequantity) <= %QUANTITY2% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 10) ) ||
        ( (REGEX(?p_brand,"^%BRAND3%$","i")) &&
          (?p_container in ("LG CASE", "LG BOX", "LG PACK", "LG PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY3%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY3% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 15) ) ) )
  }

          </pre>
        </article>
        <article>
          <h2>Query 20</h2>
          <pre>
select distinct
  ?s_name
  ?s_address
where
{
  ?supp ltpch:supplier_name ?s_name ;
        ltpch:supplier_suppkey ?suppkey ;
        ltpch:supplier_address ?s_address .
  { 
    select 
      distinct ?suppkey 
    where 
    {
      ?big_ps ltpch:partsupplier_availqty ?big_ps_availqty ;
              ltpch:supplier_suppkey ?suppkey ;
              ltpch:nation_name ?n_name ;
              ltpch:part_name ?p_name . 
      filter (REGEX (?p_name , "^%COLOR%") && 
              ?n_name = "%NATION%" && 
              xsd:decimal(?big_ps_availqty) > ?qty_threshold)
      {
        select 
          ?big_ps
          ((0.5 * sum(xsd:decimal(?l_linequantity))) as ?qty_threshold)
        where
        {
          ?li qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l_shipdate ;
              ltpch:l_linequantity ?l_linequantity ;
              ltpch:l_has_partsupplier ?big_ps .
          filter ((xsd:date(?l_shipdate) >= xsd:date("%YEAR%-01-01"^^xsd:date)) &&
            (xsd:date(?l_shipdate) < xsd:date("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration))
          )
        } 
        group by
          ?big_ps
      } .
    } 
  }
}
order by ?s_name

          </pre>
        </article>
        <article>
          <h2>Query 21</h2>
          <pre>
select
    ?s_name
    ((count(1)) as ?numwait)
where {
         ?li1 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_receiptdate ?l1_receiptdate ;
              ltpch:l_commitdate ?l1_commitdate ;
              ltpch:l_has_partsupplier ?ps ;
              ltpch:l_has_order ?ord .
         ?ps ltpch:supplier_name ?s_name ;
             ltpch:supplier_suppkey ?suppkey ;
             ltpch:nation_name ?n_name .
         ?ord ltpch:order_orderstatus ?o_orderstatus .
         filter ( 
            xsd:date(?l1_receiptdate) > xsd:date(?l1_commitdate) && 
            ?n_name = "%NATION%" && 
            ?o_orderstatus = "F"
            ) 
         filter exists {
              ?li2 ltpch:l_has_order ?ord ;
                   ltpch:l_has_partsupplier ?ps2 .
              ?ps2 ltpch:supplier_suppkey ?suppkey2 .
              filter (?suppkey != ?suppkey2)
         }
         filter not exists {
              ?li3 ltpch:l_has_order ?ord ;
                   ltpch:l_receiptdate ?l3_receiptdate ;
                   ltpch:l_commitdate ?l3_commitdate ;
                   ltpch:l_has_partsupplier ?ps3 .
              ?ps3 ltpch:supplier_suppkey ?suppkey3 .
              filter (
                 xsd:date(?l3_receiptdate) > xsd:date(?l3_commitdate) &&
                 ?suppkey3 != ?suppkey
              )
         }
       }
group by
   ?s_name
order by
    desc (count(1))
    ?s_name
limit 100

          </pre>
        </article>
        <article>
          <h2>Query 22</h2>
          <pre>
select
  ?cntrycode
  (count (1) as ?numcust)
  (sum (xsd:decimal(?c_acctbal)) as ?totacctbal)
where {
    ?cust ltpch:customer_acctbal ?c_acctbal ;
      ltpch:customer_phone ?c_phone .
      BIND (fn:substring(?c_phone,0, 3) as ?cntrycode)
    {
        select (avg (xsd:decimal(?c_acctbal2)) as ?acctbal_threshold)
          where
            {
              ?cust2 ltpch:customer_acctbal ?c_acctbal2 ;
                 ltpch:customer_phone ?c_phone2 .
              filter ((xsd:decimal(?c_acctbal2) > 0.00) &&
                fn:substring(?c_phone2,0, 3) in (%COUNTRY_CODE_SET%)  )
            }
          }
    filter (
      ?cntrycode in (%COUNTRY_CODE_SET%) &&
      (xsd:decimal(?c_acctbal) > ?acctbal_threshold ) )
    filter not exists { ?cust ltpch:order_orderkey ?orderkey }
  }
group by ?cntrycode
order by ?cntrycode
          </pre>
        </article>
      </section>
</details>
<details>
<summary>Jena queries denormalized pattern</summary>
      <section>
        <article>
        <h2>Prefix</h2>
          <pre>
prefix qb4o: &lt;http://publishing-multidimensional-data.googlecode.com/git/index.html#ref_qbplus_>
prefix qb:   &lt;http://purl.org/linked-data/cube#>
prefix ltpch: &lt;http://extbi.lab.aau.dk/ontology/ltpch/>
prefix xsd: &lt;http://www.w3.org/2001/XMLSchema#> 
prefix fn: &lt;http://www.w3.org/2005/xpath-functions#>

          </pre>
        </article>
        <article>
          <h2>Query 1</h2>
            <pre>
            select
  ?l_returnflag 
  ?l_linestatus 
  (sum(xsd:decimal(?l_linequantity)) as ?sum_qty) 
  (sum(xsd:decimal(?l_lineextendedprice)) as ?sum_base_price) 
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?sum_disc_price) 
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))*(1 + xsd:decimal(?l_linetax))) as ?sum_charge) 
  (avg(xsd:decimal(?l_linequantity)) as ?avg_qty) 
  (avg(xsd:decimal(?l_lineextendedprice)) as ?avg_price) 
  (avg(xsd:decimal(?l_linediscount)) as ?avg_disc) 
  (count(1) as ?count_order)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_returnflag ?l_returnflag ;
       ltpch:l_linestatus ?l_linestatus ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linetax ?l_linetax ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:l_linediscount ?l_linediscount .
    filter (xsd:date(?l_shipdate) <= ("1998-12-01"^^xsd:date + "-P%DELTA%D"^^xsd:duration))
} 
group by
  ?l_returnflag
  ?l_linestatus
order by
  ?l_returnflag
  ?l_linestatus</pre>
        </article>
        <article>
          <h2>Query 2</h2>
            <pre>
select distinct
  ?s_acctbal
  ?s_name
  ?nation_name
  ?p_partkey
  ?p_mfgr
  ?s_address
  ?s_phone
  ?s_comment
where {
  ?li ltpch:partsupplier_partsupplier_supplycost ?minsc ;
    ltpch:partsupplier_supplier_acctbal ?s_acctbal ;
    ltpch:partsupplier_supplier_name ?s_name ;
    ltpch:partsupplier_supplier_address ?s_address ;
    ltpch:partsupplier_supplier_phone ?s_phone ;
    ltpch:partsupplier_supplier_comment ?s_comment ;
    ltpch:partsupplier_nation_name ?nation_name ;
    ltpch:partsupplier_region_name "%REGION%" ;
    ltpch:partsupplier_part_partkey ?p_partkey ;
    ltpch:partsupplier_part_mfgr ?p_mfgr ;
    ltpch:partsupplier_part_size ?size ;
    ltpch:partsupplier_part_type ?p_type .
  FILTER (?size = str(%SIZE%) && fn:contains(?p_type, "%TYPE%"))
  { select ?p_partkey  (min(?s_cost) as ?minsc)
    where {
        ?li ltpch:partsupplier_part_partkey ?p_partkey;
            ltpch:partsupplier_partsupplier_supplycost ?s_cost ;
            ltpch:partsupplier_region_name ?region2 .
            filter (?region2 = "%REGION%")
      } 
      group by ?p_partkey 
    }
     
  }
order by
  desc (?s_acctbal)
  ?nation_name
  ?s_name
  ?p_partk             
            </pre>
        </article>
        <article>
          <h2>Query 3</h2>
            <pre>
select
  ?o_orderkey
  (sum(xsd:decimal(?l_lineextendedprice)*(1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?o_orderdate
  ?o_shippriority
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
    ltpch:l_lineextendedprice ?l_lineextendedprice ;
    ltpch:l_linediscount ?l_linediscount ;
    ltpch:l_shipdate ?l_shipdate ;
    ltpch:order_order_orderdate ?o_orderdate ;
    ltpch:order_order_shippriority ?o_shippriority ;
    ltpch:order_order_orderkey ?o_orderkey ;
    ltpch:order_customer_mktsegment ?c_mktsegment .
  filter ((xsd:date(?o_orderdate) < "%DATE%"^^xsd:date) &&
    (xsd:date(?l_shipdate) > "%DATE%"^^xsd:date) &&
    (?c_mktsegment = "%SEGMENT%") ) 
}
group by
  ?o_orderkey
  ?o_orderdate
  ?o_shippriority
order by
  desc (sum (xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
  ?o_orderdate
limit 10

            </pre>
        </article>
        <article>
          <h2>Query 4</h2>
            <pre>
select
  ?o_orderpriority
  (count(*) as ?order_count)
where  
{
  {
    select distinct
      ?o_orderpriority
      ?ordkey
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_commitdate ?l_commitdate ;
          ltpch:l_receiptdate ?l_receiptdate ;
          ltpch:order_order_orderpriority ?o_orderpriority ;
          ltpch:order_order_orderkey ?ordkey ;
          ltpch:order_order_orderdate ?o_orderdate .
      filter (
        (xsd:date(?l_commitdate) < xsd:date(?l_receiptdate)) &&
        (xsd:date(?o_orderdate) >= "%MONTH%-01"^^xsd:date) &&
        (xsd:date(?o_orderdate) < ("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration))
      )
    }
  }
}
group by
  ?o_orderpriority
order by
  ?o_orderpriority

            </pre>
        </article>
        <article>
          <h2>Query 5</h2>
            <pre>
select
  ?nation
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where  {
   ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:order_order_orderdate ?o_orderdate ;
       ltpch:order_nation_name ?c_nation ;
       ltpch:partsupplier_nation_name ?nation ;
       ltpch:partsupplier_region_name ?r_name .
    
    filter ((?c_nation = ?nation) &&
      (xsd:date(?o_orderdate) >= "%YEAR%-01-01"^^xsd:date) &&
      (xsd:date(?o_orderdate) < ("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) &&
      (?r_name = "%REGION%") ) 
  }
group by
  ?nation
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))

            </pre>
        </article>
        <article>
          <h2>Query 6</h2>
            <pre>
select
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_lineextendedprice ?l_lineextendedprice ;
       ltpch:l_linediscount ?l_linediscount ;
       ltpch:l_linequantity ?l_linequantity ;
       ltpch:l_shipdate ?l_shipdate .
    filter ( (xsd:date(?l_shipdate) >= "%YEAR%-01-01"^^xsd:date) &&
      (xsd:date(?l_shipdate) < xsd:date("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) &&
      (xsd:decimal(?l_linediscount) >= %DISCOUNT% - 0.01) &&
      (xsd:decimal(?l_linediscount) <= %DISCOUNT% + 0.01) &&
      (xsd:decimal(?l_linequantity) < %QUANTITY%) ) 
}
            </pre>
        </article>
        <article>
          <h2>Query 7</h2>
            <pre>
select 
  ?supp_nation 
  ?cust_nation 
  ?li_year
  (sum (xsd:decimal(?volume)) as ?revenue)
where {
  {
    select
      ?supp_nation
      ?cust_nation
      ?li_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_shipdate ?l_shipdate ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:order_nation_name ?cust_nation ;
        ltpch:partsupplier_nation_name ?supp_nation .
      BIND (SUBSTR(STR(?l_shipdate), 1,4) as ?li_year)
filter (xsd:boolean(
        (?cust_nation = "%NATION1%" && ?supp_nation = "%NATION2%") ||
        (?cust_nation = "%NATION2%" && ?supp_nation = "%NATION1%") ) &&
        xsd:boolean(xsd:date(?l_shipdate) >= xsd:date("1995-01-01"^^xsd:date)) &&
        xsd:boolean(xsd:date(?l_shipdate) <= xsd:date("1996-12-31"^^xsd:date)) ) 
      } 
   } 
}
group by
  ?supp_nation
  ?cust_nation
  ?li_year
order by
  ?supp_nation
  ?cust_nation
  ?li_year

            </pre>
        </article>
        <article>
          <h2>Query 8</h2>
            <pre>
select
  ?o_year
  ((?sum1 / ?sum2) as ?mkt_share)
where {
  { select
    ?o_year
    (sum (?volume * xsd:integer(fn:starts-with(?nation, "%NATION%"))) as ?sum1)
    (sum (?volume) as ?sum2)
    where {
      { select
           ((YEAR(xsd:date(?o_orderdate))) as ?o_year)
           ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?volume)
           ?nation
         where {
           ?li qb:dataSet ltpch:lineitemCube ;
               ltpch:l_has_partsupplier ?ps ;
               ltpch:l_lineextendedprice ?l_lineextendedprice ;
               ltpch:l_linediscount ?l_linediscount ;
               ltpch:partsupplier_nation_name ?nation ;
               ltpch:partsupplier_part_type ?type ;
               ltpch:order_order_orderdate ?o_orderdate ;
               ltpch:order_region_name ?region .
           filter ((xsd:date(?o_orderdate) >= xsd:date("1995-01-01"^^xsd:date)) &&
             (xsd:date(?o_orderdate) <= xsd:date("1996-12-31"^^xsd:date)) &&
              (?region = "%REGION%") &&
              (?type = "%TYPE%") 
           ) 
        } 
      } 
    }
    group by
      ?o_year 
  } 
}
order by
  ?o_year

            </pre>
        </article>
        <article>
          <h2>Query 9</h2>
            <pre>
select
  ?nation
  ?o_year
  (sum(?amount) as ?sum_profit)
where {
  { select
      ?nation
      ?o_year
      ((xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)) - xsd:decimal(?ps_supplycost) * xsd:decimal(?l_linequantity)) as ?amount)
    where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_linequantity ?l_linequantity ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount ;
          ltpch:partsupplier_nation_name ?nation ;
          ltpch:partsupplier_partsupplier_supplycost ?ps_supplycost ;
          ltpch:partsupplier_part_name ?p_name ;
          ltpch:order_order_orderdate ?o_orderdate .
      filter (REGEX (?p_name, "%COLOR%"))
      BIND (SUBSTR(STR(?o_orderdate), 1,4) as ?o_year)
    } 
  } 
}
group by
  ?nation
  ?o_year
order by
  ?nation
  desc (?o_year)

            </pre>
        </article>
        <article>
          <h2>Query 10</h2>
            <pre>
select
  ?c_custkey
  ?c_companyName
  (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))) as ?revenue)
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
where  {
  ?li qb:dataSet ltpch:lineitemCube ;
      ltpch:l_returnflag ?l_returnflag ;
      ltpch:l_lineextendedprice ?l_lineextendedprice ;
      ltpch:l_linediscount ?l_linediscount ;
      ltpch:order_order_orderdate ?o_orderdate ;
      ltpch:order_customer_address ?c_address ;
      ltpch:order_customer_phone ?c_phone ;
      ltpch:order_customer_comment ?c_comment ;
      ltpch:order_customer_acctbal ?c_acctbal ;
      ltpch:order_customer_custkey ?c_custkey ;
      ltpch:order_customer_name ?c_companyName ;
      ltpch:order_nation_name ?nation .
   filter ((xsd:date(?o_orderdate) >= xsd:date("%MONTH%-01"^^xsd:date)) &&
      (xsd:date(?o_orderdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration)) &&
      (?l_returnflag = "R") 
   ) 
}
group by
  ?c_custkey
  ?c_companyName
  ?c_acctbal
  ?nation
  ?c_address
  ?c_phone
  ?c_comment
order by
  desc (sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount))))
limit 20

            </pre>
        </article>
        <article>
          <h2>Query 11</h2>
          <pre>
select
  ?bigpspart
  ?bigpsvalue
where {
      { select
          ?bigpspart
          (sum(xsd:decimal(?b_supplycost) * xsd:decimal(?b_availqty)) as ?bigpsvalue)
        where
          {
            ?bigps ltpch:partsupplier_part_partkey ?bigpspart ;
                   ltpch:partsupplier_partsupplier_supplycost ?b_supplycost ;
                   ltpch:partsupplier_partsupplier_availqty ?b_availqty ;
                    ltpch:partsupplier_nation_name "%NATION%" .
          }
          group by ?bigpspart
      }
      {
       select
          (sum(xsd:decimal(?t_supplycost) * xsd:decimal(?t_availqty)) * %FRACTION% as ?threshold)
        where
          {
            ?thr_ps ltpch:partsupplier_partsupplier_supplycost ?t_supplycost ;
                    ltpch:partsupplier_partsupplier_availqty ?t_availqty ;
                    ltpch:partsupplier_nation_name "%NATION%" .
          }
      }
      filter (?bigpsvalue > ?threshold )
  }
order by
  desc (?bigpsvalue)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 12</h2>
          <pre>
select
  ?l_shipmode
  (sum (
      xsd:integer(fn:starts-with(?o_orderpriority, "1-URGENT") ||
      fn:starts-with(?o_orderpriority, "2-HIGH") ) ) as ?high_line_count)
  (sum (1 -
      xsd:integer(fn:starts-with(?o_orderpriority, "1-URGENT") ||
      fn:starts-with(?o_orderpriority, "2-HIGH") ) ) as ?low_line_count)
where  {
    ?li qb:dataSet ltpch:lineitemCube ;
       ltpch:l_commitdate ?l_commitdate ;
       ltpch:l_receiptdate ?l_receiptdate ;
       ltpch:l_shipmode ?l_shipmode ;
       ltpch:l_shipdate ?l_shipdate ;
       ltpch:order_order_orderpriority ?o_orderpriority .
     filter (?l_shipmode in ("%SHIPMODE1%", "%SHIPMODE2%") &&
      (xsd:date(?l_commitdate) < xsd:date(?l_receiptdate)) &&
      (xsd:date(?l_shipdate) < xsd:date(?l_commitdate)) &&
      (xsd:date(?l_receiptdate) >= "%YEAR%-01-01"^^xsd:date) &&
      (xsd:date(?l_receiptdate) < ("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration)) )
  }
group by
  ?l_shipmode
order by
  ?l_shipmode
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 13</h2>
          <pre>
select
  ?c_count
  (count(1) as ?custdist)
where {
    { select
        ?c_custkey
        (count (?orderkey) as ?c_count)
      where
        {
          {
            select distinct ?c_custkey 
            where
            {
              ?li ltpch:order_customer_custkey ?c_custkey .
            }
          }
           optional {
            {
              select ?orderkey
              where {
              ?li2 ltpch:order_customer_custkey ?c_custkey ;
                   ltpch:order_order_orderkey ?orderkey  ;
                   ltpch:order_order_comment ?o_comment .
              filter (!( REGEX (?o_comment , "%WORD1%.*%WORD2%" ) ) ) .
              }
              group by ?orderkey
            }
          }
        }
      group by 
        ?c_custkey
    }
  }
group by
  ?c_count
order by
  desc (count(1))
  desc (?c_count)

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 14</h2>
          <pre>
select
((100 * sum(xsd:integer(fn:starts-with(?p_type, "PROMO")) * xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))  / sum(xsd:decimal(?l_lineextendedprice) *  (xsd:decimal(1) - xsd:decimal(?l_linediscount)))) as ?promo_revenue)
  where {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_lineextendedprice ?l_lineextendedprice ;
          ltpch:l_linediscount ?l_linediscount ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:partsupplier_part_type ?p_type .
      filter (xsd:date(?l_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
      (xsd:date(?l_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P1M"^^xsd:duration)) )
  
}
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 15</h2>
          <pre>
select distinct
  ?s_suppkey
  ?s_name
  ?s_address
  ?s_phone
  ?total_revenue
where  
{
  ?partsupp ltpch:partsupplier_supplier_suppkey ?s_suppkey ;
            ltpch:partsupplier_supplier_name ?s_name ;
            ltpch:partsupplier_supplier_address ?s_address ;
            ltpch:partsupplier_supplier_phone ?s_phone .
  { 
    select
        ?s_suppkey
        ((sum(xsd:decimal(?l_extendedprice) * (1 - xsd:decimal(?l_discount)))) as ?total_revenue)
    where 
    {
      ?li qb:dataSet ltpch:lineitemCube ;
          ltpch:l_shipdate ?l_shipdate ;
          ltpch:l_lineextendedprice ?l_extendedprice ;
          ltpch:l_linediscount ?l_discount ;
          ltpch:l_has_partsupplier ?ps ;
          ltpch:partsupplier_supplier_suppkey ?s_suppkey .
      filter (
                xsd:date(?l_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
                xsd:date(?l_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration) )
    }
    group by
      ?s_suppkey
  } .
  { 
    select 
      (max (?l2_total_revenue) as ?maxtotal)
    where 
    {
      { 
        select
          ((sum(xsd:decimal(?l2_extendedprice) * (1 - xsd:decimal(?l2_discount)))) as ?l2_total_revenue)
        where 
        {
          ?li2 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l2_shipdate ;
              ltpch:l_lineextendedprice ?l2_extendedprice ;
              ltpch:l_linediscount ?l2_discount ;
              ltpch:l_has_partsupplier ?ps2 ;
              ltpch:partsupplier_supplier_suppkey ?s_suppkey2 .
          filter (
                        xsd:date(?l2_shipdate) >= xsd:date("%MONTH%-01"^^xsd:date) &&
                        xsd:date(?l2_shipdate) < xsd:date("%MONTH%-01"^^xsd:date + "P3M"^^xsd:duration) )
        }
        group by
          ?s_suppkey2
      }
    }
  }
  filter (?total_revenue = ?maxtotal)
}
order by
  ?s_suppkey

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 16</h2>
          <pre>
select
  ?p_brand
  ?p_type
  ?p_size
  (count(distinct ?supp) as ?supplier_cnt)
where {
    ?ps ltpch:partsupplier_part_brand ?p_brand ;
        ltpch:partsupplier_part_type ?p_type ;
        ltpch:partsupplier_part_size ?p_size ;   
        ltpch:partsupplier_supplier_suppkey ?supp .    
    filter (
      (?p_brand != "%BRAND%") &&
      !(fn:starts-with(?p_type,"%TYPE%")) &&
      (xsd:integer(?p_size) in (%SIZE1%, %SIZE2%, %SIZE3%, %SIZE4%, %SIZE5%, %SIZE6%, %SIZE7%, %SIZE8%))
    )
    filter NOT EXISTS {
       ?supp a ltpch:supplier;
             ltpch:s_comment ?badcomment .
       filter ( fn:matches (?badcomment ,"Customer.*Complaints") )
    }
  }
group by
  ?p_brand
  ?p_type
  ?p_size
order by
  desc ((count(distinct ?supp)))
  ?p_brand
  ?p_type
  ?p_size

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 17</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice)) / 7.0) as ?avg_yearly)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_has_partsupplier ?ps ;
        ltpch:partsupplier_part_partkey ?p_partkey.
    {
      select 
        ?p_partkey
        ((0.2 * avg(xsd:decimal(?l2_linequantity))) as ?threshold)
      where { 
        ?li2  a ltpch:lineitem ;
              ltpch:l_linequantity ?l2_linequantity ; 
              ltpch:partsupplier_part_partkey ?p_partkey ;
              ltpch:partsupplier_part_container ?p_container ;
              ltpch:partsupplier_part_brand ?p_brand  .
      }
      group by
        ?p_partkey
    }
    filter (xsd:decimal(?l_linequantity) < ?threshold && REGEX(?p_brand,"%BRAND%","i") && ?p_container = "%CONTAINER%") 
}
          </pre>
        </article>
        </article>
        <article>
          <h2>Query 18</h2>
          <pre>
select
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
   (sum(xsd:decimal(?l_linequantity)) as ?l_quantity)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:order_order_orderkey ?o_orderkey ;
        ltpch:order_order_orderdate ?o_orderdate ;
        ltpch:order_order_ordertotalprice ?o_ordertotalprice ;
        ltpch:order_customer_custkey ?c_custkey ;
        ltpch:order_customer_name ?c_name .
    { 
      select 
        ?o_orderkey 
        (sum (xsd:decimal(?l2_linequantity)) as ?sum_q)
      where 
      {
        ?li2 a ltpch:lineitem ;
             ltpch:l_linequantity ?l2_linequantity ;
             ltpch:order_order_orderkey ?o_orderkey .
      }
      group by
        ?o_orderkey
    } .
    filter (xsd:decimal(?sum_q) > xsd:decimal(%QUANTITY%))
}
group by
   ?c_name
   ?c_custkey
   ?o_orderkey
   ?o_orderdate
   ?o_ordertotalprice
order by
  desc (?o_ordertotalprice)
  ?o_orderdate
limit 100

          </pre>
        </article>
        </article>
        <article>
          <h2>Query 19</h2>
          <pre>
select
  ((sum(xsd:decimal(?l_lineextendedprice) * (1 - xsd:decimal(?l_linediscount)))) as ?revenue)
where {
    ?li qb:dataSet ltpch:lineitemCube ;
        ltpch:l_lineextendedprice ?l_lineextendedprice ;
        ltpch:l_linediscount ?l_linediscount ;
        ltpch:l_linequantity ?l_linequantity ;
        ltpch:l_shipmode ?l_shipmode ;
        ltpch:l_shipinstruct ?l_shipinstruct ;
        ltpch:partsupplier_part_brand ?p_brand ;
        ltpch:partsupplier_part_size ?p_size ;
        ltpch:partsupplier_part_container ?p_container .
     filter (?l_shipmode in ("AIR", "AIR REG") &&
      ?l_shipinstruct = "DELIVER IN PERSON" &&
      ( ( (REGEX(?p_brand,"^%BRAND1%$","i")) &&
          (?p_container in ("SM CASE", "SM BOX", "SM PACK", "SM PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY1%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY1% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 5) ) ||
        ( (REGEX(?p_brand,"^%BRAND2%$","i")) &&
          (?p_container in ("MED BAG", "MED BOX", "MED PKG", "MED PACK")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY2%) && 
          (xsd:integer(?l_linequantity) <= %QUANTITY2% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 10) ) ||
        ( (REGEX(?p_brand,"^%BRAND3%$","i")) &&
          (?p_container in ("LG CASE", "LG BOX", "LG PACK", "LG PKG")) &&
          (xsd:integer(?l_linequantity) >= %QUANTITY3%) &&
          (xsd:integer(?l_linequantity) <= %QUANTITY3% + 10) &&
          (xsd:integer(?p_size) >= 1) && (xsd:integer(?p_size) <= 15) ) ) )
  }

          </pre>
        </article>
        <article>
          <h2>Query 20</h2>
          <pre>
select distinct
  ?s_name
  ?s_address
where
{
  ?supp ltpch:partsupplier_supplier_name ?s_name ;
        ltpch:partsupplier_supplier_suppkey ?suppkey ;
        ltpch:partsupplier_supplier_address ?s_address .
  { 
    select 
      distinct ?suppkey 
    where 
    {
      ?li ltpch:partsupplier_partsupplier_availqty ?big_ps_availqty ;
              ltpch:partsupplier_supplier_suppkey ?suppkey ;
              ltpch:partsupplier_nation_name ?n_name ;
              ltpch:partsupplier_part_name ?p_name . 
      filter (REGEX (?p_name , "^%COLOR%") && 
              ?n_name = "%NATION%" && 
              xsd:decimal(?big_ps_availqty) > ?qty_threshold)
      {
        select 
          ?li
          ((0.5 * sum(xsd:decimal(?l_linequantity))) as ?qty_threshold)
        where
        {
          ?li qb:dataSet ltpch:lineitemCube ;
              ltpch:l_shipdate ?l_shipdate ;
              ltpch:l_linequantity ?l_linequantity .
          filter ((xsd:date(?l_shipdate) >= xsd:date("%YEAR%-01-01"^^xsd:date)) &&
            (xsd:date(?l_shipdate) < xsd:date("%YEAR%-01-01"^^xsd:date + "P1Y"^^xsd:duration))
          )
        } 
        group by
          ?li
      } .
    } 
  }
}
order by ?s_name

          </pre>
        </article>
        <article>
          <h2>Query 21</h2>
          <pre>
select
    ?s_name
    ((count(1)) as ?numwait)
where {
         ?li1 qb:dataSet ltpch:lineitemCube ;
              ltpch:l_receiptdate ?l1_receiptdate ;
              ltpch:l_commitdate ?l1_commitdate ;
              ltpch:partsupplier_supplier_name ?s_name ;
              ltpch:partsupplier_supplier_suppkey ?suppkey ;
              ltpch:partsupplier_nation_name ?n_name ;
              ltpch:order_order_orderkey ?orderkey ;
              ltpch:order_order_orderstatus ?o_orderstatus .
         filter ( 
            xsd:date(?l1_receiptdate) > xsd:date(?l1_commitdate) && 
            ?n_name = "%NATION%" && 
            ?o_orderstatus = "F"
            ) 
         filter exists {
              ?li2 ltpch:order_order_orderkey ?orderkey ;
                   ltpch:partsupplier_supplier_suppkey ?suppkey2 .
              filter (?suppkey != ?suppkey2)
         }
         filter not exists {
              ?li3 ltpch:order_order_orderkey ?orderkey ;
                   ltpch:l_receiptdate ?l3_receiptdate ;
                   ltpch:l_commitdate ?l3_commitdate ;
                   ltpch:partsupplier_supplier_suppkey ?suppkey3 .
              filter (
                 xsd:date(?l3_receiptdate) > xsd:date(?l3_commitdate) &&
                 ?suppkey3 != ?suppkey
              )
         }
       }
group by
   ?s_name
order by
    desc (count(1))
    ?s_name
limit 100

          </pre>
        </article>
        <article>
          <h2>Query 22</h2>
          <pre>
select
  ?cntrycode
  (count (1) as ?numcust)
  (sum (xsd:decimal(?c_acctbal)) as ?totacctbal)
where {
    ?cust ltpch:order_customer_acctbal ?c_acctbal ;
      ltpch:order_customer_phone ?c_phone .
      BIND (fn:substring(?c_phone,0, 3) as ?cntrycode)
    { select (avg (xsd:decimal(?c_acctbal2)) as ?acctbal_threshold)
          where
            {
              ?li ltpch:order_customer_acctbal ?c_acctbal2 ;
                 ltpch:order_customer_phone ?c_phone2 .
              filter ((xsd:decimal(?c_acctbal2) > 0.00) &&
                fn:substring(?c_phone2,0, 3) in (%COUNTRY_CODE_SET%)  )
            } 
    }
    filter (
      ?cntrycode in (%COUNTRY_CODE_SET%) &&
      (xsd:decimal(?c_acctbal) > ?acctbal_threshold ) )
    filter not exists { ?cust ltpch:order_order_orderkey ?orderkey }
  }
group by ?cntrycode
order by ?cntrycode
          </pre>
        </article>
      </section>
</details>
      
    </div>
    <aside id="right_column">
      <?php include '../topics.html';?>
      <?php include 'resources.html';?>
      <!-- /nav -->
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