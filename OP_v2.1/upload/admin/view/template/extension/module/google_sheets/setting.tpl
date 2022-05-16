<?= $header; ?><?= $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-purchase" data-toggle="tooltip" title="<?= $button_save; ?>"
                class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?= $cancel; ?>" data-toggle="tooltip" title="<?= $button_cancel; ?>"
           class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?= $heading_title; ?></h1>
      <ul class="breadcrumb">
          <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a></li>
          <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
      <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php } ?>
      <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $success; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $text_edit; ?></h3>
        <div class="alert alert-warning center-block text-center" role="alert" style="margin-top: 20px;">
          <strong class="text-danger">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
              <?= $entry_save_danger; ?>
          </strong>
        </div>
      </div>
      <div class="panel-body">

        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-setting" data-toggle="tab"><?= $text_tab_setting; ?></a></li>
            <?php if (!$login_url) { ?>
              <li><a href="#tab-export" data-toggle="tab"><?= $text_tab_export; ?></a></li>
              <li><a href="#tab-import" data-toggle="tab"><?= $text_tab_import; ?></a></li>
            <?php } ?>
          <li><a href="#tab-instruction" data-toggle="tab"><?= $text_tab_instruction; ?></a></li>
        </ul>

        <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form-purchase"
              class="form-horizontal">
          <div class="tab-content">
            <div class="tab-pane active" id="tab-setting">
                <?php if ($login_url) { ?>
                  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
                    <a target="_blank" href="<?= $login_url ?>"><?= $text_login ?> </a>
                  </div>
                <?php } ?>

                <?= $tab_setting; ?>

            </div>
              <?php if (!$login_url) { ?>
                <div class="tab-pane " id="tab-export">
                    <?= $tab_export; ?>
                </div>

                <div class="tab-pane" id="tab-import">
                    <?= $tab_import; ?>
                </div>

              <?php } ?>

            <div class="tab-pane" id="tab-instruction">

                <div class="alert alert-success">
                    <a href="https://console.cloud.google.com/">https://console.cloud.google.com/</a>
                </div>
                <?php foreach ($instruction_steps as $step) { ?>
                  <div class="alert alert-info">
                      <?= $step['text']; ?>
                  </div>
              <?php if ($step['screen']){ ?>
              <img src="<?= $step['screen']; ?>" style="width: auto; height: auto;" alt="">
              <?php } ?>
              <?php } ?>
            </div>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
<script>
    $('[data-toggle="tab"]').on('click', function () {
        window.location.hash = $(this).attr('href');
    });

    if (window.location.hash) {
        $('[href="' + window.location.hash + '"]').click();
    }

</script>
<?= $footer; ?>
