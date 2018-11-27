//get filter

module.exports = (first, second, groupBy) => {
	return ({
		query : `
		PREFIX dct: <http://purl.org/dc/terms/> 
		PREFIX bgo: <http://linkeddata.center/lodmap-bgo/v1#>

		SELECT ?${groupBy} (SUM (?account_amount) AS ?amount)
		WHERE {
		  ?accountUri a bgo:Account ;
		                bgo:amount ?account_amount ;
		                bgo:partitionLabel ?partition1 ;
		                bgo:partitionLabel ?partition2 .
		  FILTER regex(?partition1, "${first}")
		  FILTER regex(?partition2, "${second}")
		  FILTER (?partition1 != ?partition2)
		} GROUP BY ?${groupBy}
	`
})
}

