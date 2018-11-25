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
		dct:subject ?subject ;
		dct:source ?fact ;
		bgo:amount ?amount ;
		bgo:version ?year;
		bgo:partitionLabel ?partitionLabel.
    ?bubbleGraph bgo:um ?um.
} WHERE {
   ?bubbleUri a bgo:Account ;
		bgo:inBubbleGraph ?bubbleGraph;
		bgo:code ?code ;
		dct:title ?title ;
		dct:subject ?subject ;
		dct:source ?fact ;
		bgo:amount ?amount ;
		bgo:version ?year;
		bgo:partitionLabel ?partitionLabel.
  
    ?bubbleGraph bgo:um ?um.
    # FILTER (?code = "d911dc1293e3c11c547955776f812dcd")
    FILTER (?code = "${id}") 
}
`})
}
