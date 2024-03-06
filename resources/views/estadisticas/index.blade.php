@extends('adminlte::page')

@section('title', 'Estadisticas')

@section('plugins.Chartjs', true)

@section('content_header')
    <h1>Estadisticas</h1>
@stop

@section('content')
    <form id="statisticsForm">
        @csrf
        <div class="row">
            <div class="col-lg-5">
                <label for="fechaInicio" class="form-label">Fecha y hora inicial</label>
                <input type="datetime-local" class="form-control" id="fechaInicio" name="fechaInicio" required>
            </div>
            <div class="col-lg-5">
                <label for="fechaFin" class="form-label">Fecha y hora final</label>
                <input type="datetime-local" class="form-control" id="fechaFin" name="fechaFin" required>
            </div>
            <div class="col-lg-2">
                <br>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </form>
    <br>
    <section class="content">
        <div class="container-fluid">

            <div class="row">

                <section class="col-lg-6 connectedSortable ui-sortable">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Estados de cargue: [<span id="startDate"></span> - <span
                                    id="endDate"></span>] </h3>
                            <div class="card-tools">
                                <a data-toggle="modal" data-target="#downloadModal" class="btn btn-tool"
                                    title="Importar">
                                    <i class="fa fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                        </div>

                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Cantidad</th>
                                        <th>Progreso</th>
                                        <th style="width: 40px"> % </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Enviados</td>
                                        <td id="sentCount">0</td>
                                        <td>
                                            <div class="progress progress-xs progress-striped active">
                                                <div id="sentProgressBar" class="progress-bar bg-primary"></div>
                                            </div>
                                        </td>
                                        <td><span id="sentPercentage" class="badge bg-primary"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Entregado</td>
                                        <td id="deliveredCount">0</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div id="deliveredProgressBar" class="progress-bar bg-secondary"></div>
                                            </div>
                                        </td>
                                        <td><span id="deliveredPercentage" class="badge bg-secondary"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Leidos</td>
                                        <td id="readCount">0</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div id="readProgressBar" class="progress-bar bg-warning"></div>
                                            </div>
                                        </td>
                                        <td><span id="readPercentage" class="badge bg-warning"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Fallidos</td>
                                        <td id="failedCount">0</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div id="failedProgressBar" class="progress-bar bg-danger"></div>
                                            </div>
                                        </td>
                                        <td><span id="failedPercentage" class="badge bg-danger"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td id="totalMessages">0</td>
                                        <td>
                                            <div class="progress progress-xs progress-striped active">
                                                <div class="progress-bar bg-success" style="width: 100%"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success">100%</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>




                </section>


                <section class="col-lg-6 connectedSortable ui-sortable">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Confirmación mensaje: [<span id="startDate2"></span> - <span
                                    id="endDate2"></span>]</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4>Total de eventos: <span id="totalEventos"></span></h4>
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div class=""></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div class=""></div>
                                </div>
                            </div>
                            <canvas id="donutChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 389px;"
                                width="486" height="312" class="chartjs-render-monitor"></canvas>
                        </div>

                    </div>
                </section>

            </div>


        </div>
    </section>
@stop

@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#statisticsForm').submit(function(event) {
                event.preventDefault(); // Evitar el envío del formulario por defecto

                var formData = $(this).serialize(); // Obtener los datos del formulario

                // Enviar una solicitud AJAX al controlador para obtener las estadísticas
                $.ajax({
                    url: '{{ route('get-statistics') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {

                        // Asignar los valores recibidos a las variables de la vista
                        var sentCount = response.sentCount;
                        var deliveredCount = response.deliveredCount;
                        var readCount = response.readCount;
                        var failedCount = response.failedCount;
                        var totalMessages = response.totalMessages;
                        var sentPercentage = response.sentPercentage;
                        var deliveredPercentage = response.deliveredPercentage;
                        var readPercentage = response.readPercentage;
                        var failedPercentage = response.failedPercentage;
                        var startDate = response.startDate;
                        var endDate = response.endDate;

                        $('#sentCount').text(sentCount);
                        $('#deliveredCount').text(deliveredCount);
                        $('#readCount').text(readCount);
                        $('#failedCount').text(failedCount);
                        $('#totalMessages').text(totalMessages);
                        $('#totalEventos').text(totalMessages);

                        $('#startDate').text(startDate);
                        $('#endDate').text(endDate);
                        $('#startDate2').text(startDate);
                        $('#endDate2').text(endDate);


                        $('#sentPercentage').text(sentPercentage + "%");
                        $('#deliveredPercentage').text(deliveredPercentage + "%");
                        $('#readPercentage').text(readPercentage + "%");
                        $('#failedPercentage').text(failedPercentage + "%");

                        $('#sentProgressBar').css('width', sentPercentage + "%");
                        $('#deliveredProgressBar').css('width', deliveredPercentage + "%");
                        $('#readProgressBar').css('width', readPercentage + "%");
                        $('#failedProgressBar').css('width', failedPercentage + "%");

                        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
                        var donutData = {
                            labels: [
                                'Enviados',
                                'Entregados',
                                'Leidos',
                                'Fallidos',
                            ],
                            datasets: [{
                                data: [sentCount, deliveredCount, readCount,
                                    failedCount
                                ],
                                backgroundColor: ['#00c0ef', '#f39c12', '#008b46',
                                    '#f50854'
                                ],
                            }]
                        }
                        var donutOptions = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }
                        //Create pie or douhnut chart
                        // You can switch between pie and douhnut using the method below.
                        new Chart(donutChartCanvas, {
                            type: 'doughnut',
                            data: donutData,
                            options: donutOptions
                        })
                    },
                    error: function(xhr, status, error) {
                        // Manejar errores de la solicitud AJAX aquí
                        console.error(error);
                    }
                });
            });
        });
    </script>
@stop
