<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\NewsRequest as StoreRequest;
use App\Http\Requests\NewsRequest as UpdateRequest;
use Illuminate\Support\Facades\App;
use Mockery\Exception;

class NewsCrudController extends CustomCrudController {

	public function setUp() {

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
        $this->crud->setModel("App\Models\News");
        $this->crud->setRoute("raconsole/news");
        $this->crud->setEntityNameStrings('Article', 'Articles');

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/

        $this->crud->addColumn([
            'label' => 'ID',
            'name' => 'id',
        ]);

		//$this->crud->setFromDb();
        $this->crud->addColumn([
            'label' => 'Title',
            'name' => 'title',
        ]);

        $this->crud->addColumn([
            'label' => 'Language',
            'name' => 'lang',
        ]);

        $this->crud->addColumn([
            'label' => 'Alias/Slug',
            'name' => 'alias',
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

        $this->crud->addField([ // Text
            'name' => 'content',
            'label' => "Content",
            'type' => 'ckeditor',
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

	public function article($alias){

	    try{

	        $article = News::where([
	            'alias' => $alias,
                'lang' => App::getLocale()
            ])->get();

	        if( empty($article[0]->content) ){
	            throw new Exception('ARTICLE_NOT_FOUND');
            }

            return response()->ok( [
                'article' => $article[0]->content
            ] );

        }catch ( Exception $e ){
            return response()->error( $e->getMessage() );
        }

    }
}
