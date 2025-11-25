<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php include("views/partials/header.php"); ?>
<link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/homeEditor.css">
<link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/gestionarPreguntas.css">
<style>
  .sug-page {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
    color: #eceff4;
  }

  .sug-page h1 {
    font-size: 1.9rem;
    margin: 0 0 18px;
    font-weight: 600;
  }

  .sug-subbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
  }

  .sug-subbar .actions {
    display: flex;
    gap: 14px;
  }

  .btn-volver {
    background: #2d3748;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: .85rem;
    transition: .25s;
  }

  .btn-volver:hover {
    background: #4a5568;
  }

  .sug-table-wrapper {
    background: rgba(255, 255, 255, .04);
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 12px;
    padding: 16px 18px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .25);
  }

  table.sug-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .8rem;
  }

  table.sug-table th {
    text-align: left;
    padding: 10px 8px;
    font-weight: 600;
    background: rgba(255, 255, 255, .06);
    border-bottom: 1px solid rgba(255, 255, 255, .12);
  }

  table.sug-table td {
    padding: 10px 8px;
    border-bottom: 1px solid rgba(255, 255, 255, .07);
  }

  table.sug-table tr:last-child td {
    border-bottom: none;
  }

  .estado-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: .65rem;
    font-weight: 600;
    letter-spacing: .5px;
    background: #ffb347;
    color: #222;
  }

  .acciones form {
    display: inline-block;
  }

  .btn-accion {
    background: #2563eb;
    border: none;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: .7rem;
    cursor: pointer;
    margin-right: 4px;
    transition: .25s;
  }

  .btn-accion:hover {
    background: #1d4ed8;
  }

  .btn-rechazar {
    background: #dc2626;
  }

  .btn-rechazar:hover {
    background: #b91c1c;
  }

  .flash-ok,
  .flash-err {
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: .8rem;
    font-weight: 500;
  }

  .flash-ok {
    background: #164e31;
    color: #c6f6d5;
    border: 1px solid #2f855a;
  }

  .flash-err {
    background: #63171b;
    color: #fed7d7;
    border: 1px solid #c53030;
  }

  .empty-msg {
    padding: 38px 10px;
    text-align: center;
    color: #a0aec0;
    font-size: .9rem;
  }

  .col-pregunta {
    max-width: 280px;
  }

  .col-resp {
    max-width: 160px;
  }

  .truncate {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .panel-divider {
    height: 1px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, .25), rgba(255, 255, 255, 0));
    margin: 20px 0;
  }
</style>
<div class="sug-page">
  <h1>Sugerencias Pendientes</h1>
  <div class="sug-subbar">
    <div class="info">
      <span style="font-size:.85rem;color:#a0aec0;">Revisa y modera los aportes de la comunidad.</span>
    </div>
    <div class="actions">
      <a class="btn-volver" href="/trabajoPreguntasYRespuestas/editor/gestionarPreguntas">Volver a Preguntas</a>
    </div>
  </div>
  <?php if (!empty($flashOk)): ?><div class="flash-ok"><?= htmlspecialchars($flashOk) ?></div><?php endif; ?>
  <?php if (!empty($flashErr)): ?><div class="flash-err"><?= htmlspecialchars($flashErr) ?></div><?php endif; ?>
  <div class="sug-table-wrapper">
    <?php if (empty($pendientes)): ?>
      <div class="empty-msg">No hay sugerencias pendientes.</div>
    <?php else: ?>
      <table class="sug-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Categoría</th>
            <th class="col-pregunta">Pregunta</th>
            <th class="col-resp">Resp 1</th>
            <th class="col-resp">Resp 2</th>
            <th class="col-resp">Resp 3</th>
            <th class="col-resp">Resp 4</th>
            <th>Correcta</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendientes as $p): ?>
            <tr>
              <td><?= (int)$p['id'] ?></td>
              <td><?= htmlspecialchars($p['nombre_usuario']) ?></td>
              <td><?= htmlspecialchars($p['categoria_nombre']) ?></td>
              <td><span class="truncate" title="<?= htmlspecialchars($p['pregunta']) ?>"><?= htmlspecialchars($p['pregunta']) ?></span></td>
              <td><span class="truncate" title="<?= htmlspecialchars($p['respuesta_1']) ?>"><?= htmlspecialchars($p['respuesta_1']) ?></span></td>
              <td><span class="truncate" title="<?= htmlspecialchars($p['respuesta_2']) ?>"><?= htmlspecialchars($p['respuesta_2']) ?></span></td>
              <td><span class="truncate" title="<?= htmlspecialchars($p['respuesta_3']) ?>"><?= htmlspecialchars($p['respuesta_3']) ?></span></td>
              <td><span class="truncate" title="<?= htmlspecialchars($p['respuesta_4']) ?>"><?= htmlspecialchars($p['respuesta_4']) ?></span></td>
              <td><?= (int)$p['respuesta_correcta'] ?></td>
              <td><?= htmlspecialchars($p['fecha_sugerida']) ?></td>
              <td class="acciones">
                <form method="post" action="/trabajoPreguntasYRespuestas/sugerencia/aprobar">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <button type="submit" class="btn-accion">Aprobar</button>
                </form>
                <form method="post" action="/trabajoPreguntasYRespuestas/sugerencia/rechazar">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <button type="submit" class="btn-accion btn-rechazar">Rechazar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <div class="panel-divider"></div>
  <p style="font-size:.7rem;color:#718096;">Al aprobar una sugerencia se incorpora inmediatamente al banco activo de preguntas.</p>
</div>
<?php include("views/partials/footer.php"); ?>