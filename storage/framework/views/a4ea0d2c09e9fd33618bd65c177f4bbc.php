
<?php $__env->startSection('Contents'); ?>
<style>
  .overview-header {
    margin-bottom: 20px;
    padding: 26px 30px;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(120deg, #12345a, #187a8c);
    box-shadow: 0 8px 22px rgba(18, 52, 90, .16);
  }
  .overview-header h1 { margin: 0 0 7px; font-size: 27px; font-weight: 700; }
  .overview-header p { margin: 0; opacity: .85; }
  .overview-period { margin-top: 12px; font-size: 12px; opacity: .85; }
  .overview-dashboard-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 1px solid rgba(255, 255, 255, .42);
    border-radius: 10px;
    color: #fff;
    background: rgba(255, 255, 255, .12);
  }
  .overview-dashboard-icon .fa { font-size: 19px; }
  .overview-card {
    position: relative;
    min-height: 132px;
    margin-bottom: 20px;
    padding: 20px;
    overflow: hidden;
    border: 1px solid #e8edf3;
    border-radius: 11px;
    background: #fff;
    box-shadow: 0 5px 18px rgba(35, 52, 75, .06);
  }
  .overview-card .card-label { color: #65758b; font-size: 13px; }
  .overview-card .card-value { margin: 8px 0 4px; color: #1d2f45; font-size: 31px; font-weight: 700; }
  .overview-card .card-note { color: #8b98a9; font-size: 12px; }
  .overview-card .card-icon { position: absolute; top: 19px; right: 20px; color: #dbe7f2; font-size: 29px; }
  .overview-card.card-blue { border-top: 3px solid #337ab7; }
  .overview-card.card-green { border-top: 3px solid #21a179; }
  .overview-card.card-orange { border-top: 3px solid #e89b36; }
  .overview-card.card-red { border-top: 3px solid #d45d65; }
  .overview-card.card-cyan { border-top: 3px solid #45b7d1; }
  .overview-card.card-slate { border-top: 3px solid #9aabb9; }
  .overview-card .card-footer { margin: 13px -20px -20px; padding: 9px 12px; color: #fff; text-align: center; }
  .overview-card.card-green .card-footer { background: #21a179; }
  .overview-card.card-orange .card-footer { background: #d88e2d; }
  .overview-card.card-red .card-footer { background: #c64c58; }
  .overview-card.card-blue .card-footer { background: #337ab7; }
  .overview-card.card-cyan .card-footer { background: #36a6c3; }
  .overview-card.card-slate .card-footer { background: #8194a3; }
  .overview-panel {
    position: relative;
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid #e3eaf2;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 6px 20px rgba(35, 52, 75, .07);
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
  }
  .overview-panel::before {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #2878b5, #26a17b, #36a6b8);
    content: '';
  }
  .overview-panel:hover {
    border-color: #cfdeeb;
    box-shadow: 0 10px 26px rgba(35, 52, 75, .11);
    transform: translateY(-2px);
  }
  .overview-panel-heading {
    padding: 18px 20px 13px;
    border-bottom: 1px solid #f0f3f7;
    background: linear-gradient(180deg, #fbfdff, #fff);
  }
  .overview-panel-heading h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: #24364b;
    font-size: 16px;
    font-weight: 700;
  }
  .overview-title-icon {
    display: inline-flex;
    flex: 0 0 32px;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 9px;
    font-size: 14px;
  }
  .overview-title-icon.icon-blue { color: #2878b5; background: #eaf3fb; }
  .overview-title-icon.icon-green { color: #218866; background: #e8f6f0; }
  .overview-title-icon.icon-orange { color: #c27b1d; background: #fff4e3; }
  .overview-title-icon.icon-red { color: #c6535e; background: #fcebed; }
  .overview-title-icon.icon-cyan { color: #238da1; background: #e6f6f8; }
  .overview-title-icon.icon-purple { color: #775da6; background: #f0ebf8; }
  .overview-panel-heading p { margin: 5px 0 0; color: #8b98a9; font-size: 12px; }
  .overview-panel-body { padding: 15px 20px 20px; }
  .overview-chart { position: relative; height: 240px; }
  .overview-chart.chart-tall { height: 240px; }
  .overview-list { margin: 0; }
  .overview-list li { padding: 10px 0; border-bottom: 1px solid #eef1f5; }
  .overview-list li:last-child { border-bottom: 0; }
  .overview-list .text-muted { font-size: 12px; }
  .overview-empty { padding: 25px 10px; color: #8b98a9; text-align: center; }
  .overview-location { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eef1f5; }
  .overview-location:last-child { border-bottom: 0; }
  .overview-location strong { color: #24364b; font-size: 20px; }
  .chart-clickable { cursor: pointer; }
  .chart-detail-error { color: #a94442; padding: 12px; }
  @media (max-width: 767px) {
    .overview-header { padding: 22px; }
    .overview-card { min-height: 120px; padding: 16px; }
    .overview-chart.chart-tall { height: 200px; }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Dashboard
      <small>employee statistics</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-calendar"></i> <?php date_default_timezone_set("Asia/Jakarta");echo date('l, d-M-Y H:i:s');?></a></li>
    </ol>
  </section>
  <section class="content">

    <div class="row">
      <div class="col-xs-6 col-sm-4 col-lg-2">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo e(number_format($permanentPercentage, 2)); ?><sup style="font-size: 20px">%</sup></h3>

            <p><?php echo e(number_format($permanentCount)); ?> employees</p>
          </div>
          <div class="icon">
            <i class="ion ion-person"></i>
          </div>
          <a href="#" class="small-box-footer">Permanent</a>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 col-lg-2">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo e(number_format($contractPercentage, 2)); ?><sup style="font-size: 20px">%</sup></h3>

            <p><?php echo e(number_format($contractCount)); ?> employees</p>
          </div>
          <div class="icon">
            <i class="ion ion-person"></i>
          </div>
          <a href="#" class="small-box-footer">Contract</a>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 col-lg-2">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo e(number_format($internPercentage, 2)); ?><sup style="font-size: 20px">%</sup></h3>

            <p><?php echo e(number_format($internCount)); ?> interns</p>
          </div>
          <div class="icon">
            <i class="ion ion-person"></i>
          </div>
          <a href="#" class="small-box-footer">
            Magang
          </a>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 col-lg-2">
        <div class="small-box bg-primary">
          <div class="inner">
            <h3><?php echo e(number_format($coreEmployeeCount)); ?> <sup style="font-size: 20px">person</sup></h3>

            <p>&nbsp;</p>
          </div>
          <div class="icon">
            <i class="ion ion-person"></i>
          </div>
          <a href="#" class="small-box-footer">SAI Employee</a>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 col-lg-2">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo e(number_format($otherCount)); ?> <sup style="font-size: 20px">person</sup></h3>

            <p>&nbsp;</p>
          </div>
          <div class="icon">
            <i class="ion ion-person"></i>
          </div>
          <a href="#" class="small-box-footer">SAB &amp; Adyawinsa</a>
        </div>
      </div>
      <div class="col-xs-6 col-sm-4 col-lg-2">
        <div class="small-box bg-info">
          <div class="inner">
            <h3><?php echo e(number_format($psabCount)); ?> <sup style="font-size: 20px">person</sup></h3>

            <p>&nbsp;</p>
          </div>
          <div class="icon">
            <i class="ion ion-person"></i>
          </div>
          <a href="#" class="small-box-footer" style="color: #000;">PSAB</a>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="overview-panel">
          <div class="overview-panel-heading"><h3><i class="fa fa-map-marker overview-title-icon icon-green" aria-hidden="true"></i>Karawang vs Outside</h3><p>All core employees; unrecorded addresses shown separately</p></div>
          <div class="overview-panel-body">
            <div class="overview-chart"><canvas id="locationChart"></canvas></div>
            <?php if($addressTotal === 0): ?>
              <div class="overview-empty">No active addresses have been recorded.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="overview-panel">
          <div class="overview-panel-heading">
            <h3><i class="fa fa-line-chart overview-title-icon icon-orange" aria-hidden="true"></i>Contract Projection</h3>
            <p>Contracts and internships ending each month through <?php echo e($contractProjectionEnd->format('M Y')); ?></p>
          </div>
          <div class="overview-panel-body">
            <div class="overview-chart"><canvas id="contractProjectionChart"></canvas></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="overview-panel">
          <div class="overview-panel-heading">
            <h3><i class="fa fa-calendar overview-title-icon icon-cyan" aria-hidden="true"></i>Length of Service</h3>
            <p>Core employees by service-length range · average <?php echo e($averageServiceYears); ?> years</p>
          </div>
          <div class="overview-panel-body"><div class="overview-chart"><canvas id="serviceChart"></canvas></div></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="overview-panel">
          <div class="overview-panel-heading">
            <h3><i class="fa fa-users overview-title-icon icon-purple" aria-hidden="true"></i>Employee Generations</h3>
            <p>Boomers, Gen X, Gen Y, Gen Z, Alpha · missing or out-of-range birth dates included</p>
          </div>
          <div class="overview-panel-body"><div class="overview-chart"><canvas id="generationChart"></canvas></div></div>
        </div>
      </div>
      <div class="col-xs-12 col-md-12 col-lg-5">
        <div class="overview-panel">
          <div class="overview-panel-heading">
            <h3><i class="fa fa-hourglass-end overview-title-icon icon-red" aria-hidden="true"></i>10-Year Retirement Projection</h3>
            <p>Active employees expected to reach retirement age (<?php echo e($retirementAge); ?>) through <?php echo e($retirementProjectionEnd->format('M d, Y')); ?></p>
          </div>
          <div class="overview-panel-body">
            <div class="overview-chart chart-tall"><canvas id="retirementProjectionChart"></canvas></div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="overview-panel">
          <div class="overview-panel-heading">
            <h3><i class="fa fa-venus-mars overview-title-icon icon-blue" aria-hidden="true"></i>Gender</h3>
            <p>Core employee distribution</p>
          </div>
          <div class="overview-panel-body"><div class="overview-chart"><canvas id="genderChart"></canvas></div></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="overview-panel">
          <div class="overview-panel-heading"><h3><i class="fa fa-map overview-title-icon icon-green" aria-hidden="true"></i>Address Distribution</h3><p>Top 10 regencies/cities + remaining and unrecorded addresses</p></div>
          <div class="overview-panel-body"><div class="overview-chart chart-tall"><canvas id="addressChart"></canvas></div></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="overview-panel">
          <div class="overview-panel-heading"><h3><i class="fa fa-sitemap overview-title-icon icon-blue" aria-hidden="true"></i>Level Distribution</h3><p>Top 10 positions + remaining and unrecorded levels</p></div>
          <div class="overview-panel-body"><div class="overview-chart chart-tall"><canvas id="positionChart"></canvas></div></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="overview-panel">
          <div class="overview-panel-heading"><h3><i class="fa fa-graduation-cap overview-title-icon icon-purple" aria-hidden="true"></i>Education</h3><p>Highest education level</p></div>
          <div class="overview-panel-body"><div class="overview-chart"><canvas id="educationChart"></canvas></div></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="overview-panel">
          <div class="overview-panel-heading"><h3><i class="fa fa-heart overview-title-icon icon-red" aria-hidden="true"></i>Marital Status</h3><p>Status code as recorded in employee data</p></div>
          <div class="overview-panel-body"><div class="overview-chart"><canvas id="maritalChart"></canvas></div></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="overview-panel">
          <div class="overview-panel-heading"><h3><i class="fa fa-book overview-title-icon icon-orange" aria-hidden="true"></i>Religion</h3><p>Based on active employee data</p></div>
          <div class="overview-panel-body"><div class="overview-chart"><canvas id="religionChart"></canvas></div></div>
        </div>
      </div>
    </div>

    <?php if(request()->user()->hasRole('hr_access')): ?>
      <div class="modal fade" id="chartEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="chartEmployeeModalTitle">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="chartEmployeeModalTitle">Employee Details</h4>
              <p class="text-muted" id="chartEmployeeModalSummary"></p>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-striped table-condensed">
                  <thead>
                    <tr><th>Employee ID</th><th>Name</th><th>Department</th><th>Position</th></tr>
                  </thead>
                  <tbody id="chartEmployeeModalBody"></tbody>
                </table>
              </div>
              <div class="chart-detail-error" id="chartEmployeeModalError" style="display:none"></div>
              <div class="text-center" id="chartEmployeeModalLoading" style="display:none">
                <i class="fa fa-spinner fa-spin"></i> Loading data...
              </div>
              <div class="clearfix">
                <button class="btn btn-default btn-sm" id="chartEmployeePrevious" type="button">Previous</button>
                <span class="text-muted" id="chartEmployeePageInfo"></span>
                <button class="btn btn-default btn-sm pull-right" id="chartEmployeeNext" type="button">Next</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
<script src="<?php echo e(asset('/assets/js/Chart.min.js')); ?>"></script>
<script>
  (function () {
    var colors = ['#2878b5', '#26a17b', '#e8a23a', '#d65c68', '#775da6', '#36a6b8', '#8c9a45', '#e07845'];
    function translateServiceLabel(label) {
      var translations = {
        'Kurang dari 1 tahun': 'Under 1 year',
        '1–5 tahun': '1–5 years',
        '6–10 tahun': '6–10 years',
        'Lebih dari 10 tahun': 'Over 10 years',
        'Tidak tersedia': 'Not available'
      };
      return translations[label] || label;
    }

    var chartData = {
      gender: <?php echo json_encode($genderDistribution, 15, 512) ?>,
      service: <?php echo json_encode(collect($serviceTime)->map(function ($total, $label) { return ['label' => $label, 'total' => $total]; })->values()) ?>.map(function (row) {
        return { label: translateServiceLabel(row.label), detailLabel: row.label, total: row.total };
      }),
      retirementProjection: <?php echo json_encode($retirementProjection, 15, 512) ?>,
      contractProjection: <?php echo json_encode($contractProjection, 15, 512) ?>,
      generation: <?php echo json_encode($generationDistribution, 15, 512) ?>,
      religion: <?php echo json_encode($religionDistribution, 15, 512) ?>,
      education: <?php echo json_encode($educationDistribution, 15, 512) ?>,
      marital: <?php echo json_encode($maritalStatusDistribution, 15, 512) ?>,
      position: <?php echo json_encode($positionDistribution, 15, 512) ?>,
      address: <?php echo json_encode($addressDistribution, 15, 512) ?>,
      location: [
        { label: 'Karawang', total: <?php echo e($karawangCount); ?> },
        { label: 'Outside', total: <?php echo e($nonKarawangCount); ?> },
        { label: 'Belum diisi', total: <?php echo e($unrecordedAddressCount); ?> }
      ]
    };
    var canViewChartDetails = <?php echo json_encode(request()->user()->hasRole('hr_access'), 15, 512) ?>;
    var chartDetailsUrl = <?php echo json_encode(route('dashboard.preview.chart-details'), 15, 512) ?>;
    var chartTitles = {
      gender: 'Gender',
      service: 'Length of Service',
      retirement_projection: 'Retirement Projection',
      contract_projection: 'Contract End Projection',
      generation: 'Employee Generations',
      religion: 'Religion',
      education: 'Education',
      marital: 'Marital Status',
      position: 'Level Distribution',
      address: 'Address Distribution',
      location: 'Karawang vs. Outside Karawang'
    };
    function showEmployeeDetails(chart, row, detail) {
      if (!canViewChartDetails) {
        return;
      }

      var modal = window.jQuery('#chartEmployeeModal');
      var title = chartTitles[chart] + ' · ' + (detail
        ? detail.contract_label + ' · ' + detail.displayLabel
        : row.label);
      var page = 1;
      var query = { chart: chart, label: row.detailLabel || row.label };

      if (chart === 'retirement_projection') {
        query.year = row.label;
      }
      if (chart === 'contract_projection') {
        query.month = detail.month;
        query.contract_name = detail.contract_name;
      }

      document.getElementById('chartEmployeeModalTitle').textContent = title;
      modal.modal('show');

      function loadPage() {
        var body = document.getElementById('chartEmployeeModalBody');
        var error = document.getElementById('chartEmployeeModalError');
        var loading = document.getElementById('chartEmployeeModalLoading');
        var params = new URLSearchParams(query);
        params.set('page', page);
        body.textContent = '';
        error.style.display = 'none';
        loading.style.display = 'block';

        fetch(chartDetailsUrl + '?' + params.toString(), {
          headers: { 'Accept': 'application/json' },
          credentials: 'same-origin'
        })
          .then(function (response) {
            if (!response.ok) {
              throw new Error('Failed to load details (' + response.status + ').');
            }
            return response.json();
          })
          .then(function (result) {
            loading.style.display = 'none';
            document.getElementById('chartEmployeeModalSummary').textContent =
              Number(result.total).toLocaleString('en-US') + ' employees';
            document.getElementById('chartEmployeePageInfo').textContent =
              'Page ' + result.current_page + ' of ' + result.last_page;
            document.getElementById('chartEmployeePrevious').disabled = result.current_page <= 1;
            document.getElementById('chartEmployeeNext').disabled = result.current_page >= result.last_page;

            if (result.data.length === 0) {
              var emptyRow = document.createElement('tr');
              var emptyCell = document.createElement('td');
              emptyCell.colSpan = 4;
              emptyCell.className = 'text-center text-muted';
              emptyCell.textContent = 'No employees in this category.';
              emptyRow.appendChild(emptyCell);
              body.appendChild(emptyRow);
              return;
            }

            result.data.forEach(function (employee) {
              var rowElement = document.createElement('tr');
              [employee.NIK, employee.employee_name, employee.dept_code, employee.position_name].forEach(function (value) {
                var cell = document.createElement('td');
                cell.textContent = value || '-';
                rowElement.appendChild(cell);
              });
              body.appendChild(rowElement);
            });
          })
          .catch(function (requestError) {
            loading.style.display = 'none';
            error.textContent = requestError.message;
            error.style.display = 'block';
          });
      }

      document.getElementById('chartEmployeePrevious').onclick = function () {
        if (page > 1) {
          page--;
          loadPage();
        }
      };
      document.getElementById('chartEmployeeNext').onclick = function () {
        page++;
        loadPage();
      };
      loadPage();
    }

    function drawChart(id, rows, type, horizontal) {
      var canvas = document.getElementById(id);
      if (!canvas || rows.length === 0) {
        return;
      }

      var dataLabelPlugin = {
        afterDatasetsDraw: function (chart) {
          if (type !== 'line' && type !== 'bar' && type !== 'doughnut') {
            return;
          }

          var context = chart.ctx;
          var dataset = chart.data.datasets[0];
          var points = chart.getDatasetMeta(0).data;
          var doughnutTotal = type === 'doughnut'
            ? dataset.data.reduce(function (total, value) { return total + Number(value); }, 0)
            : 0;

          context.save();
          context.font = 'bold 12px Arial';
          context.fillStyle = type === 'doughnut' ? '#fff' : '#24364b';

          points.forEach(function (point, index) {
            var value = Number(dataset.data[index]).toLocaleString('en-US');

            if (type === 'doughnut') {
              if (Number(dataset.data[index]) <= 0 || point._model.circumference < 0.16) {
                return;
              }

              var angle = point._model.startAngle + point._model.circumference / 2;
              var radius = (point._model.innerRadius + point._model.outerRadius) / 2;
              var x = point._model.x + Math.cos(angle) * radius;
              var y = point._model.y + Math.sin(angle) * radius;

              context.textAlign = 'center';
              context.textBaseline = 'middle';
              if (id === 'locationChart' && doughnutTotal > 0) {
                context.fillText(value, x, y - 7);
                context.fillText(
                  (Number(dataset.data[index]) / doughnutTotal * 100).toFixed(1) + '%',
                  x,
                  y + 8
                );
              } else {
                context.fillText(value, x, y);
              }
            } else if (type === 'line') {
              context.textAlign = 'center';
              context.textBaseline = 'bottom';
              context.fillText(value, point._model.x, point._model.y - 8);
            } else if (horizontal) {
              context.textAlign = 'left';
              context.textBaseline = 'middle';
              context.fillText(value, point._model.x + 7, point._model.y);
            } else {
              context.textAlign = 'center';
              context.textBaseline = 'bottom';
              context.fillText(value, point._model.x, point._model.y - 6);
            }
          });

          context.restore();
        }
      };

      if (canViewChartDetails) {
        canvas.classList.add('chart-clickable');
      }

      new Chart(canvas.getContext('2d'), {
        plugins: [dataLabelPlugin],
        type: horizontal ? 'horizontalBar' : type,
        data: {
          labels: rows.map(function (row) { return row.label; }),
          datasets: [{
            data: rows.map(function (row) { return Number(row.total); }),
            backgroundColor: type === 'line'
              ? 'rgba(40, 120, 181, 0.12)'
              : rows.map(function (row, index) { return colors[index % colors.length]; }),
            borderWidth: type === 'doughnut' ? 2 : (type === 'line' ? 2 : 0),
            borderColor: type === 'line' ? '#2878b5' : '#e8edf3',
            hoverBorderColor: '#d7e0ea',
            hoverBorderWidth: type === 'doughnut' ? 2 : undefined,
            hoverOffset: type === 'doughnut' ? 5 : 0,
            fill: type === 'line',
            lineTension: 0.25,
            pointRadius: type === 'line' ? 2 : 0,
            pointBackgroundColor: '#2878b5'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              top: type === 'line' || (type === 'bar' && !horizontal) ? 18 : 0,
              right: type === 'bar' && horizontal ? 32 : 0
            }
          },
          legend: { display: type === 'doughnut', position: 'bottom' },
          tooltips: { callbacks: {
            label: function (item, data) {
              var label = data.labels[item.index] || '';
              var value = data.datasets[0].data[item.index] || 0;
              return label + ': ' + Number(value).toLocaleString('en-US');
            }
          } },
          onClick: function (event, activeElements) {
            if (!canViewChartDetails || activeElements.length === 0) {
              return;
            }

            var index = activeElements[0]._index;
            showEmployeeDetails(id === 'retirementProjectionChart' ? 'retirement_projection' : id.replace('Chart', ''), rows[index]);
          },
          scales: (type === 'bar' || type === 'line') ? {
            xAxes: horizontal ? [{ ticks: { beginAtZero: true, precision: 0 } }] : [{ gridLines: { display: false } }],
            yAxes: horizontal ? [] : [{ ticks: { beginAtZero: true, precision: 0 } }]
          } : {}
        }
      });
    }

    drawChart('genderChart', chartData.gender, 'doughnut', false);
    drawChart('serviceChart', chartData.service, 'bar', true);
    drawChart('retirementProjectionChart', chartData.retirementProjection, 'line', false);
    drawContractProjectionChart();
    drawChart('generationChart', chartData.generation, 'bar', true);
    drawChart('religionChart', chartData.religion, 'bar', true);
    drawChart('educationChart', chartData.education, 'bar', true);
    drawChart('maritalChart', chartData.marital, 'bar', false);
    drawChart('positionChart', chartData.position, 'bar', true);
    drawChart('addressChart', chartData.address, 'bar', true);
    drawChart('locationChart', chartData.location, 'doughnut', false);

    function drawContractProjectionChart() {
      var canvas = document.getElementById('contractProjectionChart');
      var projection = chartData.contractProjection;
      if (!canvas || !projection.labels.length) {
        return;
      }

      if (canViewChartDetails) {
        canvas.classList.add('chart-clickable');
      }

      new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
          labels: projection.displayLabels,
          datasets: projection.datasets.map(function (dataset) {
            return {
              label: dataset.contract_name === 'Kontrak' ? 'Contract' : 'Internship',
              data: dataset.data,
              borderColor: dataset.borderColor,
              backgroundColor: dataset.backgroundColor,
              borderWidth: 2,
              fill: false,
              lineTension: 0.25,
              pointRadius: 2,
              pointHoverRadius: 6
            };
          })
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: { padding: { top: 18 } },
          legend: { display: true, position: 'bottom' },
          tooltips: { mode: 'index', intersect: false },
          onClick: function (event, activeElements) {
            if (!canViewChartDetails || activeElements.length === 0) {
              return;
            }

            var active = activeElements[0];
            var dataset = projection.datasets[active._datasetIndex];
            var index = active._index;
            showEmployeeDetails('contract_projection', {
              label: (dataset.contract_name === 'Kontrak' ? 'Contract' : 'Internship') + ' · ' + projection.displayLabels[index]
            }, {
              month: projection.labels[index],
              displayLabel: projection.displayLabels[index],
              contract_name: dataset.contract_name,
              contract_label: dataset.contract_name === 'Kontrak' ? 'Contract' : 'Internship'
            });
          },
          scales: {
            xAxes: [{ gridLines: { display: false } }],
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
          }
        },
        plugins: [{
          afterDatasetsDraw: function (chart) {
            var context = chart.ctx;
            context.save();
            context.font = 'bold 12px Arial';
            context.textAlign = 'center';
            context.textBaseline = 'bottom';
            context.fillStyle = '#24364b';

            chart.data.datasets.forEach(function (dataset, datasetIndex) {
              chart.getDatasetMeta(datasetIndex).data.forEach(function (point, index) {
                context.fillText(Number(dataset.data[index]).toLocaleString('en-US'), point._model.x, point._model.y - 7);
              });
            });

            context.restore();
          }
        }]
      });
    }
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\dashboard\preview.blade.php ENDPATH**/ ?>