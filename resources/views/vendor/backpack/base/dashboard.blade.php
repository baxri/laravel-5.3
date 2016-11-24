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
                        <td >
                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-cc-mastercard fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h1 class="huge">{{(int)$transaction->count}} / {{number_format($transaction->sum/100,2)}} GEL</h1>
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
                        <td style="padding: 10px;">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-ticket fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h1 class="huge">{{(int)$ticket->count}} / {{number_format($ticket->sum/100,2)}} GEL</h1>
                                            <div>Sold Tickets ToDay</div>
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
                        <td style="padding: 10px;">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-university fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <h1 class="huge">{{(int)$payout->count}} / {{number_format($payout->sum/100,2)}} GEL</h1>
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
                </table>




                <div class="panel panel-default" >
                    <div class="panel-heading">
                        <i></i> Last 10 IP Activity
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
