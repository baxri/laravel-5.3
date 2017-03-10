<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\NewsRequest as StoreRequest;
use App\Http\Requests\NewsRequest as UpdateRequest;
use Illuminate\Support\Facades\App;
use Mockery\Exception;

class NewsCrudController extends CrudController {

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
            if( $value )
                $this->crud->addClause( 'where', 'lang', $value );
            });


        //$this->crud->enableAjaxTable();

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
