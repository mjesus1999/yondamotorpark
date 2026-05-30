
<!-- Selector de temas -->
<a href="#" class="theme-toggle">
  <i class="fa-regular fa-moon"></i>
  <i class="fa-regular fa-sun"></i>
</a>

<footer class="footer">
  <div class="container-fluid">
    <div class="row text-muted">
      <div class="col-6 text-start">
        <p class="mb-0">
          <a href="#" class="text-muted">
            <span>Motorpark Yonda</span>
          </a>
        </p>
      </div>
      <div class="col-6 text-end">
        <ul class="list-inline">
          <li class="list-inline-item">
            <a href="#" class="text-muted">Desarrollador</a>
          </li>
          <li class="list-inline-item">
            <a href="#" class="text-muted">Ayuda</a>
          </li>
          <li class="list-inline-item">
            <a href="#" class="text-muted">Novedades</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>

</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js" defer></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js" defer></script>
<script src="https://cdn.datatables.net/select/3.0.0/js/dataTables.select.js" defer></script>
<script src="https://cdn.datatables.net/select/3.0.0/js/select.bootstrap5.js" defer></script>
<script src="/assets/js/script-dashboard.js"></script>


<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/assets/js/swalcustom.js"></script>


<script>
  (() => {
    const SERVER_TIMEOUT = parseInt("<?php
      $sessionTimeout = 600;
      if (class_exists(\App\Config\Env::class)) {
        $sessionTimeout = (int) \App\Config\Env::get('SESSION_TIMEOUT', '600');
      } else {
        $sessionTimeout = (int) (getenv('SESSION_TIMEOUT') ?: 600);
      }
      echo max(60, $sessionTimeout);
    ?>", 10) || 600;
    const INTERVAL_MS = Math.max(5, Math.floor(SERVER_TIMEOUT / 2)) * 1000;
    const KEEPALIVE_URL = '/keepalive';

    // Solo correr si hay usuario logueado (para no hacer pings en pantalla pública)
    const IS_LOGGED = <?php echo !empty($_SESSION['user']) ? 'true' : 'false'; ?>;
    if (!IS_LOGGED) return;

    function sendKeepAlive() {
      if (document.hidden || !navigator.onLine) return;
      fetch(KEEPALIVE_URL, {
          method: 'GET',
          credentials: 'include',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(res => {
          if (res.status === 401) window.location.href = '/login';
          return res.json().catch(() => null);
        })
        .catch(() => {
          /* silencioso */ });
    }

    sendKeepAlive();
    setInterval(sendKeepAlive, INTERVAL_MS);
  })();
</script>


</body>

</html>