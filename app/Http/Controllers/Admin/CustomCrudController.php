<?php

namespace App\Http\Controllers\Admin;

use App\Backpack\Crud\AjaxTable;
use App\Backpack\Crud\MyCrudPanel;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class CustomCrudController extends CrudController
{
    use AjaxTable;

    public $crud;

    public function __construct()
    {
        parent::__construct();

        $this->crud = new MyCrudPanel();
    }

    public function index()
    {
        $this->crud->hasAccessOrFail('list');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = ucfirst($this->crud->entity_name_plural);

        // get all entries if AJAX is not enabled
        if (! $this->data['crud']->ajaxTable()) {
            $this->data['entries'] = $this->data['crud']->getEntries();
        }

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        // $this->crud->getListView() returns 'list' by default, or 'list_ajax' if ajax was enabled
        return view('vendor.backpack.crud.list', $this->data);
    }
}
