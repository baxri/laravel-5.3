<?php

namespace App\Http\Controllers\Admin;

use App\Models\PayoutTransaction;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\PayoutTransactionRequest as StoreRequest;
use App\Http\Requests\PayoutTransactionRequest as UpdateRequest;
use Carbon\Carbon;
use App\Backpack\Crud\AjaxTable;
use App\Backpack\Crud\MyCrudPanel;

class PayoutTransactionCrudController extends CrudController {

    use AjaxTable;

    public $crud;

    public function __construct()
    {
        parent::__construct();

        $this->crud = new MyCrudPanel();
    }

	public function setUp() {

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
        $this->crud->setModel( PayoutTransaction::class );
        $this->crud->setRoute("raconsole/payout");
        $this->crud->setEntityNameStrings('payouttransaction', 'Payout Transaction/Banks');

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/

        $this->crud->addColumn([
            'label' => 'Payout ID',
            'name' => 'id',
        ]);

        $this->crud->addColumn([
            'label' => 'UniPAY HASH',
            'name' => 'payout_hash_id',
        ]);

        $this->crud->addColumn([
            'label' => 'Amount',
            'name' => 'amount',
            'type' => 'model_function',
            'function_name' => 'getAmountView',
        ]);

        $this->crud->addColumn([
            'label' => 'Name',
            'name' => 'name',
        ]);

        $this->crud->addColumn([
            'label' => 'Surname',
            'name' => 'surname',
        ]);

        $this->crud->addColumn([
            'label' => 'idnumber',
            'name' => 'idnumber',
        ]);

        $this->crud->addColumn([
            'label' => 'Birth Date',
            'name' => 'birth_date',
        ]);

        $this->crud->addColumn([
            'label' => 'Bank',
            'name' => 'bank',
        ]);

        $this->crud->addColumn([
            'label' => 'Iban',
            'name' => 'iban',
        ]);

        $this->crud->addColumn([
            'label' => 'Status',
            'name' => 'status',
            'type' => 'model_function',
            'function_name' => 'getStatusView',
        ]);

        $this->crud->addColumn([
            'label' => 'Created At',
            'name' => 'created_at',
        ]);

        $this->crud->addColumn([
            'label' => 'Updated_At',
            'name' => 'updated_at',
        ]);

        $this->crud->addColumn([
            'label' => 'IP',
            'name' => 'ip',
        ]);

        $this->crud->addColumn([
            'label' => 'Log',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'getLogIcon',
        ]);

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status',
            'label'=> 'Status'
        ],
            [
                -1 => 'Canceled',
                1 => 'Process',
                2 => 'Hold',
                3 => 'Success',
            ],
            function( $value ) {
                if($value)
                    $this->crud->addClause('where', 'status', $value);
            });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'payout_hash_id',
            'label'=> 'Unipay HASH'
        ],
            false,
            function($value) {
                if( !empty($value) ){
                    $this->crud->addClause('where', 'payout_hash_id', $value);
                }
            });

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
                    $this->crud->addClause( 'where', 'updated_at', '>=', $value );

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
                    $this->crud->addClause( 'where', 'updated_at', '<', date('Y-m-d', strtotime($value . ' + 1 day')));
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

        $this->crud->removeButton('delete');
        $this->crud->removeButton('create');
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
        //$this->crud->enableAjaxTable();
        
        
        // ------ DATATABLE EXPORT BUTTONS
        // Show export to PDF, CSV, XLS and Print buttons on the table view.
        // Does not work well with AJAX datatables.
         $this->crud->enableExportButtons();

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
        return view('vendor.backpack.crud.details.payout_transaction_details_row', [
            'payout' => PayoutTransaction::find( $id )
        ]);
    }
}
