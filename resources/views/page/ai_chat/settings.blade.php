@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Pengaturan AI Chat <small>Hanya role root</small></h1>
    </section>
    <section class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="alert alert-warning">
            Data hanya dapat dibaca oleh AI. Pilih kolom yang boleh digunakan dan tentukan cakupan baris.
            Cakupan semua baris memberi akses ke semua data dalam tabel tersebut.
        </div>

        <form method="GET" action="{{ url('/AIChat/Settings') }}" style="margin-bottom: 20px">
            <div class="form-group" style="width: 100%; max-width: 600px">
                <label for="tableSelector">Pilih tabel</label>
                <select id="tableSelector" name="table" class="form-control select2" style="width: 100%" onchange="this.form.submit()">
                    <option value="">-- Pilih tabel --</option>
                    @foreach($tables as $table)
                        <option value="{{ $table['name'] }}" @selected($selectedTable === $table['name'])>{{ $table['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($selectedTable && count($columns))
            @php($currentColumns = $current ? json_decode($current->allowed_columns, true) : [])
            <form method="POST" action="{{ url('/AIChat/Settings') }}" class="box box-primary">
                @csrf
                <input type="hidden" name="table_name" value="{{ $selectedTable }}">
                <div class="box-header with-border"><h3 class="box-title">{{ $selectedTable }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Cakupan baris</label>
                        <select name="row_scope" id="rowScope" class="form-control" required>
                            <option value="employee" @selected(($current->row_scope ?? '') === 'employee')>Data milik karyawan yang login</option>
                            <option value="department" @selected(($current->row_scope ?? '') === 'department')>Data departemen karyawan yang login</option>
                            <option value="all" @selected(($current->row_scope ?? '') === 'all')>Semua baris</option>
                        </select>
                    </div>
                    <div class="form-group" id="scopeColumnGroup">
                        <label>Kolom pembatas data</label>
                        <select name="scope_column" class="form-control">
                            <option value="">-- Pilih kolom --</option>
                            @foreach($columns as $column)
                                <option value="{{ $column['name'] }}" @selected(($current->scope_column ?? '') === $column['name'])>{{ $column['name'] }} ({{ $column['type'] }})</option>
                            @endforeach
                        </select>
                        <p class="help-block">Kolom ini dibandingkan dengan ID karyawan atau ID departemen dari akun yang login.</p>
                    </div>
                    <div class="form-group" style="width: 100%; margin-top: 15px">
                        <label>Kolom yang boleh digunakan AI</label>
                        <div style="margin: 8px 0">
                            <button type="button" class="btn btn-default btn-sm" id="selectAllColumns">Pilih semua</button>
                            <button type="button" class="btn btn-default btn-sm" id="clearAllColumns">Lepas semua</button>
                        </div>
                        <div class="row">
                            @foreach($columns as $column)
                                <div class="col-sm-4">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="ai-allowed-column" name="allowed_columns[]" value="{{ $column['name'] }}" @checked(in_array($column['name'], $currentColumns ?? [], true))>
                                        {{ $column['name'] }} <small>({{ $column['type'] }})</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit">Simpan izin</button>
                </div>
            </form>
        @elseif($selectedTable)
            <div class="alert alert-danger">Kolom tabel tidak dapat dibaca.</div>
        @endif

        @if($current)
            <form method="POST" action="{{ url('/AIChat/Settings/Delete') }}" onsubmit="return confirm('Hapus izin AI untuk tabel ini?')">
                @csrf
                <input type="hidden" name="table_name" value="{{ $current->table_name }}">
                <button class="btn btn-danger" type="submit">Hapus izin tabel</button>
            </form>
        @endif
    </section>
</div>
<script>
    (function () {
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#tableSelector').select2({
                width: '100%',
                placeholder: '-- Cari tabel --',
                allowClear: true
            });
        }

        const scope = document.getElementById('rowScope');
        const group = document.getElementById('scopeColumnGroup');
        if (scope && group) {
            const updateScopeColumn = function () {
                group.style.display = scope.value === 'all' ? 'none' : '';
            };
            scope.addEventListener('change', updateScopeColumn);
            updateScopeColumn();
        }

        const columns = document.querySelectorAll('.ai-allowed-column');
        const setColumnSelection = function (checked) {
            columns.forEach(function (column) {
                column.checked = checked;
            });
        };
        const selectAllButton = document.getElementById('selectAllColumns');
        const clearAllButton = document.getElementById('clearAllColumns');
        if (selectAllButton) {
            selectAllButton.addEventListener('click', function () {
                setColumnSelection(true);
            });
        }
        if (clearAllButton) {
            clearAllButton.addEventListener('click', function () {
                setColumnSelection(false);
            });
        }
    })();
</script>
@endsection
