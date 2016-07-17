---
view::extends: _includes.blog_post_base
view::yields: post_body
post::title: PHP Solarium Multi-Select facets with SOLR
post::brief: How to use multiple selects with SOLR for a guided navigation. Handy for filtering with multiple selections for a given list.
pageTitle: SOLR - Using Multi-Select facets in PHP Solarium - 
-----------------------------------------------------------

This blog will be my first in a series about [SOLR](http://lucene.apache.org/solr/) and the popular [PHP Solarium](https://github.com/solariumphp/solarium) library created by Bas de Nooijer.
Solarium is by far the best library in PHP for querying SOLR. We had some issues with advanced faceting for finding Jobs in one of our projects and I would like to share that with you.

Lets start with some basics and information about faceting. Faceting is the arrangement of search results into categories based on indexed terms. Searchers are presented with the indexed terms, along with numerical counts of how many matching documents were found were each term. Faceting makes it easy for users to explore search results, narrowing in on exactly the results they are looking for.

One of our projects was about building a search for a jobboard and one of our facets is the degree field. A job applicant can have multiple degrees, or is looking for a job which can be done at multiple degree levels.

**facet=true&facet.field=degree**  
== Degrees ==  
[ ] WO (5)  
[ ] HBO (24)  
[ ] MBO (16)  
[ ] VMBO (8)  
When someone selects “MBO” as an extra criteria, facet will return no results for every other element in the category if it is a singular value field. In this case it is a multiValue field which can be more results than zero.

**facet=true&facet.field=degree&fq=degree:MBO**  
== Degrees ==  
[ ] WO (0)  
[ ] HBO (5)  
[X] MBO (16)  
[ ] VMBO (0)  
Executing this query on Solr is as easy as adding the fq GET parameter. This works great if you only want to search for one value in a category.
That was not what we wanted to achieve. We want to search for multiple values in a category as a Job can be done at multiple degree levels.

Sometimes you want to limit these degrees based on a region or provence like in the Netherlands. And only show the jobs that are located in that region.
That can simply be done with an extra GET parameter of 'facet.mincount=1' to the SOLR query. To hide the missing values of that region.

**facet=true&facet.field=degree&facet.mincount=1&fq=provence:Groningen**  
== Degree ==  
[ ] HBO (24)  
[ ] MBO (16)  
At this time we only see the job degrees within that provence. However if we select 'MBO' we see the issue.

**facet=true&facet.field=degree&facet.mincount=1&fq=provence:Groningen&fq=degree:MBO**  
== Degree ==  
[x] MBO (16)  
The selection for “HBO” is gone. The reason behind this is that facet.mincount=1 filters out all elements with zero results. In this case the Provence Groningen does not have a Job at multiple degree levels.

SOLR provides a solution for this kind of faceting if you like to select 'HBO' as well. This was the problem we had for filtering out the data we would like to see. In general you can set tags for searching and faceting data. 
The solution to our problem is written in the following example.

**facet=true&facet.field={!ex=exclude}degree&facet.mincount=1&fq={!tag:include}provence:Groningen&fq={!tag=exclude}degree:MBO**  
== Degree ==  
[ ] HBO (24)  
[X] MBO (16)  
When using this query on Solr it will return 16 results, but leave the facet model untouched for the filter query on the degree itself.
In this query we used the tags ‘include’ and ‘exclude’. Include defines the base criteria that always should be filtered. Exclude defines the criteria that the user has added selecting facets in the guided navigation. 
In this case you can select both degrees to search for jobs.

TL;DR
-----
How can we build a facet query than can select multiple values from a facet field with PHP Solarium? These kind of query's are very easy thanks to the great work of Bas de Nooijer's library  
If we use the last query as an example then all the code we need is this: 

```php
$client = new Solarium_Client($config);
$query = $client->createSelect();
$query->addFilterQuery(array('key'=>'provence', 'query'=>'provence:Groningen', 'tag'=>'include'));
$query->addFilterQuery(array('key'=>'degree', 'query'=>'degree:MBO', 'tag'=>'exclude'));
$facets = $query->getFacetSet();
$facets->createFacetField(array('field'=>'degree', 'exclude'=>'exclude'));
$client->select($query);
```

That is all it takes to build a SOLR 'OR' based faceting result instead of the default 'AND' based faceting result.

In the next blog I will show you a way to implement this within a Laravel project.