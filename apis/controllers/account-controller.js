//Files
const config = require('../config.js'),
rdflib = require('./rdflib.js');
//Default values
const DEFAULT_SCHEMA_ACCOUNTS = "bubbles",
	DEFAULT_SCHEMA_ACCOUNT = "full",
	DEFAULT_ACCEPT = "text/turtle";

const partition1 = "partition1"
partition2 = "partition2";


//Modules
const http = require('http'),
{URL} = require('url'),
csv = require('csvtojson'),
zip = require('lz-string'),
querystring = require('querystring');

//#######################################GET_ROUTES################################################
exports.getAccounts = async (req, res) => {
	let queryAccounts, accountsJson;
	queryAccounts = require('../queries/get-accounts.js');
	accountsJson = await rdflib.parseAccounts(await getQueryResult(config.endpoint, queryAccounts), DEFAULT_ACCEPT);
	res.json(accountsJson);
}

exports.getAccount = async (req, res) => {
	let queryAccount, outputJson;
	queryAccount = require('../queries/get-account.js')(req.params.id);
	outputJson = await rdflib.parseAccount(await getQueryResult(config.endpoint, queryAccount), DEFAULT_ACCEPT);
	res.json(outputJson);
}


// exports.getPartitionLabels =  async (req, res) => {
//	//Variables
//	let queriesPartitionLabels, topPartitionLabelsJson, secondPartitionLabelsJson, outputJson;
//
//	queriesPartitionLabels = require('../queries/get-partition-labels.js');
//	
//	//Get the lables
//	topPartitionLabelsJson = await csv().fromString(
//		await getQueryResult(config.endpoint, queriesPartitionLabels.top_partition_labels));
// 	secondPartitionLabelsJson = await csv().fromString(
//		await getQueryResult(config.endpoint, queriesPartitionLabels.second_partition_labels));
//
// 	//Build the json
// 	outputJson = {};
// 	outputJson.top_partition = topPartitionLabelsJson;
// 	outputJson.second_partition = secondPartitionLabelsJson;
//
// 	res.json(outputJson);
//}

exports.getStats = async (req, res) => {
	let queryStats, result;

	queryStats = require('../queries/get-stats.js');
	result = await getQueryResult(config.endpoint, queryStats, "application/json");

	res.send(result);

}

exports.getFilter = async (req, res) => {
	let filters = req.query.filters;
	filters = JSON.parse(zip.decompressFromBase64(filters));
	//Prepare filters
	let filter1 = filters[Object.keys(filters)[0]].join('|');
	let filter2 = filters[Object.keys(filters)[1]].join('|');
	//prepare queries
	let query1 = require('../queries/filter.js')(filter1, filter2, partition1);
	let query2 = require('../queries/filter.js')(filter1, filter2, partition2);
	//prepare data
	let object1 = await buildJsonFilter(await getQueryResult(config.endpoint, query1, 'text/csv'), partition1);
	let object2 = await buildJsonFilter(await getQueryResult(config.endpoint, query2, 'text/csv'), partition2);
	//prepare result
	let result = {};
	result[Object.keys(filters)[0]] = object1;
	result[Object.keys(filters)[1]] = object2;
	res.json(result);
}


/**
	* @endpoint must a be a complete path (e.s. https://query.wikidata.org/sparql)
	* @query must be an object (e.s. {query : "this is a query" })
*/
function getQueryResult(endpoint, query, format = DEFAULT_ACCEPT){
	return new Promise((resolve, reject) => {
		let url = new URL(endpoint),
		result,
		options = {
			host: url.hostname,
			port: url.port,
			path: url.pathname,
			method: 'POST',
			headers: {
          		'Accept': format,
          		'Content-Type': 'application/x-www-form-urlencoded'
      		}
		};

		query = querystring.stringify(query);

		const request = http.request(options, (res)=> {
			result = ""; //inizialize
			res.on('data', (chunk) => {
				result += chunk;
			});

			res.on('end', ()=> {
				console.log('No more data in response');
				//console.log(result);
				resolve(result);
			});

			res.on('error', (e) => {
  				console.error(`problem with request: ${e.message}`);
				reject(e);
			});
		});

		request.on('error', (e) => {	
  			reject(e);
		});

		request.write(query);
		request.end();

	});
}


async function buildJsonAccountsList(data){
	return new Promise(async (resolve, reject) =>{
		try{
			let output = await csv().fromString(data);
			output.map(account => {
				//Set new tags
				//top_partition_label second_partition_label
				account.partitions = {
					top_partition: account.top_partition_label,
					second_partition: account.second_partition_label
				}
				account.amount = parseparseFloat(account.amount);
				account.last_amount = parseparseFloat(account.last_amount);
				account.top_level = account.top_partition_label;
				//remove old ones
				delete account.top_partition_label;
				delete account.second_partition_label;
			});
			resolve(output);
			
		}catch (e){
			reject(e);
		}
	});
}

async function buildJsonAccount(data){
	return new Promise(async (resolve, reject) =>{
		try{
			let json, output, singleCds, put;

			json = await csv().fromString(data);
			output = json[0];

			output.past_values= {};
			output.partitions = {};
			output.cds = [];

			json.map((account) => {
				output.past_values[account.year] = parseparseFloat(account.history_amount);
				singleCds = {
					name: account.fact_label,
					amount: parseparseFloat(account.fact_amount),
				}
				put = containsObject(singleCds, output.cds);
				if(!put) {
					output.cds.push(singleCds);	
    			}
			});

			output.partitions = {
				top_partition: output.top_partition_label,
				second_partition: output.second_partition_label
			}
			output.amount = parseparseFloat(output.amount);
			output.last_amount = parseparseFloat(output.last_amount);
			output.top_level =  output.top_partition_label;
			
			//remove old ones
			delete output.history_amount;
			delete output.year;
			delete output.top_partition_label;
			delete output.second_partition_label;

			//remove capitoli di spesa
			delete output.fact_uri;
			delete output.fact_label;
			delete output.fact_amount;


			resolve(output);
		
		}catch (e){
			reject(e);
		}	
	});
}

function containsObject(obj, list) {
	for (let i = 0; i < list.length; i++) {
		if(list[i].name.localeCompare(obj.name) == 0){
			return true;
		}
	}
    return false;
}

/**
	* @group must be one of the const values @topPartition @secondPartition
*/
async function buildJsonFilter(data, group){
	return new Promise(async (resolve, reject) =>{
		try{
			console.log(data);
			let output, result = await csv().fromString(data);
			
			output = {};
			result.map(d => {
				output[d[group]] = d.amount;
			})

			resolve(output);
			
		}catch (e){
			reject(e);
		}
	});
}
