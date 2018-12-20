# Smart Data Management Platform

This project contains all needed for setting up and update a knowledge base ready to be used by the inps.g0v.it application.

The knowledge base is built around the [g0v application profile](https://github.com/g0v-it/ontologies/tree/master/fr-ap) and  [LODMAP Bubble Graph Ontology](https://github.com/linkeddatacenter/LODMAP-ontologies/tree/master/v1/bgo):

- the g0v application profile is used to describe the semantic of a financial report
- the Bubble Graph Ontology is used to describe the graphical objects that reprensent financial report components

The data ingestion process is managed by the [LinkedData.Center SDaaS platform](https://linkeddata.center/p/sdaas) (community edition).


## knowledge base build process:

- edit local rdf files in the *data* directory providing static metadata (titles, taxonomies, welcome links, etc. etc.)
- develop the *gateways* for transforming raw data about financial report in linked data according the g0v application profile. See [gateways doc.](gateways/README.md)
- write *axioms* and rules to cleanup financial data.
- write axioms and rules to link financial data to the graphical objects.
- edit the *build script* that drives the whole data ingestion process.
- run sdaas

### debugging the build script

the test of the build script requires the sdaas-ce container.

```
docker run --rm -ti -v $PWD/gateways:/app composer install --no-dev
docker run -d -p 9999:8080 -v $PWD/.:/workspace --name kb linkeddatacenter/sdaas-ce
docker exec -ti kb bash
apk --no-cache add php7-iconv
sdaas --debug -f build.sdaas --reboot
exit
docker rm -f kb
```

logs info and debug traces will be created in .cache directory

Access the admin workbench pointing browser to http://localhost:9999/sdaas

 
### publishing  the knowledge base

You can pack data and services with :

```
docker build . -t sdaas
docker run -d -p 8889:8080 --name datastore sdaas
```

The resulting container will provide a readonly distribution of the whole knowlede base in a stand-alone graph database with sparql interface.


## Directory structure

- the **build.sdaas** file is a script to populate the knowledge base from scratch. It requires sdaas platform community edition 2.0+
- the **axioms** directory contains rules to be processed during reasoning windows.
- the **data** directory contains local data files
- the **data/src** directory contains some helper that were used to produce some ttl files.
- the **gateways** directory contains the code to transform raw data in linked data
- the **.cache** temporary directory that contains logs and debugging info. Not saved in repo.


## Credits and license

- the dockerfile reuse [Docker Blazegraph](https://github.com/lyrasis/docker-blazegraph)
- the sdaas platform is derived from [LinkedData.Center SDaas Product](https://it.linkeddata.center/p/sdaas) and licensed with CC-by-nd-nc by LinkedData.Center to g0v community
