@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                WhatsApp Test
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">WhatsApp Wuzapi</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-whatsapp text-green"></i> Wuzapi Sender +62985368301164 </h3>
                        </div>
                        <form action="/whatsapp/send" method="POST">
                            @csrf
                            <div class="box-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <h4><i class="icon fa fa-check"></i> Berhasil!</h4>
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <h4><i class="icon fa fa-ban"></i> Gagal!</h4>
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="phone">Nomor Tujuan</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        placeholder="Masukkan nomor HP tujuan" value="{{ old('phone') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="template">Pilih Template Pesan (Opsional)</label>
                                    <select class="form-control" id="template" onchange="changeTemplate()">
                                        <option value="">-- Ketik Pesan Manual --</option>
                                        <option
                                            value="Halo, ini adalah pesan peringatan bahwa Anda terlambat absen hari ini. Mohon segera melakukan absensi atau menghubungi HRD.">
                                            Pengingat Telat Absen</option>
                                        <option
                                            value="Halo, pengajuan cuti Anda telah disetujui. Silakan cek sistem EMS untuk detailnya.">
                                            Info Approval Cuti</option>
                                        <option value="Halo, slip gaji Anda bulan ini sudah tersedia di sistem EMS.">Info
                                            Slip
                                            Gaji</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="message">Isi Pesan</label>
                                    <textarea name="message" id="message" rows="6" class="form-control"
                                        placeholder="Ketik pesan Anda di sini..." required>{{ old('message') }}</textarea>
                                </div>
                                <p class="text-muted"><small>*Pesan dikirim melalui API EMS Wuzapi.</small></p>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success"><i class="fa fa-send"></i> Kirim Pesan
                                    WA</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('Scripts')

    <script>
        function changeTemplate() {
            var template = document.getElementById('template').value;
            if (template !== "") {
                document.getElementById('message').value = template;
            }
        }
    </script>

@endsection
