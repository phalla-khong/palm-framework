<?php ob_start(); ?>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-2 g-4 py-5" style="max-width: 570px; margin: auto;">
    <div class="col d-flex align-items-start">
        <span style="font-size: 27px;color: Dodgerblue;width: 60px;display: inline-block;">
            <i class="fas fa-receipt"></i>
        </span>
        <div>
            <a href="/sugar">
                <h4 class="fw-bold mb-0">Sugar</h4>
            </a>
        </div>
    </div>
    <div class="col d-flex align-items-start">
        <span style="font-size: 27px;color: Dodgerblue;width: 60px;display: inline-block;">
            <i class="fas fa-chart-line"></i>
        </span>
        <div>
            <a href="/fruit">
                <h4 class="fw-bold mb-0">Fruit</h4>
            </a>
        </div>
    </div>
</div>
<div class="jumbotron jumbotron-fluid">
    <div class="container text-center">
        <h1 class="display-4">Welcome Palm</h1>
        <p class="lead">This is a simple framwork.</p>
    </div>
</div>
<?php $content = ob_get_clean(); ?>