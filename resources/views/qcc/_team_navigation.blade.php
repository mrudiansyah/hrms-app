<div class="qcc-navigation">
    <a href="{{ url('/qcc_team') }}" class="btn btn-sm {{ request()->is('qcc_team') ? 'btn-primary' : 'btn-default' }}">
        <i class="fa fa-users"></i> Master Team
    </a>
    <a href="{{ url('/qcc_team/periode') }}" class="btn btn-sm {{ request()->is('qcc_team/periode') ? 'btn-primary' : 'btn-default' }}">
        <i class="fa fa-calendar"></i> Periode Team
    </a>
</div>
