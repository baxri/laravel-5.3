@if (Auth::check())
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">


        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
          <li class="header">{{ trans('backpack::base.administration') }}</li>
          <!-- ================================================ -->
          <!-- ==== Recommended place for admin menu items ==== -->
          <!-- ================================================ -->
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction') }}"><i class="fa fa-cc-mastercard"></i> <span>Transactions</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/ticket') }}"><i class="fa fa-ticket"></i> <span>Tickets</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/payout') }}"><i class="fa fa-university"></i> <span>Bank Payouts</span></a></li>

          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/station') }}"><i class="fa fa-train"></i> <span>Stations</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/ip') }}"><i class="fa fa-globe"></i> <span>Ips</span></a></li>

          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/news') }}"><i class="fa fa-newspaper-o"></i> <span>Articles</span></a></li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/note') }}"><i class="fa fa-comments-o"></i> <span>Messages</span></a></li>

          <!-- ======================================= -->
          <li class="header">{{ trans('backpack::base.user') }}</li>
          <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/logout') }}"><i class="fa fa-sign-out"></i> <span>{{ trans('backpack::base.logout') }}</span></a></li>
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
@endif
