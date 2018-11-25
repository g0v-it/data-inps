//get accounts
module.exports = {
	query : `
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX bgo: <http://linkeddata.center/lodmap-bgo/v1#>


DESCRIBE ?bgo ?partitionScheme  ?partitionOrderedList ?bubble WHERE {
 
  ?bgo a bgo:BubbleGraph ; 
		bgo:partitionScheme ?partitionScheme ;
		bgo:partitionOrderedList ?partitionOrderedList . 
  ?bubble a bgo:Account.
}
`
}
