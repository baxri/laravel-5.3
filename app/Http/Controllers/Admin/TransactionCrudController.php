<?php

namespace App\Http\Controllers\Admin;


use App\Backpack\Crud\AjaxTable;
use App\Backpack\Crud\MyCrudPanel;
use App\Models\Transaction;
use App\Person;
use App\Ticket;
use Backpack\CRUD\app\Http\Controllers\CrudController;

use App\Http\Requests\TransactionRequest as StoreRequest;
use App\Http\Requests\TransactionRequest as UpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;

class TransactionCrudController extends CrudController {

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
        $this->crud->setModel(Transaction::class);
        $this->crud->setRoute("raconsole/transaction");
        $this->crud->setEntityNameStrings('transaction', 'transactions/Tickets');

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/

		//$this->crud->setFromDb();

        $this->crud->addColumn([
            'label' => 'RAID',
            'name' => 'id',
        ]);

        $this->crud->addColumn([
            'label' => 'UniPAY HASH ID',
            'name' => 'checkout_id',
        ]);

        $this->crud->addColumn([
            'label' => 'Amount',
            'name' => 'amount',
            'type' => 'model_function',
            'function_name' => 'getAmountView',
        ]);

        $this->crud->addColumn([
            'label' => 'Commission',
            'name' => 'commission',
            'type' => 'model_function',
            'function_name' => 'getCommissionView',
        ]);

        $this->crud->addColumn([
            'label' => 'Departure',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'getLeaveTicketStatusView',
        ]);

        $this->crud->addColumn([
            'label' => 'Return',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'getReturnTicketStatusView',
        ]);


        $this->crud->addColumn([
            'label' => 'Email',
            'name' => 'email',
        ]);

        $this->crud->addColumn([
            'label' => 'Mobile',
            'name' => 'mobile',
            'type' => 'model_function',
            'function_name' => 'getMobileView',
        ]);

        $this->crud->addColumn([
            'label' => 'Updated At',
            'name' => 'updated_at',
            'type' => 'model_function',
            'function_name' => 'getUpdateedAtView',
        ]);

        $this->crud->addColumn([
            'label' => 'Payment',
            'name' => 'status',
            'type' => 'model_function',
            'function_name' => 'getStatusView',
        ]);

        $this->crud->addColumn([
            'label' => 'Email D.',
            'name' => 'email_delivery',
            'type' => 'model_function',
            'function_name' => 'emailDeliveryView',
        ]);

        $this->crud->addColumn([
            'label' => 'SMS D.',
            'name' => 'sms_delivery',
            'type' => 'model_function',
            'function_name' => 'smsDeliveryView',
        ]);

        $this->crud->addColumn([
            'label' => 'PAY',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'getLogIcon',
        ]);

        $this->crud->addColumn([
            'label' => 'SMS',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'getSMSLogIcon',
        ]);

        $this->crud->addColumn([
            'label' => 'IP',
            'name' => 'ip',
        ]);

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'ticket_status',
            'label'=> 'Sold Status'
        ],
            [
                -1 => 'Canceled',
                //1 => 'Process',
                2 => 'Hold',
                3 => 'Success',
            ],
            function( $value ) {
                if( !empty($value) ){
                    $this->value = $value;
                    $this->crud->addClause('whereHas', 'tickets', function( $query ) {
                        $query->where('status', $this->value );
                    });
                }
            });


        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status',
            'label'=> 'Payment Status'
        ],
            [
                'not_finished' => 'Not Finished',
                -1 => 'Canceled',
                3 => 'Success',
                18 => 'Reversed',
            ],
            function( $value ) {
                if (!empty($value))
                    $this->crud->addClause('where', 'status', $value);

                if( $value == 'not_finished' )
                    $this->crud->addClause('where', 'status', 0);
            });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'ticket_id',
            'label'=> 'Ticket ID'
        ],
            false,
            function($value) {
                if( !empty($value) ){
                    $this->value = $value;
                    $this->crud->addClause('whereHas', 'tickets', function( $query ) {
                        $query->where('id', $this->value );
                    });
                }
            });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'request_id',
            'label'=> 'Ticket Request ID'
        ],
            false,
            function($value) {
                if( !empty($value) ){
                    $this->value = $value;
                    $this->crud->addClause('whereHas', 'tickets', function( $query ) {
                        $query->where('request_id', $this->value );
                    });
                }
            });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'checkout_id',
            'label'=> 'UniPAY Order Hash'
        ],
            false,
            function($value) {
                if( !empty($value) )
                    $this->crud->addClause('where', 'checkout_id', $value);
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
                    $this->crud->addClause( 'where', 'created_at', '>=', $value );

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
                    $this->crud->addClause( 'where', 'created_at', '<', date('Y-m-d', strtotime($value . ' + 1 day')));
            });

		// ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        $this->crud->addField([ // Text
            'name' => 'index',
            'label' => "Index",
            'type' => 'text',
        ]);

        $this->crud->addField([ // Text
            'name' => 'mobile',
            'label' => "Mobile",
            'type' => 'text',
        ]);

        $this->crud->addField([ // Text
            'name' => 'email',
            'label' => "Email",
            'type' => 'text',
        ]);

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
        $this->crud->addButton('line', 'return_ticket', 'view', 'vendor.backpack.crud.buttons.resend_email');
        $this->crud->addButton('line', 'return_ticket', 'view', 'vendor.backpack.crud.buttons.resend_sms');

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
        // $this->crud->orderBy('id', 'desc');
        // $this->crud->groupBy();
        // $this->crud->limit();
    }

    public function myexport(){

        $data = $this->crud->query->get();

        foreach ($data as $d){
            echo '<pre>';
            print_r($d->toArray());
            echo '</pre>';
        }

        die;
    }

    public function ret( Person $person, Request $request ){
        try{

            $comment = $request->input('comment');

            if( !$person->ret() ){
                throw new Exception('CANNOT_RETURN_TICKET');
            }

            return response()->ok([
                'class' => $person->getStatusClass(),
                'name' => $person->getStatusName(),
            ]);
        }catch( Exception $e ){
            return response()->error( $e->getMessage(), 500, true );
        }
    }

    public function resendEmail( Transaction $transaction ){
        try{

            $transaction->notifyEmail( $throw_exception = true );

            return response()->ok($transaction->toArray());
        }catch( Exception $e ){
            return response()->error( $e->getMessage(), 500, true );
        }
    }

    public function resendSms( Transaction $transaction ){
        try{

            $transaction->notifySMS( $throw_exception = true );

            return response()->ok($transaction->toArray());
        }catch( Exception $e ){
            return response()->error( $e->getMessage(), 500, true );
        }
    }

    public function pdf( Ticket $ticket ){
        return $ticket->toPdf( $download = true );
    }

    public function html( Ticket $ticket ){
        return $ticket->html( $download = true );
    }

    public function sync( Ticket $ticket ){
        try{

            return response()->ok( $ticket->sync() );
        }catch( Exception $e ){
            return response()->error( $e->getMessage(), 500, true );
        }
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
        return $id;

        /*return view('vendor.backpack.crud.details.transaction_details_row', [
            'transaction' => Transaction::find($id)
        ]);*/
    }
}
