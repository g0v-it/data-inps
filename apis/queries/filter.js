//get filter

module.exports = (first, second, third, groupBy) => {
	return ({
		query : `
		PREFIX dct: <http://purl.org/dc/terms/> 
		PREFIX bgo: <http://linkeddata.center/lodmap-bgo/v1#>

		SELECT ?${groupBy} (SUM (?account_amount) AS ?amount)
		WHERE {
		  ?accountUri a bgo:Account ;
		                bgo:amount ?account_amount ;
		                bgo:partitionLabel ?partition1 ;
		                bgo:partitionLabel ?partition2 ;
		                bgo:partitionLabel ?partition3 .
		  FILTER regex(?partition1, "${first}")
		  FILTER regex(?partition2, "${second}")
		  FILTER regex(?partition3, "${third}")
		  FILTER (?partition1 != ?partition2)
		  FILTER (?partition1 != ?partition3)
		  FILTER (?partition2 != ?partition3)
		} GROUP BY ?${groupBy}
	`
})
}

