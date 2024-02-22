@extends('adminlte::page')

@section('title', 'Estadisticas')

@section('plugins.Chartjs', true)

@section('content_header')
    <h1>Estadisticas</h1>
@stop

@section('content')
    <form>
        <div class="row">
            <div class="col-lg-5">
                <label for="fechaInicio" class="form-label">Fecha y hora inicial</label>
                <input type="datetime-local" class="form-control" id="fechaInicio" name="fechaInicio">
            </div>
            <div class="col-lg-5">
                <label for="fechaFin" class="form-label">Fecha y hora final</label>
                <input type="datetime-local" class="form-control" id="fechaFin" name="fechaFin">
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

                <section class="col-lg-7 connectedSortable ui-sortable">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Estados de cargue: </h3>
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
                                        <td>700</td>
                                        <td>
                                            <div class="progress progress-xs progress-striped active">
                                                <div class="progress-bar bg-primary" style="width: 30%"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary">30%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Leidos</td>
                                        <td>500</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-warning" style="width: 70%"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-warning">70%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Fallidos</td>
                                        <td>400</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" style="width: 55%"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-danger">55%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>1600</td>
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


                <section class="col-lg-5 connectedSortable ui-sortable">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Confirmación mensaje:</h3>
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
    <br>
    <h1>Pusher Test</h1>
    <p>
        Try publishing an event to channel <code>my-channel</code>
        with event name <code>my-event</code>.
    </p>
@stop

@section('css')
@stop

@section('js')
    <script>
        //-------------
        //- DONUT CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
        var donutData = {
            labels: [
                'Enviados',
                'Leidos',
                'Fallidos',
            ],
            datasets: [{
                data: [700, 500, 400],
                backgroundColor: ['#00c0ef', '#f39c12', '#f56954'],
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
    </script>
@stop
