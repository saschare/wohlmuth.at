set session group_concat_max_len = 10000;

select
	concat(
		'<constraint name="', 
		fk.constraint_name, 
		'" table="',
		lower(substr(fk.table_name from 5)),
		'" column="',
		lower(fk.column_name),
		'" reftable="',
		lower(substr(fk.referenced_table_name from 5)),
		'" refcolumn="',
		lower(fk.referenced_column_name),
		'" onupdate="',
		lower(rc.update_rule),
		'" ondelete="',
		lower(rc.delete_rule),
		'" />'
		) constr
from information_schema.key_column_usage fk
left join information_schema.referential_constraints rc on fk.constraint_name = rc.constraint_name and fk.table_schema = rc.constraint_schema
where 
    fk.table_schema = 'dev.aitsu.local'
    and fk.constraint_name != 'PRIMARY'
    and fk.referenced_table_name is not null
order by
    fk.table_name asc,
    fk.referenced_table_name asc,
    fk.referenced_column_name asc