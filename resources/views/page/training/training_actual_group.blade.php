@extends('layouts/admin')
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
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 onclick="">
                Actual Training
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-12 col-md-10 col-xs-12">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-header">
                            <i class="fa fa-users"></i>
                            <h3 class="box-title">Training List</h3>
                            <div class="box-tools pull-right">
                                <!-- <button type="button" class="btn btn-success btn-xs form"><i class="fa fa-plus"></i> &nbsp;Add New</button> -->
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="pull-right">
                                <div class="col-lg-6">
                                    <select id="category" class="form-control pilihan">
                                        <?php 
                                        if ($type != 0) {
        echo "<option value=" . $type . ">" . $skill_type . "</option>";
    } else
        echo "<option value='0'>All Level</option>";
                                        ?>
                                        @foreach($tb_skill_type as $dt)
                                                                        <?php    if ($dt->id != $type)
                                            echo "<option value=" . $dt->id . ">" . $dt->skill_type . "</option>";?>
                                        @endforeach
                                        <?php if ($type != 0) {
        echo "<option value='0'>All Level</option>";
    }?>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <select id="category2" class="form-control pilihan">
                                        <option value="{{$category}}">{{$category}}</option>
                                        <option value="Induction">Induction</option>
                                        <option value="SkillUp">SkillUp</option>
                                        <option value="Additional">Additional</option>
                                        <option value="Competence">Competence</option>
                                        <option value="Succession">Succession</option>
                                        <option value="0">All Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="box-body" style="overflow-x: scroll;">
                            <table id="table2" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width:30px;">No</th>
                                        <th>Training Name</th>
                                        <th>Level</th>
                                        <th>Category</th>
                                        <th>Trainer</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Location</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 0;?>
                                    @foreach($tb_training_actual as $dt)
                                                                    <tr>
                                                                        <td><?php    $no++;
                                        echo $no;?></td>
                                                                        <td>{{$dt->training_name}}</td>
                                                                        <td>{{$dt->skill_type}}</td>
                                                                        <td>{{$dt->category}}</td>
                                                                        <td>{{$dt->nara_sumber}}</td>
                                                                        <td>{{$dt->tanggal_aktual}}</td>
                                                                        <td>
                                                                            <?php 
                                                                                                                    echo date('H:i', strtotime($dt->start_aktual));
                                        echo "~";
                                        echo date('H:i', strtotime($dt->finish_aktual));
                                                                                                                ?>
                                                                        </td>
                                                                        <td>
                                                                            @if($dt->in_class == 0)
                                                                                Virtual
                                                                            @else
                                                                                InClass
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <div class="pull-right">
                                                                                <a href="/Training/Actual/{{$dt->id}}" title="Participant" type="button"
                                                                                    class="participant btn btn-primary btn-xs"><i
                                                                                        class="fa fa-folder-o"></i></a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.Content -->

@endsection
@section('Scripts')
    <!-- page script Tabel-->
    <script>
        $(function () {
            $('#table2').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
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
        $(document).ready(function () {
            var table = $('#tables').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 10,
                'autoWidth': false,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
                //"iDisplayLength": 50
                //dom: 'Bfrtip',buttons: ['print']
            });

            new $.fn.dataTable.Buttons(table, {
                buttons: ['copy', 'excel', 'print']
            });

            table.buttons(0, null).container().prependTo(
                table.table().container()
            );
        });


    </script>
    <script>
        window.setTimeout(function () {
            $(".alert").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 5000);

        $('body').on("change", ".pilihan", function () {
            var category = document.getElementById('category').value;
            var category2 = document.getElementById('category2').value;
            window.location.href = "/Training/Actuals/" + category + "/" + category2;
        });

    </script>
@endsection