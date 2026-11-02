<div class="qcc-navigation">
    <a href="{{ url('/qcc_step') }}" class="btn btn-sm {{ request()->is('qcc_step*') ? 'btn-primary' : 'btn-default' }}">
        <i class="fa fa-list-ol"></i> Master Steps
    </a>
    <a href="{{ url('/qcc_perriode') }}" class="btn btn-sm {{ request()->is('qcc_perriode*') ? 'btn-primary' : 'btn-default' }}">
        <i class="fa fa-calendar"></i> QCC Periode
    </a>
    <a href="{{ url('/qcc_schedule') }}" class="btn btn-sm {{ request()->is('qcc_schedule*') ? 'btn-primary' : 'btn-default' }}">
        <i class="fa fa-calendar-check-o"></i> Schedule
    </a>
</div>
