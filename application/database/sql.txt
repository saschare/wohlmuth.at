set session group_concat_max_len = 10000;

select distinct
	concat(
		'<field name="', 
		col.column_name, 
		'" table="',
		lower(substr(col.table_name from 5)),
		'" engine="',
		tab.engine,
		'" type="',
		lower(col.column_type),
		'" default="', 
		if(col.column_default is null, 'null', col.column_default), 
		'" nullable="',
		lower(col.is_nullable),
		'" key="',
		lower(col.column_key),
		'" extra="',
		col.extra,
		'" />'
		) field
from information_schema.columns col
left join information_schema.tables tab on col.table_name = tab.table_name
where
	col.table_schema = 'dev.aitsu.local'
	and col.table_name like 'ait_%'
order by
	col.table_name asc,
	col.ordinal_position asc;