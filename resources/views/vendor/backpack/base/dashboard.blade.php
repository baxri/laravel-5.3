@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        ToDay is {{  date('d F H:i ', strtotime( \Carbon\Carbon::now() )) }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default" style="padding: 10px;">

                <table width="100%">
                    <tr>
                        <td style="padding: 5px;" width="50%">
                            <div class="panel panel-success ">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-cc-mastercard fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h4 class="huge">
                                                <b>{{(int)$transaction->count}} / {{number_format($transaction->sum/100,2)}} GEL</b>
                                            </h4>
                                            <div>Transactions/Payments ToDay</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction') }}">
                                    <div class="panel-footer">
                                        <span class="pull-left">
                                             View Details
                                        </span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </td>
                        <td style="padding: 5px;" colspan="2" width="50%">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-university fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h4 class="huge">
                                                <b>{{(int)$payout->count}} / {{number_format($payout->sum/100,2)}} GEL</b>
                                            </h4>
                                            <div>Bank Payouts ToDay</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ url(config('backpack.base.route_prefix', 'admin').'/payout') }}">
                                    <div class="panel-footer">
                                        <span class="pull-left">
                                                View Details
                                        </span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </td>


                    </tr>

                    <tr>
                        <td style="padding: 5px;">
                            <div class="panel panel-default">
                                <div class="panel-heading  ">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-cc-mastercard fa-1x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h4>
                                                @foreach( $transaction_statuses as $t )
                                                    <b>
                                                        @if( $t->status == \App\Models\Transaction::$process )
                                                            <span >
                                                            <a style="color: lightskyblue;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?status='.\App\Ticket::$process) }}"> Process ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Models\Transaction::$hold )
                                                            <span >
                                                            <a style="color: red;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?status='.\App\Ticket::$hold) }}">Hold ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Models\Transaction::$cancel )
                                                            <span >
                                                            <a style="color: red;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?status='.\App\Ticket::$cancel) }}">Cancel ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Models\Transaction::$success )
                                                            <span >
                                                            <a style="color: green;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?status='.\App\Ticket::$success) }}">Success ( {{$t->count}} )</a>
                                                        </span>
                                                        @endif
                                                    </b>
                                                @endforeach

                                                @if( count($transaction_statuses) == 0 )
                                                    <b>No Transactions Found ToDay</b>
                                                @endif
                                            </h4>
                                            <div>Transaction Statuses</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </td>
                        <td style="padding: 5px;">
                            <div class="panel panel-default">
                                <div class="panel-heading  ">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-university fa-1x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h4>
                                                @foreach( $payout_statuses as $t )
                                                    <b>
                                                        @if( $t->status == \App\Models\PayoutTransaction::$process )
                                                            <span >
                                                            <a style="color: lightskyblue;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/payout?status='.\App\Ticket::$process) }}"> Process ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Models\PayoutTransaction::$hold )
                                                            <span >
                                                            <a style="color: red;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/payout?status='.\App\Ticket::$hold) }}">Hold ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Models\PayoutTransaction::$cancel )
                                                            <span >
                                                            <a style="color: red;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/payout?status='.\App\Ticket::$cancel) }}">Cancel ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Models\PayoutTransaction::$success )
                                                            <span >
                                                            <a style="color: green;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/payout?status='.\App\Ticket::$success) }}">Success ( {{$t->count}} )</a>
                                                        </span>
                                                        @endif
                                                    </b>
                                                @endforeach

                                                @if( count($payout_statuses) == 0 )
                                                    <b>No Bank Payouts Found ToDay</b>
                                                @endif
                                            </h4>
                                            <div>Payout Statuses</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 5px;">
                            <div class="panel panel-default">
                                <div class="panel-heading  ">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-train fa-1x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h4>
                                                @foreach( $ticket_statuses as $t )
                                                    <b>
                                                        @if( $t->status == \App\Ticket::$process )
                                                            <span >
                                                            <a style="color: lightskyblue;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?ticket_status='.\App\Ticket::$process) }}"> Process ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Ticket::$hold )
                                                            <span >
                                                            <a style="color: red;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?ticket_status='.\App\Ticket::$hold) }}">Hold ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Ticket::$cancel )
                                                            <span >
                                                            <a style="color: red;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?ticket_status='.\App\Ticket::$cancel) }}">Cancel ( {{$t->count}} )</a>
                                                        </span>
                                                        @elseif( $t->status == \App\Ticket::$success )
                                                            <span >
                                                            <a style="color: green;" href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction?ticket_status='.\App\Ticket::$success) }}">Success ( {{$t->count}} )</a>
                                                        </span>
                                                        @endif
                                                    </b>
                                                @endforeach

                                                @if( count($ticket_statuses) == 0 )
                                                    <b>No Tickets Found ToDay</b>
                                                @endif
                                            </h4>
                                            <div>Ticket Statuses</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                </table>




                <div class="panel panel-default" style="margin: 5px;">
                    <div class="panel-heading">
                        <i></i> Last {{config('railway.last_ip_activity_count')}} IP Activity
                        <div class="pull-right">
                            <a href="{{ url(config('backpack.base.route_prefix', 'admin').'/ip') }}">
                                View Details
                            </a>
                        </div>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>IP</th>
                                            <th>Country Code</th>
                                            <th>Country</th>
                                            <th>City</th>
                                            <th>Isp</th>
                                            <th>As</th>
                                            <th>Region Name</th>
                                            <th>Region</th>
                                            <th>Time/Zone</th>
                                            <th>Lat</th>
                                            <th>Lon</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach( $ips as $ip )
                                            <tr>
                                                <td>{{$ip->id}}</td>
                                                <td>{{$ip->ip_key}}</td>
                                                <td>{{$ip->countryCode}}</td>
                                                <td>{{$ip->country}}</td>
                                                <td>{{$ip->city}}</td>
                                                <td>{{$ip->isp}}</td>
                                                <td>{{$ip->as}}</td>
                                                <td>{{$ip->regionName}}</td>
                                                <td>{{$ip->region}}</td>
                                                <td>{{$ip->timezone}}</td>
                                                <td>{{$ip->lat}}</td>
                                                <td>{{$ip->lon}}</td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="11" align="center">
                                                @if(count($ips) == 0)
                                                    No IP Activity Found
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.panel-body -->
                </div>






            </div>
        </div>
    </div>
@endsection

<script>
    jQuery(document).ready(function($) {



    });
</script>
