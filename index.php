<?php require_once('db-connect.php') ?>
<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.9/index.global.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.9/index.global.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.9/index.global.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/es.global.js'></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
        <title>Calendario Universitario</title>        
    </head>    
    <body>
        <?php
        $schedules = $conn->query("SELECT * FROM eventos");
        $sched_res = [];
        foreach ($schedules->fetch_all(MYSQLI_ASSOC) as $row) {
            $row['sdate'] = date("F d, Y h:i A", strtotime($row['start']));
            $row['edate'] = date("F d, Y h:i A", strtotime($row['end']));
            $sched_res[$row['id']] = $row;
        }
        ?>        
        <script>
            var scheds = $.parseJSON('<?= json_encode($sched_res) ?>');
            var calendar;
            var Calendar = FullCalendar.Calendar;
            var events = [];
            $(function () {
                if (!!scheds) {
                    Object.keys(scheds).map(k => {
                        var row = scheds[k];
                        events.push({id: row.id, title: row.title, start: row.start, end: row.end});
                    });
                }
                var date = new Date();
                var d = date.getDate(),
                        m = date.getMonth(),
                        y = date.getFullYear();
                calendar = new Calendar(document.getElementById('calendar'), {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    timeZone: 'America/Argentina/Buenos_Aires',
                    headerToolbar: {
                        left: 'today prev,next',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek,dayGridDay timeGridWeek,timeGridDay'
                    },
                    selectable: true,
                    events: events,
                    eventClick: function (info) {
                        var _details = $('#event-details-modal');
                        var id = info.event.id;
                        if (!!scheds[id]) {
                            _details.find('#title').text(scheds[id].title);
                            _details.find('#descripcion').text(scheds[id].descripcion);
                            _details.find('#start').text(scheds[id].sdate);
                            _details.find('#end').text(scheds[id].edate);
                            _details.find('#edit,#delete').attr('data-id', id);
                            _details.modal('show');
                        } else {
                            alert("El evento no está definido");
                        }
                    },
                    editable: true
                });
                calendar.render();
                $('#evento-form').on('reset', function () {
                    $(this).find('input:hidden').val('');
                    $(this).find('input:visible').first().focus();
                });
                $('#edit').click(function () {
                    var id = $(this).attr('data-id');
                    if (!!scheds[id]) {
                        var _form = $("#evento-form");
                        console.log(String(scheds[id].start), String(scheds[id].start).replace(" ", "\\t"));
                        _form.find('[name="id"]').val(id);
                        _form.find('[name="title"]').val(scheds[id].title);
                        _form.find('[name="descripcion"]').val(scheds[id].descripcion);
                        _form.find('[name="start"]').val(String(scheds[id].start).replace(" ", "T"));
                        _form.find('[name="end"]').val(String(scheds[id].end).replace(" ", "T"));
                        $('#event-details-modal').modal('hide');
                        _form.find('[name="title"]').focus();
                    } else {
                        alert("El evento no está definido");
                    }
                });
                $('#delete').click(function () {
                    var id = $(this).attr('data-id');
                    if (!!scheds[id]) {
                        var _conf = confirm("Confirma eliminación del Evento?");
                        if (_conf === true) {
                            location.href = "./eliminar_evento.php?id=" + id;
                        }
                    } else {
                        alert("El evento no está definido");
                    }
                });
            });
        </script>
        <nav class="navbar navbar-expand-lg navbar-light bg-info">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Calendario Universitario</span>
            </div>
        </nav>
        <br>
        <div class="d-flex">
            <div class="card col-sm-2">
                <div class="card-body">
                    <form action="agregar_evento.php" method="post" id="evento-form">
                        <h3 class="text-center bg-light">Crear Evento Nuevo</h3>                        
                        <input type="hidden" name="id" value="">                        
                        <div class="form-group mb-2">
                            <label for="title" class="control-label">Título</label>
                            <input type="text" class="form-control form-control-sm rounded-0" name="title" id="title" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="descripcion" class="control-label">Descripción</label>
                            <textarea rows="3" class="form-control form-control-sm rounded-0" name="descripcion" id="descripcion" required></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label for="start" class="control-label">Inicio</label>
                            <input type="datetime-local" class="form-control form-control-sm rounded-0" name="start" id="start" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="end" class="control-label">Fin</label>
                            <input type="datetime-local" class="form-control form-control-sm rounded-0" name="end" id="end" required>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary btn-sm rounded-0" type="submit" form="evento-form"><i class="fa fa-save"></i> Guardar</button>
                            <button class="btn btn-default border btn-sm rounded-0" type="reset" form="evento-form"><i class="fa fa-reset"></i> Cancelar</button>
                        </div>                        
                    </form>
                </div>
            </div>            
            <div class="col-sm-6 p-2">
                <div id="calendar"></div>
            </div>

            <div class="col-sm-4">
                <table class="table table-hover table-bordered border-primary" method="POST">
                    <h3 class="text-center bg-light">Lista de Eventos</h3>
                    <thead class="table-secondary">
                        <tr>
                            <th>TITULO</th>
                            <th>DESCRIPCION</th>
                            <th>INICIO</th>
                            <th>FIN</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = $conn->query("select * from eventos");
                        while ($datos = $sql->fetch_object()) {
                            ?>
                            <tr>                            
                                <td><?= $datos->title ?></td>
                                <td><?= $datos->descripcion ?></td>
                                <td><?= $datos->start ?></td>
                                <td><?= $datos->end ?></td>                            
                            </tr>
                        <?php }
                        ?>                        
                    </tbody>
                </table>
            </div>            
        </div>

        <div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="event-details-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0">
                    <div class="modal-header rounded-0">
                        <h5 class="modal-title">Detalles de evento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body rounded-0">
                        <div class="container-fluid">
                            <dl>
                                <dt class="text-muted">Título</dt>
                                <dd id="title" class="fw-bold fs-4"></dd>
                                <dt class="text-muted">Descripción</dt>
                                <dd id="descripcion" class=""></dd>
                                <dt class="text-muted">Inicio</dt>
                                <dd id="start" class=""></dd>
                                <dt class="text-muted">Fin</dt>
                                <dd id="end" class=""></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="modal-footer rounded-0">
                        <div class="text-end">
                            <button type="button" class="btn btn-primary btn-sm rounded-0" id="edit" data-id="">Editar</button>
                            <button type="button" class="btn btn-danger btn-sm rounded-0" id="delete" data-id="">Eliminar</button>
                            <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
    </body>        
</html>
