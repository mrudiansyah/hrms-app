@extends('layouts/home')
@section('Contents')
    <?php $filename = "http://localhost/ESS/public/fokar/" . $id_employee . ".jpg";?>

    <!-- Main content -->
    <section class="content">
        @foreach($tb_employee as $dt)
            <div class="row">
                <div class="col-lg-4 col-sm-6 col-xs-12">

                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body" style="padding-top:20px;">
                            <div class="box-body box-profile">
                                <img title="{{$photo}}" class="profile-user-img img-responsive img-square"
                                    style="width:50%;border:1px solid #ccc;" src="<?php    echo $photo;?>"
                                    alt="User profile picture">
                                <h3 class="profile-username text-center">{{$dt->employee_name}}</h3>
                                <p class="text-muted text-center">{{$dt->dept_name}} Dept<br>{{$dt->position_name}}</p>
                                <div class="row" style="padding:20px;">
                                    <div class="col-lg-6 col-sm-6 col-xs-6" style="text-align:center;">
                                        Checkin<br><b style="font-size:20px;color:#0F0;">{{$checkin_act}}</b>
                                    </div>
                                    <div class="col-lg-6 col-sm-6 col-xs-6" style="text-align:center;">
                                        Checkout<br><b style="font-size:20px;color:#F00;">{{$checkout_act}}</b>
                                    </div>
                                </div>
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <b>Today Status</b>
                                        <a
                                            class="pull-right"><?php    if ($checkin_act == '-' && $checkout_act == '-')
                echo 'Absent';
            else
                echo 'Present';?></a>

                                    </li>
                                    <li class="list-group-item">
                                        <b>Group </b> <a class="pull-right">{{$shift_code}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Current Working Time</b> <a class="pull-right">{{$cshift}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.box -->

                    <!-- Leave -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <strong><i class="fa fa-book margin-r-5"></i> Leave Status</strong>
                            <div class="box-tools pull-right">
                                &nbsp;
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            @foreach($tb_employee_leave as $dt3)
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- The time line -->
                                        <ul class="timeline">
                                            <!-- timeline time label -->
                                            <li class="time-label">
                                                <span class="bg-aqua">
                                                    <?php 
                                        echo date('d M Y', strtotime($dt3->start)) . ' ~ ' . date('d M Y', strtotime($dt3->end));
                                      ?>
                                                </span>
                                            </li>
                                            <?php        $Bln = '';?>
                                            @foreach($tb_leaves as $dt4)
                                                                    <li>
                                                                        <?php            if ($dt4->start_leave > date('Y-m-d'))
                                                    $warna = 'yellow';
                                                else
                                                    $warna = 'blue';?>
                                                                        <?php            if (date('m', strtotime($dt4->start_leave)) != $Bln)
                                                    echo "<i class='fa fa-calendar bg-" . $warna . "'></i>";?>
                                                                        <div class="timeline-item">
                                                                            <?php            if (date('m', strtotime($dt4->start_leave)) != $Bln)
                                                    echo "<span class='time' style='font-size:12px;padding:3px;'><i class='fa fa-clock-o'></i> " . date('M', strtotime($dt4->start_leave)) . "</span>";
                                                $Bln = date('m', strtotime($dt4->start_leave));?>
                                                                            <h3 class="timeline-header" style="font-size:14px;padding:3px;">
                                                                                <?php 
                                                                if ($dt4->start_leave == $dt4->finish_leave)
                                                    echo date('l, d M Y', strtotime($dt4->start_leave));
                                                else
                                                    echo date('l, d', strtotime($dt4->start_leave)) . '~' . date('d M Y', strtotime($dt4->finish_leave));
                                                              ?>
                                                                            </h3>

                                                                        </div>
                                                                    </li>
                                            @endforeach
                                        </ul>

                                    </div>
                                    <div class="box-foot" style="padding-left:20px;">
                                        <b>Leave Used: {{$dt3->used}} Days</b>
                                        <div class="box-tools pull-right" style="padding-right:20px;">
                                            <b>Leave Ballance: {{$dt3->outstanding}} Days</b>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            @endforeach



                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- End Leave -->

                </div>
                <div class="col-lg-4 col-sm-12 col-xs-12">

                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <strong><i class="fa fa-user margin-r-5"></i> Employee Register</strong><br><br>

                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>NIK</b> <a class="pull-right">{{$dt->NIK}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Gender</b> <a class="pull-right">{{$dt->gender}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Date of Birth</b> <a class="pull-right">{{$dt->tempat_lahir}}
                                        <?php    if ($dt->tanggal_lahir != '')
                echo date(', d-m-Y', strtotime($dt->tanggal_lahir));?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Religion</b> <a class="pull-right">{{$dt->agama}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Level</b> <a class="pull-right">{{$dt->position_name}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Direct Leader</b>
                                    <a class="pull-right">{{$leader_name}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Divisi</b> <a class="pull-right">{{$dt->divisi}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Department</b> <a class="pull-right">{{$dt->dept_name}}</a>
                                </li>
                            </ul>

                            <strong><i class="fa fa-map-marker margin-r-5"></i> Position</strong>
                            <p class="text-muted">
                            <ul>
                                <?php    $jml = 0;?>
                                @foreach($tb_bagian as $rec)
                                                <?php        $jml++;?>
                                                <li>
                                                    {{$rec->posisi}} <?php        if ($rec->line != '')
                                    echo " - " . $rec->line;?><br>
                                                    Start from <?php        echo date('F Y', strtotime($rec->implement));?>
                                                </li>
                                @endforeach
                                <?php    if ($jml == 0)
                echo "<li>No Record</li>";?>
                            </ul>
                            </p>
                            <hr>

                            <strong><i class="fa fa-book margin-r-5"></i> Education</strong>
                            <p class="text-muted">
                            <ul>
                                @foreach($tb_education as $rec)
                                    <li>
                                        {{$rec->institute}},<br>Program Study {{$rec->prodi}}, Graduate on {{$rec->graduate_year}}
                                    </li>
                                @endforeach
                            </ul>
                            </p>
                            <hr>


                            <strong><i class="fa fa-list-alt margin-r-5"></i> Experience</strong>
                            <p class="text-muted">
                            <ul>
                                @foreach($tb_experience as $rec)
                                    <li>
                                        {{$rec->factory}}<br>{{$rec->section}} start from {{$rec->year}}, Finish on
                                        {{$rec->finish_year}}
                                    </li>
                                @endforeach
                            </ul>
                            </p>
                            <hr>


                            <strong><i class="fa fa-pencil margin-r-5"></i> Skills</strong>
                            <?php    $char = array('danger', 'success', 'info', 'warning', 'primary');
            $i = 1;
            $j = 0;?>
                            <p>
                                @foreach($tb_skill as $rec)
                                    <?php        $j = $i % 5;?>
                                    <span class="label label-<?php        echo $char[$j];?>">{{$rec->skill_name}}</span>
                                    <?php        $i++;?>
                                @endforeach
                            </p>
                            <hr>
                            <strong><i class="fa fa-file-text-o margin-r-5"></i> Warning Letter</strong>
                            <p>No Records</p>

                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- Contract History -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <strong><i class="fa fa-edit margin-r-5"></i> Contract History</strong>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <?php    $i = 0;?>
                            <ul>
                                @foreach($tb_status as $dt2)
                                            <?php        if ($dt2->contract_name != 'Draft') {?>
                                            <li>{{$dt2->start_contract}}<?php            if ($dt2->contract_name != 'Permanen')
                                    echo " ~ " . $dt2->finish_contract . ",";?>
                                                {{$dt2->contract_name}}</li>
                                            <?php        }?>
                                @endforeach
                            </ul>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /Contract Hiostory -->

                </div>
                <div class="col-lg-4 col-sm-6 col-xs-12">

                    <!-- Address -->

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <strong><i class="fa fa-map-marker margin-r-5"></i> Employee Address</strong>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            @foreach($tb_address as $dtadr)
                                <p class="text-muted">
                                    {{$dtadr->detail}}, {{$dtadr->kelurahan}} {{$dtadr->kecamatan}} {{$dtadr->kabupaten}}
                                    {{$dtadr->provinsi}}<br>
                                </p>
                                <?php        $peta = $dtadr->map_address;?>
                                <div style="padding:7px;border:1px solid #DDD;border-radius:5px;">
                                    <?php        echo $peta;?>
                                </div>
                            @endforeach
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <strong><i class="fa fa-map-marker margin-r-5"></i> Employee Domicile</strong>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            @foreach($tb_domicile as $dtadr)
                                <p class="text-muted">
                                    {{$dtadr->detail}}, {{$dtadr->kelurahan}} {{$dtadr->kecamatan}} {{$dtadr->kabupaten}}
                                    {{$dtadr->provinsi}}<br>
                                </p>
                                <?php        $peta = $dtadr->map_address;?>
                                <div style="padding:7px;border:1px solid #DDD;border-radius:5px;">
                                    <?php        echo $peta;?>
                                </div>
                            @endforeach
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /Address -->
                    <!-- Emergency Contact -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <strong><i class="fa fa-exclamation-circle margin-r-5"></i> Emergency Contact</strong>
                        </div>
                        <div class="box-body">
                            <?php    $i = 0;?>
                            <ul class="list-group list-group-unbordered">
                                @foreach($tb_address_darurat as $dt2)
                                    <li class="list-group-item">
                                        <b>Nama Keluarga</b> <a class="pull-right">{{$dt2->nama_keluarga}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Hubungan</b> <a class="pull-right">{{$dt2->hubungan}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Nomor Kontak</b> <a class="pull-right">{{$dt2->nomor_kontak}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <label>Alamat</label><br>
                                        {{$dt2->detail_kontak}},
                                        Kel.{{$dt2->kelurahan_kontak}},
                                        Kab.{{$dt2->kabupaten_kontak}} -
                                        {{$dt2->provinsi_kontak}}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!-- /Emergency Contact -->
                    <!-- Family -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <strong><i class="fa fa-users margin-r-5"></i> Employee's Family</strong>
                        </div>
                        <div class="box-body">
                            <ul>
                                <?php    $anak = 0;
            $pasangan = 0;?>
                                @foreach($tb_employee_family as $dt2)
                                                    <?php        if (substr($dt2->hubungan, 0, 4) == 'Anak')
                                        $anak++;
                                    if (substr($dt2->hubungan, 0, 4) != 'Anak')
                                        $pasangan++;?>
                                                    <li>
                                                        {{$dt2->hubungan}}: {{$dt2->nama_keluarga}}, Lahir
                                                        <?php        echo date('d-m-Y', strtotime($dt2->tanggal_lahir));?>
                                                    </li>
                                @endforeach
                                <?php    $new_anak = $anak + 1;?>
                            </ul>
                        </div>
                    </div>
                    <!-- /Family -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        @endforeach
    </section>
    <!-- /.content -->
    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog box box-danger" style="width:400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                </div>
                <div class="modal-body">
                    Click Yes to Delete : <b id="delname"></b> ?
                    <input type="hidden" id="delid">
                    <input type="hidden" id="delid1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                </div>
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection
@section('Scripts')
    <!-- page script Tabel-->
    <script>
        $(function () {
            $('#table1').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 25,
                'autoWidth': false
            })
            $('#table2').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true
            })
        })
    </script>
    <!-- page script alert-->
    <script type="text/javascript">
        $(document).ready(function () {
            // sembunyikan form kabupaten, kecamatan dan desa
            $("#form_kab3").hide();
            $("#form_kec3").hide();
            $("#form_des3").hide();

            $("#form_kabs3").hide();
            $("#form_kecs3").hide();
            $("#form_dess3").hide();
            $("#detail_address3").hide();
            $("#saveAddress3").hide();
            $("#link_map3").hide();
            //$("#address").hide();

            // ambil data kabupaten ketika data memilih provinsi
            $('body').on("change", "#form_prov3", function () {
                var id = $(this).val();
                var data = "id=" + id;
                $.ajax({
                    type: 'POST',
                    url: "/ESS/Admin/Employee/Kabupaten",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (hasil) {
                        $("#form_kab3").html(hasil);
                        $("#form_kab3").show();
                        $("#form_kec3").hide();
                        $("#form_des3").hide();

                        $("#form_kabs3").show();
                        $("#form_kecs3").hide();
                        $("#form_dess3").hide();
                        $("#detail_address3").hide();
                        $("#link_map3").hide();
                    }
                });
            });

            // ambil data kecamatan/kota ketika data memilih kabupaten
            $('body').on("change", "#form_kab3", function () {
                var id = $(this).val();
                var data = "id=" + id;
                $.ajax({
                    type: 'POST',
                    url: "/ESS/Admin/Employee/Kecamatan",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (hasil) {
                        $("#form_kec3").html(hasil);
                        $("#form_kec3").show();
                        $("#form_des3").hide();

                        $("#form_kecs3").show();
                        $("#form_dess3").hide();
                        $("#detail_address3").hide();
                        $("#link_map3").hide();
                    }
                });
            });

            // ambil data desa ketika data memilih kecamatan/kota
            $('body').on("change", "#form_kec3", function () {
                var id = $(this).val();
                var data = "id=" + id;
                $.ajax({
                    type: 'POST',
                    url: "/ESS/Admin/Employee/Desa",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (hasil) {
                        $("#form_des3").html(hasil);
                        $("#form_des3").show();

                        $("#form_dess3").show();
                        $("#detail_address3").hide();
                        $("#link_map3").hide();
                    }
                });
            });
            $('body').on("change", "#form_des3", function () {
                $("#detail_address3").show();
                $("#saveAddress3").show();
                $("#link_map3").show();
            });


        });
    </script>
    <script type="text/javascript">
        // Delete Data
        $(document).on('click', '.delete-modal', function () {
            $('#delid').val($(this).data('delid'));
            $('#delid1').val($(this).data('delid1'));
            $('#delname').text($(this).data('delname'));
            $('#modal-delete').modal('show');
        });
        $('.modal-footer').on('click', '.delete', function () {
            var id = $('#delid').val();
            var data = "id=" + id;
            $.ajax({
                type: 'POST',
                url: "/ESS/Admin/Employee/Delete/Domicile",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (hasil) {
                    //alert(hasil);
                    window.location.reload();
                }
            });
        });
    </script>
    <script>
        function generateMapLink() {
            //alert("Masuk");
            // Ambil nilai dari inputan
            const provinsi = document.getElementById('form_prov3').value;
            const kabupaten = document.getElementById('form_kab3').value;
            const kecamatan = document.getElementById('form_kec3').value;
            const desa = document.getElementById('form_des3').value;
            const detailAlamat = document.getElementById('detail_address3').value;

            // Gabungkan alamat menjadi satu string
            const fullAddress = `${detailAlamat}, ${desa}, ${kecamatan}, ${kabupaten}, ${provinsi}`;

            // Encode alamat untuk URL
            const encodedAddress = encodeURIComponent(fullAddress);

            // Buat URL iframe Google Maps
            const iframeSrc = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0405391163818!2d107.27705657459006!3d-6.258390461272131!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69832243580c7f%3A0xbf61a43aa7646d4!2s${encodedAddress}!5e0!3m2!1sen!2sid!4v1741062632282!5m2!1sen!2sid`;

            // Buat kode iframe
            const iframeCode = `<iframe src="${iframeSrc}" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;

            // Tampilkan kode iframe di textarea
            document.getElementById('peta').value = iframeCode;
        }
    </script>

@endsection