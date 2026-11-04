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
    @if(request()->user()->hasRole('improvement'))
        <a href="{{ url('/qcc_assesment') }}" class="btn btn-sm {{ request()->is('qcc_assesment') ? 'btn-primary' : 'btn-default' }}">
            <i class="fa fa-list"></i> Assessment
        </a>
    @endif
    @if(request()->user()->hasRole('committee'))
        <a href="{{ url('/qcc_assesor') }}" class="btn btn-sm {{ request()->is('qcc_assesor*') ? 'btn-primary' : 'btn-default' }}">
            <i class="fa fa-users"></i> Assessor
        </a>
        <a href="{{ url('/qcc_assesment_team') }}" class="btn btn-sm {{ request()->is('qcc_assesment_team*') ? 'btn-primary' : 'btn-default' }}">
            <i class="fa fa-check-square-o"></i> Penilaian
        </a>
        <a href="{{ url('/qcc_assesment_summary') }}" class="btn btn-sm {{ request()->is('qcc_assesment_summary*') ? 'btn-primary' : 'btn-default' }}">
            <i class="fa fa-bar-chart"></i> Summary Penilaian
        </a>
    @endif
</div>
