<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\StationRequest as StoreRequest;
use App\Http\Requests\StationRequest as UpdateRequest;

class StationCrudController extends CustomCrudController {

	public function setUp() {

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
        $this->crud->setModel("App\Models\Station");
        $this->crud->setRoute("raconsole/station");
        $this->crud->setEntityNameStrings('station', 'stations');

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/

        $this->crud->addColumn([
            'label' => 'Ordering',
            'name' => 'ordering',
        ]);

        $this->crud->addColumn([
            'label' => 'Georgian',
            'name' => 'label_ka',
        ]);

        $this->crud->addColumn([
            'label' => 'English',
            'name' => 'label_en',
        ]);

        $this->crud->addColumn([
            'label' => 'Value',
            'name' => 'value',
        ]);

        $this->crud->addColumn([
            'label' => 'FilterCode',
            'name' => 'filtercode',
        ]);

        $this->crud->addColumn([
            'label' => 'Published',
            'name' => 'published',
            'type' => 'model_function',
            'function_name' => 'getPublishedView',
        ]);

		// ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        $this->crud->addField([
            'name' => 'label_ka',
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'label_en',
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name'        => 'published', // the name of the db column
            'label'       => 'Published', // the input label
            'type'        => 'radio',
            'options'     => [ // the key will be stored in the db, the value will be shown as label;
                0 => "UnPublish",
                1 => "Publish"
            ],
            // optional
            'inline'      => true, // show the radios all on the same line?
        ]);


        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'published',
            'label'=> 'Published'
        ],
            [
                1 => 'UnPublished',
                2 => 'Published',
            ],
            function( $value ) {
                if($value == 1 )
                    $this->crud->addClause('where', 'published', 0);

                if($value == 2 )
                    $this->crud->addClause('where', 'published', 1);
            });

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
        //$this->crud->removeButton('update');
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
         $this->crud->enableAjaxTable();

         $this->crud->enableAjaxExport();
        
        // ------ DATATABLE EXPORT BUTTONS
        // Show export to PDF, CSV, XLS and Print buttons on the table view.
        // Does not work well with AJAX datatables.
        // $this->crud->enableExportButtons();

        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
         //$this->crud->orderBy('ordering');
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
