set session group_concat_max_len = 10000;

select 
	concat(
		'<index table="',
		lower(substr(ind.table_name from 5)),
		'" name="',
		ind.index_name,
		'" columns="',
		group_concat(distinct ind.column_name order by ind.seq_in_index),
		'" type="',
		ind.index_type,
		'" unique="',
		if(ind.non_unique = 'NO', 'true', 'false'),
		'" />'
		) node
from information_schema.statistics ind
where
	ind.table_schema = 'dev.aitsu.local'
	and ind.table_name like 'ait_%'
group by
    ind.index_type,
    ind.index_name,
    ind.table_name
order by
	ind.table_name asc;