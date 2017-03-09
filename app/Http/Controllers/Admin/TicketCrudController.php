<?php

namespace App\Http\Controllers\Admin;

use App\Ticket;
use App\Http\Requests\TicketRequest as StoreRequest;
use App\Http\Requests\TicketRequest as UpdateRequest;
use Carbon\Carbon;

class TicketCrudController extends CustomCrudController {

	public function setUp() {

        $this->crud->setModel(Ticket::class);
        $this->crud->setRoute("raconsole/ticket");
        $this->crud->setEntityNameStrings('ticket', 'tickets');

        $this->crud->addTotal([
            'aggregate' => 'count',
            'label' => 'Tickets',
        ]);
        $this->crud->addTotal([
            'aggregate' => 'sum',
            'name' => 'amount_from_api',
            'label' => 'Sum',
            'type' => 'model_function',
            'function_name' => 'getSumView',
        ]);
        $this->crud->addColumn([
            'label' => 'Type',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'getTicketType',
        ]);

        $this->crud->addColumn([
            'label' => 'Request ID',
            'name' => 'request_id',
        ]);
        $this->crud->addColumn([
            'label' => 'Amount',
            'name' => 'amount_from_api',
            'type' => 'model_function',
            'function_name' => 'getAmountView',
        ]);
        $this->crud->addColumn([
            'label' => 'Departure',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'gePersonstatusView',
        ]);
        $this->crud->addColumn([
            'label' => 'Source Station',
            'name' => 'source_station',
        ]);
        $this->crud->addColumn([
            'label' => 'Destinaton Station',
            'name' => 'destination_station',
        ]);
        $this->crud->addColumn([
            'label' => 'Train Class',
            'name' => 'train_class',
        ]);
        $this->crud->addColumn([
            'label' => 'Vagon Type',
            'name' => 'vagon_type',
        ]);
        $this->crud->addColumn([
            'label' => 'Vagon Class',
            'name' => 'vagon_class',
        ]);
        $this->crud->addColumn([
            'label' => 'Created At',
            'name' => 'created_at',
            'type' => 'model_function',
            'function_name' => 'getCreatedAtView',
        ]);
        $this->crud->addColumn([
            'label' => 'Updated At',
            'name' => 'updated_at',
            'type' => 'model_function',
            'function_name' => 'getUpdateedAtView',
        ]);
        $this->crud->addColumn([
            'label' => 'Sold Status',
            'name' => 'status',
            'type' => 'model_function',
            'function_name' => 'getStatusView',
        ]);

        $this->crud->query->select('tickets.*');
        $this->crud->query->leftjoin('persons', 'tickets.id', '=', 'persons.ticket_id');

        $this->crud->addFilter([
            'type' => 'date',
            'name' => 'date-from',
            'label'=> 'Date From',
            'default_value'=> Carbon::today()->toDateString(),
        ],
            false,
            function($value) {
                if( empty($value) )
                    $value = Carbon::today()->toDateString();

                if($value)
                    $this->crud->addClause( 'where', 'tickets.created_at', '>=', $value );

            });

        $this->crud->addFilter([
            'type' => 'date',
            'name' => 'date-to',
            'label'=> 'Date To',
            'default_value'=> Carbon::today()->toDateString(),
        ],
            false,
            function($value) {
                if( empty($value) )
                    $value = Carbon::today()->toDateString();

                if($value)
                    $this->crud->addClause( 'where', 'tickets.created_at', '<', date('Y-m-d', strtotime($value . ' + 1 day')));
            });

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status',
            'label'=> 'Sold Status'
        ],
            [
                -1 => 'Canceled',
                2 => 'Hold',
                3 => 'Success',
            ],
            function( $value ) {
                if( !empty($value) )
                    $this->crud->addClause('where', 'tickets.status', $value);
                else
                    $this->crud->addClause('where', 'tickets.status', Ticket::$success);
            });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'request_id',
            'label'=> 'Request ID'
        ],
            false,
            function($value) {
                if( !empty($value) )
                    $this->crud->addClause('where', 'tickets.request_id', $value);
            });


		// ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        // ------ CRUD COLUMNS
        // $this->crud->addColumn(); // add a single column, at the end of the stack
        // $this->crud->addColumns(); // add multiple columns, at the end of the stack
        // $this->crud->removeColumn('column_name'); // remove a column from the stack
        // $this->crud->removeColumns(['column_name_1', 'column_name_2']); // remove an array of columns from the stack
        // $this->crud->setColumnDetails('column_name', ['attribute' => 'value']); // adjusts the properties of the passed in column (by name)
        // $this->crud->setColumnsDetails(['column_1', 'column_2'], ['attribute' => 'value']);
        
        // ------ CRUD BUTTONS
        // possible positions: 'beginning' and 'end'; defaults to 'beginning' for the 'line' stack, 'end' for the others;
        // $this->crud->addButton($stack, $name, $type, $content, $position); // add a button; possible types are: view, model_function
        // $this->crud->addButtonFromModelFunction($stack, $name, $model_function_name, $position); // add a button whose HTML is returned by a method in the CRUD model
        // $this->crud->addButtonFromView($stack, $name, $view, $position); // add a button whose HTML is in a view placed at resources\views\vendor\backpack\crud\buttons
        // $this->crud->removeButton($name);
        // $this->crud->removeButtonFromStack($name, $stack);

        $this->crud->removeButton('create');
        $this->crud->removeButton('delete');
        $this->crud->removeButton('update');

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
         $this->crud->enableDetailsRow();
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('details_row');
        // NOTE: you also need to do overwrite the showDetailsRow($id) method in your EntityCrudController to show whatever you'd like in the details row OR overwrite the views/backpack/crud/details_row.blade.php

        // ------ REVISIONS
        // You also need to use \Venturecraft\Revisionable\RevisionableTrait;
        // Please check out: https://laravel-backpack.readme.io/docs/crud#revisions
        // $this->crud->allowAccess('revisions');

        // ------ AJAX TABLE VIEW
        // Please note the drawbacks of this though:
        // - 1-n and n-n columns are not searchable
        // - date and datetime columns won't be sortable anymore
         $this->crud->enableAjaxTable();

        // ------ Disable Ordering On Columns
        $this->crud->disableOrderingOnExtraColumns();

        // ------ DATATABLE EXPORT BUTTONS
        // Show export to PDF, CSV, XLS and Print buttons on the table view.
        // Does not work well with AJAX datatables.
         $this->crud->enableExportButtons();

        // ------ DATATABLE AJAX RELOAD BUTTON
        $this->crud->enableAjaxReload();

        // ------ DATATABLE SERVER SIDE EXPORT BUTTON
        $this->crud->enableAjaxExport();

        // ------ DATATABLE DEFAULT ORDERING
        $this->crud->setDefaultOrdering( 'id', 'desc' );


        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        // $this->crud->orderBy();
        // $this->crud->groupBy();
        // $this->crud->limit();
    }

	public function store(StoreRequest $request)
	{
		return parent::storeCrud();
	}

	public function update(UpdateRequest $request)
	{
		return parent::updateCrud();
	}

    public function showDetailsRow($id)
    {
        return view('vendor.backpack.crud.details.ticket_details_row', [
            'ticket' => Ticket::find($id)
        ]);
    }
}
