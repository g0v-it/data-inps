//get account

module.exports = (id) => {
	return ({
	query : `
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX bgo: <http://linkeddata.center/lodmap-bgo/v1#>

CONSTRUCT { 
   ?bubbleUri a bgo:Account ;
		bgo:inBubbleGraph ?bubbleGraph;
		bgo:code ?code ;
		dct:title ?title ;
		dct:description ?description ;
		dct:subject ?subject ;
		dct:source ?fact ;
		bgo:amount ?amount ;
		bgo:version ?year;
		bgo:previousValue ?previousValue ;
		bgo:partitionLabel ?partitionLabel ;
  		bgo:isVersionOf ?historyRec.
     
    ?historyRec a bgo:VersionedAmount;  
    	bgo:version ?historyVersion; 
    	bgo:amount ?historyAmount .
    	
    ?bubbleGraph a bgo:BubbleGraph; bgo:um ?um .
} WHERE {
   ?bubbleUri a bgo:Account ;
		bgo:inBubbleGraph ?bubbleGraph;
		bgo:code ?code ;
		dct:title ?title ;
		dct:description ?description ;
		dct:subject ?subject ;
		dct:source ?fact ;
		bgo:amount ?amount ;	
		bgo:version ?year;
		bgo:partitionLabel ?partitionLabel.
  	
  	OPTIONAL { ?bubbleUri bgo:previousValue ?previousValue }
  	OPTIONAL { 
      ?bubbleUri bgo:isVersionOf ?historyRec .
      ?historyRec bgo:version ?historyVersion; bgo:amount ?historyAmount
    }
  
    ?bubbleGraph bgo:um ?um.
    # FILTER (?code = "deb6ab43991a693a51b608d95f211273")
    FILTER (?code = "${id}") 
}
`})
}
