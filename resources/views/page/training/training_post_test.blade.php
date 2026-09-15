@extends('layouts/home')
@section('Contents')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Contents -->
    <style>
        #tablesx th {
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            background-color: #2F4F4F;
            color: white;
        }

        .table1 tr:hover {
            cursor: pointer;
        }

        #tables th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #tables tbody tr:hover {
            cursor: pointer;
        }

        #table2 th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #table2 tbody tr:hover {
            cursor: pointer;
        }

        #table3 th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #table3 tbody tr:hover {
            cursor: pointer;
        }

        #table4 th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #table4 tbody tr:hover {
            cursor: default;
        }
    </style>
    <section class="content-header">
        <h1 onclick="">
            Post Test
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header">
                        <i class="fa fa-file-text-o"></i>
                        <h3 class="box-title">Question List for {{$test_name}}</h3>
                        <div class="box-tools pull-right">
                            &nbsp;
                        </div>
                    </div>
                    <div class="box-body" style="overflow-x: scroll;" id="soal">
                        <?php 
                                        $no = 1;
    $total = 0;
    $benar = 0;
    $percent = '';
    $status_grade = 0;
    foreach ($tb_question as $dt) {
        $no++;
        $total++;
        echo "<div class='col-md-12 col-lg-12 col-xs-12'>";
        echo "<div class='box box-default'>";
        echo "<div class='box-header with-border'>";
        echo "<h3 class='box-title'>";
        echo "<b class='label label-info'>" . $dt->index_question . "</b>";
        echo "</h3>";
        echo $dt->question;
        echo "<div class='pull-right'>";
        if ($dt->answer_code == $dt->answer_actual) {
            $benar++;
            //echo "<i class='fa fa-thumbs-o-up'></i> ";
            // echo "<b class='badge bg-green'>".$dt->answer_actual."</b>";
        } elseif ($dt->answer_code != $dt->answer_actual && $dt->answer_actual != '') {
            // echo "<b class='badge bg-red'>".$dt->answer_actual."</b>";
        }
        $percent = $benar * 100 / $total;
        if ($percent >= $passing_grade)
            $status_grade = 1;
        else
            $status_grade = 0;
        echo "</div>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<div class='form-group col-lg-6'>";
        echo "<b>A</b> <div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='A'><label class=''><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='A'";
        if ($dt->answer_actual == 'A')
            echo ' checked';
        echo ">" . $dt->option_a . "</label></div></div>";
        echo " <div class='form-group col-lg-6'><b>B</b><div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='B'><label class=''><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='B'";
        if ($dt->answer_actual == 'B')
            echo ' checked';
        echo ">" . $dt->option_b . "</label></div></div>";
        echo "<div class='form-group col-lg-6'><b>C</b><div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='C'><label class=''><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='C'";
        if ($dt->answer_actual == 'C')
            echo ' checked';
        echo ">" . $dt->option_c . "</label></div></div>";
        echo "<div class='form-group col-lg-6'><b>D</b><div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='D'><label class=''><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='D'";
        if ($dt->answer_actual == 'D')
            echo ' checked';
        echo ">" . $dt->option_d . "</label></div></div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    echo "<input type='hidden' value='" . $percent . "' id='percent'>";
    echo "<input type='hidden' value='" . $status_grade . "' id='statusgrade'>";
                                    ?>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header">
                        <i class="fa fa-square-check"></i>
                        <h3 class="box-title">Post Test Value</h3>
                        <div class="box-tools pull-right">
                            <button id="finishtest" title="Examination" type="button" class="btn btn-primary">Finish Test
                                &nbsp;<i class="fa fa-angle-double-right"></i></button>
                        </div>
                    </div>
                    {{-- <div class="box-body" style="overflow-x: scroll;"> --}}
                        {{-- <table id="table2" class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:30px;">No</th>
                                    <th>Training Date</th>
                                    <th>Test Name</th>
                                    <th>Target</th>
                                    <th>Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=0;?>
                                @foreach($tb_training_test as $dt)
                                <tr>
                                    <td>
                                        <?php $no++;echo $no;?>
                                    </td>
                                    <td>
                                        <?php echo date('d-m-Y',strtotime($dt->tanggal_aktual));?>
                                    </td>
                                    <td>{{$dt->test_name}}</td>
                                    <td>{{$dt->passing_grade}} <small>%</small></td>
                                    <td><b id="hasil">{{$dt->post_test}} <small>%</small></b>
                                        <div class="pull-right">
                                            <b id="hasilstatus">
                                                <?php 
                                                                if($dt->post_test>=$dt->passing_grade)echo "<i class='fa fa-thumbs-o-up' style='color:green;'></i>";
                                                                elseif($dt->post_test<$dt->passing_grade&&$dt->post_test>0) echo "<i class='fa fa-thumbs-o-down' style='color:red;'></i>";
                                                            ?>
                                            </b>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table> --}}
                        {{-- </div> --}}
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>


@endsection
@section('Scripts')
    <!-- page script Tabel-->
    <script>
        $(function () {
            $('#table2').DataTable({
                'paging': false,
                'lengthChange': true,
                'searching': false,
                'ordering': true,
                'info': true,
                "pageLength": 10,
                'autoWidth': false,
            })
        })
        $(function () {
            $('#table3').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 10,
                'autoWidth': false,
            })
        })
    </script>
    <script>
        window.setTimeout(function () {
            $(".alert").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 5000);
    </script>
    <script type="text/javascript">
        // Form
        $(document).on('click', '.pilihan', function () {
            var idparticipant = "{{$id_participant}}";
            var idquestion = $(this).data('idquestion');
            var isi = $(this).data('isi');
            var idtest = "{{$id_test}}";
            var passinggrade = "{{$passing_grade}}";

            $.ajaxSetup({
                type: "POST",
                url: "/Simpan/PostTest",
            });
            $.ajax({
                data: { _token: $('meta[name="csrf-token"]').attr('content'), idparticipant: idparticipant, idquestion: idquestion, isi: isi, idtest: idtest, passinggrade: passinggrade },
                success: function (respond) {
                    $('#soal').html(respond);
                    var x = $('#percent').val();
                    var y = $('#statusgrade').val();
                    $('#hasil').html(x);
                    if (y == 1) {
                        $('#hasilstatus').html("<i class='fa fa-thumbs-o-up' style='color:green;'></i>");
                    } else {
                        $('#hasilstatus').html("<i class='fa fa-thumbs-o-down' style='color:red;'></i>");
                    }
                }, error: function (err) {
                    alert("Terjadi kesalahan saat record data,  silakan klik kembali jawaban anda.")
                }
            })
        });

        // Form End
        $(document).on('click', '#finishtest', function () {
            let isExecuted = confirm("Are you sure to execute this action?");
            if (isExecuted) {
                var x ={{ $id_participant }};
                var y = "{{ $id_training_actual }}"
                var passing_grade = "{{ $passing_grade }}"
                var data = {
                    _token: $('meta[name="csrf-token"]').attr('content'), id_participant: x, passing_grade: passing_grade
                }
                $.ajax({
                    type: "POST",
                    url: "/Training.FinishTestPost",
                    data: data,
                    success: function (data) {
                        console.log(data)
                        window.location.href = '/Training/Actual/' + y + '/0';
                    }
                });
            }
        });
    </script>
@endsection