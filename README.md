![copernicani](https://copernicani.it/wp-content/uploads/cropped-logo_orizzontale_trasparente-1-e1525161268864.png)

# g0v data-inps

A simple smart data management platform to feed the http://inps.g0v.it/ web application. 

This project aims to create a general smart data management platform to feed a budget visualization application based on W3C Semantic Web standards.

**Reference implementation:**

- **api endpoint**: https://data.inps.g0v.it/ldp
- **SPARQL endpoint**: https://data.budget.g0v.it/sparql


## Development

The project contains the two logical components:

- **sdaas** (smart data as a service):  the data management platform core providing a RDF store, a [SPARQL endpoint](https://www.w3.org/TR/sparql11-overview), a data ingestion engine, a set of gateways to transform raw data in linked data and a build script that populates the RDF store. See files and docs in [sdaas directory](sdaas/README.md)
- a set of **apis** that query the SPARQL endpoint and produce json data with a schema suitable to be used with the [BubbleGraph Component](). See files and docs in [apis directory](apis/README.md)

This picture shows the components interactions:

![architecture](doc/architecture.png)


The platform deploy requires a stack of some services (e.g. docker containers):

![stack](doc/stack.png)

The platform is shipped with a [Docker](https://docker.com) setup that makes it easy to get a containerized development
environment up and running. If you do not already have Docker on your computer, [it's the right time to install it](https://docs.docker.com/install/).

clone this project, open a terminal, and navigate to the directory of this repository. Copy the env.dist file in .env (`cp env.dist .env`), edit .env file according your needs. Do not save the .env file in the repository.

To start all services using [Docker Compose](https://docs.docker.com/compose/) type: 

```
docker-compose up
```

This starts locally the following services:


| Name        | Description                                                   | Port(s) 
| ----------- | ------------------------------------------------------------- | ------- 
| sdaas       | a server that manages the datastore and the ingestion engine  | 29311    
| api         | a server that manages the web-budget api                      | 29312 
| lodview     | a LODVIEW server to navigate linked data                      | 29319  


Try http://localhost:29311/sdaas to access blazegraph workbench
Try http://localhost:29312/ to test api endpoint
Try http://localhost:29314/welcome to navigate linked data with a configured lodview server


The first time you start the containers, Docker downloads and builds images for you. It will take some time, but don't worry
this is done only once. Starting servers will then be lightning fast



### Production deployment 

For production deployment a reverse proxy server, with caching capability is required. 

The reverse proxy should provide following public points:

- **/** redirects to the project home page (i.e. this readme file)
- **/ldp/{*} *** redirects to api container 
- **/sparql** redirects to sparql endpoint of sdaas container

The revers proxy server must also manage SSL protocol and certificates.

## Support

For answers you may not find in here or in the Wiki, avoid posting issues. Feel free to ask for support on the [Slack](https://copernicani.slack.com/) general room. Make sure to mention **@enrico** so he is notified


## Credits

- data extracted from [INPS Open Data portal](https://www.inps.it/nuovoportaleinps/default.aspx?iIDLink=103) with [IODL](http://www.dati.gov.it/iodl/2.0/) open license
- the RDF datastore and the SPARQL endpoint is based on the [Blazegraph community edition](https://www.blazegraph.com/)
- the Smart Data Management Platform was developed by [Enrico Fagnoni](https://github.com/ecow) using the [SDaaS platform by LinkedData.Center](http://LinkedData.Center/)
- the [g0v fr-ap application profile ](https://github.com/g0v-it/ontologies/tree/master/fr-ap) and the  [LODMAP Bubble Graph Ontology](https://github.com/linkeddatacenter/LODMAP-ontologies/tree/master/BGO) was developed by Enrico Fagnoni @ LinkedData.Center
- API server was developed by [Yassine Ouahidi](https://github.com/YassineOuahidi)  @ LinkedData.Center and DataChef.Cloud


Thanks to all project contributors, to the [Copernicani community](https://copernicani.it/) and to the [g0v asia community](http://g0v.asia) for ideas and support.


## License


The MIT License (MIT). Please see [License File](LICENSE) for more information.

