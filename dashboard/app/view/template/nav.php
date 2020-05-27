  <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <a class="navbar-brand mr-1" href="../dashboard/index.php">Integração</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search -->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
      <div class="input-group">
        &nbsp;&nbsp; | &nbsp;&nbsp;
        <span class="navbar-brand mr-1"> <?php echo '<strong>Ambiente</strong>: '.  $ambiente; ?> </span>
        &nbsp;&nbsp; | &nbsp;&nbsp;
        <span class="navbar-brand mr-1"> <?php echo '<strong>Data</strong>: '.  date("d/m/Y H:i"); ?> </span>
      </div>
    </form>



  </nav>