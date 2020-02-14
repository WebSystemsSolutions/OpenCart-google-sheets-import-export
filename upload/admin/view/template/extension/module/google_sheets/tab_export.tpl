
<div class="form-group">
  <label class="col-sm-2 control-label" for="input-language_export"><?= $entry_language_export; ?></label>
  <div class="col-sm-10">
    <select name="<?= $language_export_field_name ?>" id="input-language_export" class="form-control">
        <?php foreach ($languages as $language) { ?>
            <?php if ($language['code'] == $language_export) { ?>
            <option value="<?= $language['code']; ?>" selected="selected"><?= $language['code']; ?></option>
            <?php } else { ?>
            <option value="<?= $language['code']; ?>"><?= $language['code']; ?></option>
            <?php } ?>
        <?php } ?>
    </select>
  </div>
</div>

<div class="progress">
  <div class="progress-bar progress-bar-export" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
       style="min-width: 2em; width: 0%;">
    0%
  </div>
</div>
<div class="form-group">
  <div class="col-md-12">
    <a class="btn btn-warning center-block" onclick="start_generate()">
      <strong>
        <i class="fa fa-play-circle-o" aria-hidden="true"></i>
        <?= $entry_export; ?>
      </strong>
    </a>
    <div class="alert alert-warning center-block text-center" role="alert" style="margin-top: 20px;">
      <strong class="text-danger">
        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
          <?= $entry_export_danger; ?>
      </strong>
    </div>

    <div class="alert alert-logger-danger  alert-warning center-block text-center" role="alert" style="margin-top: 20px;display: none;">
      <strong class="text-danger">
        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
        <div class="logger-danger"></div>
      </strong>
    </div>

  </div>
</div>


<script type="text/javascript">

    function updateProgress(percentage) {
        if (percentage > 100) percentage = 100;
        $('.progress-bar-export').attr('aria-valuenow', percentage);
        $('.progress-bar-export').css('width', percentage + '%');
        $('.progress-bar-export').html(percentage + '%');
    }

    function start_generate() {
        updateProgress(0);
        $('.logger-danger').html('');
        $('.alert-logger-danger').hide();
        generate('before');
    }

    function generate(page) {

        $.ajax({
            url: 'index.php?route=extension/module/google_sheets/export&token=<?= $token; ?>',
            dataType: 'json',
            data: 'page=' + page,
            type: 'post',
            success: function (json) {

                if (json['error']) {
                    $('.alert-logger-danger').show();
                    $('.logger-danger').append(json['error']);

                } else if (json['page']) {

                    generate(json['page']);
                    updateProgress(json['progress']);

                } else {
                    updateProgress(100);
                }
            }
        });

    }
</script>
