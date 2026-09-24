@php
    $currentLevel = isset($level) ? $level : 1;
    $hasChildren = !empty($node['children']) && count($node['children']) > 0;
    // Show top 2 rows: Level 1 children (Level 2) are shown, Level 2 children (Level 3+) are collapsed by default
    $isCollapsed = $currentLevel >= 2;
@endphp

<li>
    <!-- Employee Card Box -->
    <div class="node-box" data-id="{{ $node['id'] }}" data-name="{{ $node['employee_name'] }}"
        data-nik="{{ $node['NIK'] }}" data-dept="{{ $node['dept_code'] ?? '-' }}"
        data-position="{{ $node['position_name'] ?? '-' }}">

        <!-- Portrait Rectangle Avatar Icon -->
        <!-- <div class="node-avatar-box">
            <i class="fa fa-user"></i>
        </div> -->

        <!-- Employee Name -->
        <div class="node-name" title="{{ $node['employee_name'] }}">
            {{ $node['employee_name'] }}
        </div>

        <!-- NIK & Dept Code -->
        <div class="node-badges">
            <span class="label label-default" style="font-size: 10px;">{{ $node['NIK'] }}</span>
            @if(!empty($node['dept_code']))
                <span class="node-badge-dept">{{ $node['dept_code'] }}</span>
            @endif
        </div>

        <!-- Position Name -->
        <div class="node-position" title="{{ $node['position_name'] ?? '-' }}">
            {{ $node['position_name'] ?? '-' }}
        </div>

        <!-- Subordinate Counts -->
        <div class="sub-counts">
            <div class="count-item" title="Jumlah bawahan langsung">
                <span class="count-number badge-direct">{{ $node['direct_count'] }}</span>
                <span class="count-label">Direct</span>
            </div>
            <div class="count-item" title="Jumlah seluruh bawahan di bawah struktur">
                <span class="count-number badge-total">{{ $node['total_count'] }}</span>
                <span class="count-label">Total Sub</span>
            </div>
        </div>
    </div>

    <!-- Toggle Button Wrapper with Connector Line -->
    @if($hasChildren)
        <div class="btn-toggle-wrapper">
            <button type="button" class="btn btn-default btn-xs tree-toggle-btn" title="Klik untuk Buka/Tutup Bawahan">
                <i class="fa {{ $isCollapsed ? 'fa-plus' : 'fa-minus' }}"></i> {{ count($node['children']) }} Bawahan
            </button>
        </div>

        <!-- Recursive Children -->
        <ul class="{{ $isCollapsed ? 'hidden-branch' : '' }}">
            @foreach($node['children'] as $childNode)
                @include('page.admin.structure.node', ['node' => $childNode, 'level' => $currentLevel + 1])
            @endforeach
        </ul>
    @endif
</li>