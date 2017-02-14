<?php

namespace App\Http\Controllers\Admin;

use App\Models\Note;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\NoteRequest as StoreRequest;
use App\Http\Requests\NoteRequest as UpdateRequest;
use Illuminate\Support\Facades\App;

class NoteCrudController extends CrudController {

	public function setUp() {

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
        $this->crud->setModel("App\Models\Note");
        $this->crud->setRoute("raconsole/note");
        $this->crud->setEntityNameStrings('message', 'Messages');

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/

		//$this->crud->setFromDb();

        $this->crud->addColumn([
            'label' => 'Title',
            'name' => 'title',
        ]);

        $this->crud->addColumn([
            'label' => 'Content',
            'name' => 'content',
        ]);

        $this->crud->addColumn([
            'label' => 'Alias/Slug',
            'name' => 'alias',
        ]);

        $this->crud->addColumn([
            'label' => 'Published',
            'name' => 'published',
            'type' => 'model_function',
            'function_name' => 'getPublishedView',
        ]);

        $this->crud->addField([ // Text
            'name' => 'title',
            'label' => "Title",
            'type' => 'text',
        ]);

        $this->crud->addField([ // Text
            'name' => 'alias',
            'label' => "Alias/Slug",
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'label' => 'Language',
            'name' => 'lang',
        ]);

        $this->crud->addField([ // Text
            'name' => 'content',
            'label' => "Content",
            'type' => 'ckeditor',
            'height' => 500
        ]);

        $this->crud->addField([ // Text
            'name' => 'published',
            'label' => "Published",
            'type' => 'select_from_array',
            'options' => [ 0 => 'Unpublished', 1 => 'Published' ],
            'allows_null' => false
        ]);

        $this->crud->addField([ // Text
            'name' => 'type',
            'label' => "Message Type",
            'type' => 'select_from_array',
            'options' => [
                1 => 'Warning',
                2 => 'Notice',
                3 => 'Error/Problem',
                4 => 'Simple text',
            ],
            'allows_null' => false
        ]);

        $this->crud->addField([ // Text
            'name' => 'lang',
            'label' => "language",
            'type' => 'select_from_array',
            'options' => [ 'en' => 'en', 'ka' => 'ka' ],
            'allows_null' => false
        ]);

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'lang',
            'label'=> 'Language'
        ],
            [
                'ka' => 'ka',
                'en' => 'en',
            ],
            function( $value ) {
                $this->crud->addClause( 'where', 'lang', $value );
            });

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'published',
            'label'=> 'Published'
        ],
            [
                1 => 'Unpublished',
                2 => 'Published',
            ],
            function( $value ) {
                $this->crud->addClause( 'where', 'published', ($value-1) );
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

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
         $this->crud->enableReorder('content', 1);
         $this->crud->allowAccess('reorder');
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
         //$this->crud->enableAjaxTable();
        
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
         $this->crud->orderBy('lft', 'asc');
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

	public function notes(){
        return response()->ok([
            'messages' => Note::where([
                'published' => 1,
                'lang' => App::getLocale(),
            ])->orderBy('lft', 'asc')->get()
        ]);
    }
}
