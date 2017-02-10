<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Note extends Model
{
	use CrudTrait;

     /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

	protected $table = 'notes';
	protected $primaryKey = 'id';
	// public $timestamps = false;
	// protected $guarded = ['id'];
    protected $fillable = ['title', 'alias', 'content', 'lang', 'published', 'type', 'parent_id', 'lft', 'rgt', 'depth'];
	// protected $fillable = [];
	// protected $hidden = [];
    // protected $dates = [];




    public function getPublishedView(){
        if( $this->published == 1 )
            return 'Published';
        else
            return 'Unpublished';
    }
}
