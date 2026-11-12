@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>AI Chat <small>Asisten data HRMS</small></h1>
    </section>
    <section class="content">
        @if($canConfigure)
            <p><a class="btn btn-default" href="{{ url('/AIChat/Settings') }}"><i class="fa fa-cog"></i> Pengaturan akses data</a></p>
        @endif
        @if($tables->isEmpty())
            <div class="alert alert-info">Belum ada tabel yang diizinkan. Hubungi admin root untuk mengatur akses.</div>
        @else
            <div class="box box-primary">
                <div class="box-body">
                    <form id="aiChatForm">
                        @csrf
                        <div class="form-group">
                            <label for="aiTable">Tabel</label>
                            <select class="form-control" id="aiTable" name="table_name" required>
                                @foreach($tables as $table)
                                    <option value="{{ $table->table_name }}">{{ $table->table_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="aiQuestion">Pertanyaan</label>
                            <textarea class="form-control" id="aiQuestion" name="question" rows="3" maxlength="1000" required placeholder="Contoh: Berapa jumlah data pada periode ini?"></textarea>
                        </div>
                        <button class="btn btn-primary" id="aiSubmit" type="submit"><i class="fa fa-paper-plane"></i> Tanyakan</button>
                    </form>
                </div>
            </div>
            <div id="aiError" class="alert alert-danger" role="alert" hidden></div>
            <div id="aiResult" class="box box-default" hidden>
                <div class="box-header with-border"><h3 class="box-title">Jawaban</h3></div>
                <div class="box-body">
                    <p id="aiAnswer" style="white-space: pre-wrap"></p>
                    <pre id="aiData" style="max-height: 360px; overflow: auto"></pre>
                </div>
            </div>
            <script>
                document.getElementById('aiChatForm').addEventListener('submit', async function (event) {
                    event.preventDefault();
                    const button = document.getElementById('aiSubmit');
                    const error = document.getElementById('aiError');
                    const result = document.getElementById('aiResult');
                    button.disabled = true;
                    error.hidden = true;
                    result.hidden = true;
                    try {
                        const response = await fetch('{{ url('/AIChat/Ask') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.querySelector('[name="_token"]').value
                            },
                            body: new FormData(this)
                        });
                        let payload;
                        try {
                            payload = await response.json();
                        } catch {
                            throw new Error('Server mengembalikan respons yang tidak dapat dibaca.');
                        }
                        if (!response.ok) {
                            throw new Error(payload.message || Object.values(payload.errors || {}).flat().join(' ') || 'Permintaan AI gagal.');
                        }
                        document.getElementById('aiAnswer').textContent = payload.answer;
                        document.getElementById('aiData').textContent = JSON.stringify(payload.data, null, 2);
                        result.hidden = false;
                    } catch (exception) {
                        error.textContent = exception.message;
                        error.hidden = false;
                    } finally {
                        button.disabled = false;
                    }
                });
            </script>
        @endif
    </section>
</div>
@endsection
