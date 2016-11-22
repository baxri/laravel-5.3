<?php

namespace App\Http\Controllers\Admin;

use App\Models\TransactionLog;
use Backpack\CRUD\app\Http\Controllers\CrudController;

use App\Backpack\Crud\AjaxTable;
use App\Backpack\Crud\MyCrudPanel;
use App\Http\Requests\TransactionLogRequest as StoreRequest;
use App\Http\Requests\TransactionLogRequest as UpdateRequest;

class TransactionLogCrudController extends CrudController {

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
        $this->crud->setModel( TransactionLog::class );
        $this->crud->setRoute("raconsole/transaction-log");
        $this->crud->setEntityNameStrings('transactionlog', 'transaction logs');

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/

        $this->crud->addColumn([
            'label' => 'Transaction ID',
            'name' => 'ticket_id',
        ]);

        $this->crud->addColumn([
            'label' => 'OP',
            'name' => 'op',
        ]);

        $this->crud->addColumn([
            'label' => 'Arguments',
            'name' => 'arguments',
            'type' => 'model_function',
            'function_name' => 'getArgumentsView',
        ]);

        $this->crud->addColumn([
            'label' => 'text',
            'name' => 'text',
            'type' => 'model_function',
            'function_name' => 'getTextView',
        ]);

        $this->crud->addColumn([
            'label' => 'Created At',
            'name' => 'created_at',
        ]);

        $this->crud->addColumn([
            'label' => 'Updated_at',
            'name' => 'updated_at',
        ]);

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'op',
            'label'=> 'OP'
        ],
            [
                'checkout' => 'checkout',
                'callback' => 'callback',
            ],
            function( $value ) {
                if( $value ){
                    $this->crud->addClause('where', 'op', $value);
                }
            });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'transaction_id',
            'label'=> 'Transaction ID'
        ],
            false,
            function($value) {
                $this->crud->addClause('where', 'transaction_id', $value);
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
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
        // $this->crud->enableDetailsRow();
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
        // $this->crud->enableAjaxTable();
        
        
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
}
