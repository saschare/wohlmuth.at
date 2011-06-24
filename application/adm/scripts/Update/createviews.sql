create view ait_pubv_article_content as
    select 
        idartlang, `index`, `value`, modified, pubid, `status` 
    from ait_pub_article_content 
    where 
        `status` = 1;
                
create view ait_pubv_art_lang as
    select 
        idartlang, idart, idlang, title, urlname, pagetitle, teasertitle,
        summary, created, lastmodified, online, pubfrom, pubuntil, published,
        redirect, redirect_url, artsort, locked, configsetid, config, pubid, 
        `status`, mainimage 
    from ait_pub_art_lang 
    where 
        `status` = 1;
                
create view ait_pubv_art_meta as
    select 
        idartlang, description, author, keywords, `date`, expires, robots,
        dctitle, dccreator, dcsubject, dcpublisher, dccontributor, dcdate,
        dctype, dcformat, dcidentifier, dcsource, dclanguage, dcrelation,
        cdcoverage, dcrights, pubid, `status` 
    from ait_pub_art_meta 
    where 
        `status` = 1;

create view ait_pubv_aitsu_article_property as
    select 
        propertyid, idartlang, textvalue, floatvalue, datevalue, pubid, `status` 
    from ait_pub_aitsu_article_property 
    where 
        `status` = 1;