<?php


$table = 'datatables_demo';

// Table's primary key
$primaryKey = 'id';


$columns = array(
	array(
		'db' => 'id',
		'dt' => 'DT_RowId',
		'formatter' => function( $d, $row ) {
		
			return 'row_'.$d;
		}
	),
	array( 'db' => 'first_name', 'dt' => 'first_name' ),
	array( 'db' => 'last_name',  'dt' => 'last_name' ),
	array( 'db' => 'position',   'dt' => 'position' ),
	array( 'db' => 'office',     'dt' => 'office' ),
	array(
		'db'        => 'start_date',
		'dt'        => 'start_date',
		'formatter' => function( $d, $row ) {
			return date( 'jS M y', strtotime($d));
		}
	),
	array(
		'db'        => 'salary',
		'dt'        => 'salary',
		'formatter' => function( $d, $row ) {
			return '$'.number_format($d);
		}
	)
);

$sql_details = array(
	'user' => '',
	'pass' => '',
	'db'   => '',
	'host' => ''
);




require( 'ssp.class.php' );

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);

