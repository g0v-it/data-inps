//get filter

module.exports = (first, second, third, groupBy) => {
	return ({
		query : `
		PREFIX dct: <http://purl.org/dc/terms/> 
		PREFIX bgo: <http://linkeddata.center/lodmap-bgo/v1#>
		PREFIX accounts: <https://data.inps.g0v.it/ldp/accounts#>
		PREFIX resource: <http://inps.linkeddata.cloud/resource/>
		PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
		PREFIX fr: <http://linkeddata.center/botk-fr/v1#>

		SELECT ?${groupBy} (SUM (?account_amount) AS ?amount)
		WHERE {
		  {
		  	SELECT DISTINCT ?accountUri ?p1_entrate_uscite ?p2_categorie ?p3_upb ?account_amount 
		    WHERE  {
		      ?accountUri a bgo:Account ;
		                bgo:amount ?account_amount ;
		                bgo:partitionLabel ?p1_entrate_uscite ;
		                bgo:partitionLabel ?p2_categorie  ;
		                bgo:partitionLabel ?p3_upb .
		  	
		      accounts:upb bgo:partition ?upb.
		      accounts:entrate_uscite bgo:partition ?entrate_uscite.
		      accounts:categorie bgo:partition ?categorie.
		      
		      ?upb bgo:label ?p3_upb.
		      ?entrate_uscite bgo:label ?p1_entrate_uscite.
		      ?categorie bgo:label ?p2_categorie .
		    }
		  }
		  
		  FILTER regex(?p1_entrate_uscite, "${first}")
		  FILTER regex(?p2_categorie , "${second}")
		  FILTER regex(?p3_upb, "${third}")
		} GROUP BY ?${groupBy} ORDER BY (?amount)
		`
})
}

