<?php

namespace App\Backpack\Crud;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

trait AjaxTable
{
    /**
     * Respond with the JSON of one or more rows, depending on the POST parameters.
     * @return JSON Array of cells in HTML form.
     */
    public function search()
    {
        $request_type = isset($_GET['request_type']) ? $_GET['request_type'] : 'list';

        if( $request_type == 'total' ){

            /*
             * Retrive total information
             *
             * */

            $totals = $this->crud->getTotals();

            $table_name = $this->crud->model->getTable();

            foreach ( $totals as $key => $total ){

                if( isset( $total['aggregate'] ) && $total['aggregate'] == 'sum'  ){
                    $value = $this->crud->query->sum($table_name.'.'.$total['name']);
                }else{
                    $value = $this->crud->query->count();
                }

                if( isset($total['type']) && isset($total['function_name']) && $total['type'] == 'model_function' ){

                    $function = $total['function_name'];
                    $value = $this->crud->model->$function($value);
                    $totals[$key]['value'] = $value;

                }else{
                    $totals[$key]['value'] = $value;
                }
            }

            return response()->json($totals);


        }elseif( $request_type == 'excel' ){



            $table_name = $this->crud->model->getTable();

            $filename = str_replace("_", " ", ucfirst($table_name));

            $result = $this->crud->query->get();

            Excel::create(str_replace("_", " ", ucfirst($table_name)), function($excel) use ($result) {

                $excel->sheet('Sheet', function($sheet) use ($result) {

                    $data = array();

                    foreach ( $result as $item ){

                        d($item);

                        $data[] = [

                        ];
                    }

                    $sheet->with([
                        [
                            'rt' => 'sdf',
                            'rtt' => 'sdf'
                        ]
                    ]);

                });

            })->store('xls');

            return response()->json([
                'download' => url('/exports').'/'.$filename.'.xls',
            ]);

        }else{
            $this->crud->hasAccessOrFail('list');

            // crate an array with the names of the searchable columns
            $columns = collect($this->crud->columns)
                ->reject(function ($column, $key) {
                    // the select_multiple columns are not searchable
                    return
                        ( isset($column['type']) && $column['type'] == 'select_multiple' ) ||
                        ( isset($column['exists']) && $column['exists'] == 'extra' );
                })
                ->pluck('name')
                // add the primary key, otherwise the buttons won't work
                ->merge($this->crud->model->getKeyName())
                ->toArray();

            // structure the response in a DataTable-friendly way
            $dataTable = new \LiveControl\EloquentDataTable\DataTable($this->crud->query, $columns);

            // make the datatable use the column types instead of just echoing the text
            $dataTable->setFormatRowFunction(function ($entry) {
                // get the actual HTML for each row's cell
                $row_items = $this->crud->getRowViews($entry, $this->crud);

                // add the buttons as the last column
                if ($this->crud->buttons->where('stack', 'line')->count()) {
                    $row_items[] = \View::make('crud::inc.button_stack', ['stack' => 'line'])
                        ->with('crud', $this->crud)
                        ->with('entry', $entry)
                        ->render();
                }

                // add the details_row buttons as the first column
                if ($this->crud->details_row) {
                    array_unshift($row_items, \View::make('crud::columns.details_row_button')
                        ->with('crud', $this->crud)
                        ->with('entry', $entry)
                        ->render());
                }

                return $row_items;
            });

            return $dataTable->make();
        }
    }
}
