<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Person;
use App\Ticket;
use App\Http\Requests\TransactionRequest as StoreRequest;
use App\Http\Requests\TransactionRequest as UpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;

class TransactionCrudController extends CustomCrudController {

    public function setUp() {

        $this->crud->setModel(Transaction::class);
        $this->crud->setRoute("raconsole/transaction");
        $this->crud->setEntityNameStrings('transaction', 'transactions/Tickets');

        $this->crud->addTotal([
            'aggregate' => 'count',
            'label' => 'Transactions',
        ]);

        $this->crud->addTotal([
            'aggregate' => 'sum',
            'name' => 'amount',
            'label' => 'Sum',
            'type' => 'model_function',
            'function_name' => 'getSumView',
        ]);

        $this->crud->addTotal([
            'aggregate' => 'sum',
            'name' => 'commission',
            'label' => 'Commission',
            'type' => 'model_function',
            'function_name' => 'getSumView',
        ]);

		//$this->crud->setFromDb();

        $this->crud->addColumn([
            'label' => 'Request ID',
            'exists' => 'extra',
            'type' => 'model_function',
            'function_name' => 'getRequestID',
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
            'type' => 'model_function',
            'function_name' => 'getIP',
        ]);

        /*$this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'ticket_status',
            'label'=> 'Sold Status'
        ],
            [
                1 => 'Not Payed',
               -1 => 'Canceled',
                2 => 'Hold',
                3 => 'Success',
            ],
            function( $value ) {
                if( !empty($value) ){
                    $this->value = $value;
                    $this->crud->addClause('whereHas', 'tickets', function( $query ) {
                        $query->where('tickets.status', $this->value );
                    });
                }
            });*/

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
                    $this->crud->addClause( 'where', 'transactions.created_at', '>=', $value );

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
                    $this->crud->addClause( 'where', 'transactions.created_at', '<', date('Y-m-d', strtotime($value . ' + 1 day')));
            });

        $this->crud->addFilter([
            'type' => 'o_dropdown',
            'name' => 'status',
            'label'=> 'Payment Status'
        ],
            [
                1 => 'Not Payed',
                -1 => 'Canceled',
                3 => 'Success',
                18 => 'Reversed',
            ],
            function( $value ) {
                if (!empty($value))
                    $this->crud->addClause('where', 'transactions.status', $value);
                else
                    $this->crud->addClause('where', 'transactions.status', '<', Transaction::$notfinished);

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
                        $query->where('tickets.request_id', $this->value );
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
                    $this->crud->addClause('where', 'transactions.checkout_id', $value);
            });

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'passenger',
            'label'=> 'Pass/Email/Mob/ID'
        ],
            false,
            function($value) {
                if( !empty($value) ){
                    $this->value = str_replace("  ", " ", trim($value));


                    if(filter_var( $value, FILTER_VALIDATE_EMAIL )) {
                        $this->crud->addClause('where', 'transactions.email', $value);
                    }elseif( is_numeric($value) && strlen($value) < 11 ){
                        $this->crud->addClause('where', 'transactions.mobile', $value);
                    }
                    else {

                        $this->crud->addClause('whereHas', 'tickets', function( $query ) {

                            $query->leftjoin('persons', 'persons.ticket_id', '=', 'tickets.id');

                            $passenger = explode(" ", $this->value);

                            if( !isset($passenger[1]) ){
                                $query->where('persons.name', 'like', '%' . trim($passenger[0]) . '%' );
                                $query->orWhere('persons.surname', 'like', '%' . trim($passenger[0]) . '%' );
                                $query->orWhere('persons.idnumber', 'like', '%' . trim($passenger[0]) . '%' );
                            }else{
                                $query->where('persons.name', 'like', '%' . trim($passenger[0]) . '%' );
                                $query->orWhere('persons.surname', 'like', '%' . trim($passenger[1]) . '%' );
                            }
                        });

                    }

                }
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

        // ------ DATATABLE AJAX RELOAD BUTTON
        $this->crud->enableAjaxReload();

        // ------ DATATABLE SERVER SIDE EXPORT BUTTON
        $this->crud->enableAjaxExport();

        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });

        // $this->crud->groupBy();
        // $this->crud->limit();
    }

    /**
     * Display all rows in the database for this entity.
     *
     * @return Response
     */
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
        return view('vendor.backpack.crud.details.transaction_details_row', [
            'transaction' => Transaction::find($id)
        ]);
    }
}
