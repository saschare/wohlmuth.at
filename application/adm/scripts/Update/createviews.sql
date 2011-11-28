create view ait_pubv_article_content as
    select 
        *
    from ait_pub_article_content 
    where 
        `status` = 1;
                
create view ait_pubv_art_lang as
    select 
        *
    from ait_pub_art_lang 
    where 
        `status` = 1;
                
create view ait_pubv_art_meta as
    select 
        * 
    from ait_pub_art_meta 
    where 
        `status` = 1;

create view ait_pubv_aitsu_article_property as
    select 
        * 
    from ait_pub_aitsu_article_property 
    where 
        `status` = 1;