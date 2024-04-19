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
            <div class="row">

                <section class="col-lg-6 connectedSortable ui-sortable">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Exportación de reporte</h3>
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
                            <table id="resporteTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>generar</th>
                                        <th>descargar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportes as $reporte)
                                        <tr> <!-- Agregamos esta línea para definir una fila de la tabla -->
                                            <td>{{ $reporte->id }}</td>
                                            <td>{{ $reporte->fechaInicio }}</td>
                                            <td>{{ $reporte->fechaFin }}</td>
                                            <td>
                                                <a href="#"
                                                    onclick="exportarMensaje({{ $reporte->id }}); return false;"
                                                    class="btn btn-success btn-sm mb-2">
                                                    Crear
                                                </a>
                                            </td>
                                            @if ($reporte->archivo)
                                                <td>
                                                    <a href="{{ route('download', $reporte->id) }}"
                                                        class="btn btn-primary btn-sm mb-2">
                                                        <i class="fa fa-download"></i></a>
                                                </td>
                                            @else
                                                <td>
                                                    <span>
                                                        <p>esparando..</p>
                                                    </span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
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
    <script src="https://code.jquery.com/jquery-3.7.0.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        var baseUrl = "{{ url('/') }}";
        var exportUrl = "{{ route('exportar-mensajes', '_id_') }}";
        var downloadUrl = "{{ route('download', '_id_') }}";
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
                        // Actualizar el contenido de la tabla con los nuevos datos de los reportes
                        var tableBody = $('#resporteTable tbody');
                        tableBody.empty(); // Limpiar el contenido actual de la tabla
                        // Iterar sobre los nuevos reportes y agregar filas a la tabla
                        response.reportes.forEach(function(reporte) {
                            var newRow = '<tr>' +
                                '<td>' + reporte.id + '</td>' +
                                '<td>' + reporte.fechaInicio + '</td>' +
                                '<td>' + reporte.fechaFin + '</td>' +
                                '<td><a href="#" onclick="exportarMensaje(' + reporte
                                .id +
                                ')" class="btn btn-success btn-sm mb-2">crear</a></td>' +
                                (reporte.archivo ? '<td><a href="' + downloadUrl
                                    .replace('_id_', reporte.id) +
                                    '" class="btn btn-primary btn-sm mb-2"><i class="fa fa-download"></i></a></td>' :
                                    '<td><span><p>esperando...</p></span></td>') +
                                '</tr>';
                            tableBody.append(newRow);

                        });
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
                        // Mostrar el error con SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: 'Hubo un problema con la solicitud: ' + error,
                            footer: '<a href>¿Necesitas ayuda?</a>'
                        });
                        console.error(error);
                    }
                });
            });
        });
    </script>
    <script>
        new DataTable('#resporteTable');
    </script>
    <script>
        function exportarMensaje(reporteId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Iniciarás la exportación de los mensajes!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, exportar ahora!',
            }).then((result) => {
                if (result.value) {
                    console.log("Confirmación del usuario");
                    $.ajax({
                        url: 'exportar-mensajes/' + reporteId,
                        type: 'GET',
                        success: function(response) {
                            Swal.fire(
                                '¡Iniciado!',
                                'La exportación ha comenzado.',
                                'success'
                            );
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error',
                                'No se pudo iniciar la exportación: ' + error,
                                'error'
                            );
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    console.log("Cancelado por el usuario");
                    Swal.fire(
                        'Cancelado',
                        'No se inició la exportación.',
                        'info'
                    );
                }
            });
        }
    </script>
@stop
