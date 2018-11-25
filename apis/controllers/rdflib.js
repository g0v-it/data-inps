//RDFLIB
const $rdf = require("rdflib");
//Namspaces
const DCT = $rdf.Namespace("http://purl.org/dc/terms/"), BGO = $rdf.Namespace("http://linkeddata.center/lodmap-bgo/v1#"),
RDF = $rdf.Namespace("http://www.w3.org/1999/02/22-rdf-syntax-ns#");

//Attributes devided by namespace
let accountSimpleWordsBGO = [
    "code",
    "amount",
    "previousValue",
    "version"
], accountSimpleWordsDCT = [
    "source",
    "title",
    "subject",
    "description"
]

let accountData = `@prefix dct: <http://purl.org/dc/terms/> .
@prefix bgo: <http://linkeddata.center/lodmap-bgo/v1#> .
@base <http://lodmap-bgo.example.com/api/v1/> .
    
<account/a1#data> a bgo:Account ;
    bgo:inBubbleGraph <accounts#bubbleGraph> ;
    bgo:code "24823764283476242387" ;
    dct:title "the first fact"@en ;
    dct:source <http://example.org/resource/account_a1> ;
    dct:description "This is the description of the account a1" ;
    dct:subject "this is the path" ;
    bgo:version "2018";
    bgo:partitionLabel "Finanze", "Lavoro e Politiche Sociali";
    bgo:amount 1000.00 ;
    bgo:previousValue 800.00  ;
    bgo:isVersionOf
        [ bgo:version "2017"; bgo:amount 800.00 ],
        [ bgo:version "2016"; bgo:amount 1100.00 ]
    ;
    bgo:hasPart
        [ dct:title "amount detail 1 of a1"; bgo:amount 400 ], 
        [ dct:title "amount detail 2 of a1"; bgo:amount 600 ]
.

<accounts#bubbleGraph> 
    bgo:um "EUR" ;
.`,
accountsData = `@prefix dct: <http://purl.org/dc/terms/> .
@prefix bgo: <http://linkeddata.center/lodmap-bgo/v1#> .
@base <http://lodmap-bgo.example.com/api/v1/> .

<accounts#bubbleGraph> a bgo:BubbleGraph ;
    dct:title "Balance of 2017"@en ;
    dct:description "A bubble graph representation of a balance"@en ;
    dct:source <http://example.org/resource/dataset_uri> ;
    bgo:um "EUR" ;
    bgo:partitionScheme 
        <accounts#default_partition>,
        <accounts#top_partition>,
        <accounts#second_partition>;
    bgo:partitionOrderedList ( 
        <accounts#default_partition>
        <accounts#top_partition>
        <accounts#second_partition>
    )
.

<account/a1#data> a bgo:Account ;
    bgo:inBubbleGraph <accounts#bubbleGraph> ;
    bgo:code "24823764283476242387" ;
    dct:subject "this is the path" ;
    dct:title "the first fact"@en ;
    bgo:partitionLabel "Finanze", "Lavoro e Politiche Sociali";
    bgo:amount 1000.00 ;
    bgo:previousValue 800.00
.
<account/a2#data> a bgo:Account ;
    bgo:inBubbleGraph <accounts#bubbleGraph> ;
    bgo:code "24823764283476abc" ;
    dct:subject "this is the path" ;
    dct:title "the second fact"@en ;
    bgo:partitionLabel "Finanze", "altra missione";
    bgo:amount 10000.00 ;
    bgo:previousValue 8000.00 
.

<accounts#default_partition> a bgo:PartitionScheme ;
    bgo:partitionSchemeId "default_partition";
    dct:title "STATO"
.

<accounts#top_partition>  a bgo:PartitionScheme ;
    bgo:partitionSchemeId "top_partition"; 
    dct:title "MINISTERO";
    bgo:partition
        [   bgo:label "Turismo"; bgo:partitionAmount 139492783471.0 ],
        [   bgo:label "Finanze"; bgo:partitionAmount 149278346786671.0 ]
.
            
<accounts#second_partition>  a bgo:PartitionScheme ;
    bgo:partitionSchemeId "second_partition";
    dct:title "MISSIONE";
    bgo:partition
        [   bgo:label "Lavoro e Politiche Sociali"; bgo:partitionAmount 139492783471.0 ],
        [   bgo:label "altra missione"; bgo:partitionAmount 1123783471.0 ]
.`;

exports.parseAccounts = async (data) => {
    return new Promise(async (resolve, reject) =>{
        let store = $rdf.graph(),
        base = 'https://data.inps.g0v.it/api/v2',
        bubbleGraph, accounts, partitions, //My subjects
        jsonMeta = {}, jsonPartition = {}, jsonPartitions = {}, jsonAccounts = [], jsonAccount = {}, json = {}, partitionSchemeId; //My data Structures
        

        $rdf.parse(data, store, base, "text/turtle");
        //Set the subjects
        bubbleGraph = store.any(undefined, RDF("type"), BGO("BubbleGraph"));
        accounts = store.each(undefined, RDF("type"), BGO("Account"));
        partitions = store.each(undefined, RDF("type"), BGO("PartitionScheme"));


        jsonMeta['title'] = store.any(bubbleGraph, DCT("title")).value;
        jsonMeta['description'] = store.any(bubbleGraph, DCT("description")).value;
        jsonMeta['source'] = store.any(bubbleGraph, DCT("source")).value;
        jsonMeta['um'] = store.any(bubbleGraph, BGO("um")).value;

        //OrderdList treated as a collection with a bunch of element
        //jsonPartition
        jsonMeta['partitionOrderedList'] = [];
        store.any(bubbleGraph, BGO("partitionOrderedList")).elements.forEach(node => {
            partitionSchemeId = store.any(node, BGO("partitionSchemeId")).value; //Get Partition Schema id to be used later
            jsonPartition['title'] = store.any(node, DCT("title")).value;
            jsonPartition['partitions'] = [];

            store.each(node, BGO("partition")).forEach((subNode) => {
                //Construnction of object
                jsonPartition['partitions'].push({
                    label : store.any(subNode, BGO("label")).value,
                    partitionAmount : store.any(subNode, BGO("partitionAmount")).value
                });
            })
            //Putting everything together
            jsonPartitions[partitionSchemeId] = jsonPartition;
            jsonMeta['partitionOrderedList'].push(partitionSchemeId);
            jsonPartition = {};
        });
        jsonMeta['partitionScheme'] = jsonPartitions;

        //Finally, the accounts
        accounts.forEach((account) => {
            jsonAccount['code'] = store.any(account, BGO("code")).value;
            jsonAccount['amount'] = parseFloat(store.any(account, BGO("amount")).value);
            jsonAccount['previousValue'] = parseFloat(store.any(account, BGO("previousValue")).value);
            jsonAccount['subject'] = store.any(account, DCT("subject")).value;
            jsonAccount["partitionLabel"] = [];
            store.each(account, BGO("partitionLabel")).forEach((partition) => {
                jsonAccount["partitionLabel"].push(partition.value);
            });
            jsonAccounts.push(jsonAccount);
            jsonAccount = {}; 
        });
        jsonMeta['accounts'] = jsonAccounts;

        resolve(jsonMeta);
    });
}

exports.parseAccount = async (data) => {
    return new Promise(async (resolve, reject) =>{
        let store = $rdf.graph(),
        base = 'https://data.inps.g0v.it/api/v2/account',
        isVersionOfObject = {}, hasPartObject = {}, jsonAccount = {}, //my data structure
        account; //My subject


        $rdf.parse(accountData, store, base , "text/turtle");  // pass base URI
        account = store.any(undefined, RDF("type"), BGO("Account")); //Get the subject based on Type (it may change)
        
        //Build Simple Params
        let value;
        accountSimpleWordsBGO.forEach((word) => {
            value = store.any(account, BGO(word)).value;
            if(word == "amount" || word == "previousValue")
                value = parseFloat(value);
            jsonAccount[word] = value;
        });
        accountSimpleWordsDCT.forEach((word) => {
            jsonAccount[word] = store.any(account, DCT(word)).value;
        });


        //Partitions partitionLabel
        jsonAccount["partitionLabel"] = [];
        store.each(account, BGO("partitionLabel")).forEach((partition) => {
            jsonAccount["partitionLabel"].push(partition.value);
        });


        //IsVersionOf
        jsonAccount['isVersionOf'] = [];
        store.each(account, BGO("isVersionOf")).forEach(node => {
            isVersionOfObject["version"] = store.any(node, BGO("version")).value;
            isVersionOfObject["amount"] = parseFloat(store.any(node, BGO("amount")).value);
            jsonAccount['isVersionOf'].push(isVersionOfObject);
            isVersionOfObject = {};
        });

        //Haspart
        jsonAccount['hasPart'] = [];
        store.each(account, BGO("hasPart")).forEach(node => {
            hasPartObject["title"] = store.any(node, DCT("title")).value;
            hasPartObject["amount"] = parseFloat(store.any(node, BGO("amount")).value);
            jsonAccount['hasPart'].push(hasPartObject);
            hasPartObject = {};
        });

        resolve(jsonAccount);
    });
}

function getUri(code){
    //return "http://lodmap-bgo.example.com/api/v1/account/" + code;
    //return "http://lodmap-bgo.example.com/api/v1/account" + "/" + code
    return code;
}

//RDFLIB