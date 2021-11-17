

<div class="form-group">
    <label class="col-sm-2 control-label" for="input-language_import"><?= $entry_language_import; ?></label>
    <div class="col-sm-10">
        <select name="<?= $language_import_field_name ?>" id="input-language_import" class="form-control">
            <?php foreach ($languages as $language) { ?>
                <?php if ($language['code'] == $language_import) { ?>
                    <option value="<?= $language['code']; ?>" selected="selected"><?= $language['code']; ?></option>
                <?php } else { ?>
                    <option value="<?= $language['code']; ?>"><?= $language['code']; ?></option>
                <?php } ?>
            <?php } ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="input-attribute_group_id"><?= $entry_attribute_group_import; ?></label>
    <div class="col-sm-10">
        <select name="<?= $import_attribute_group_id_name ?>" id="input-attribute_group_id" class="form-control">
            <?php foreach ($attribute_groups as $attribute_group) { ?>
                <?php if ($attribute_group['attribute_group_id'] == $import_attribute_group_id) { ?>
                    <option value="<?= $attribute_group['attribute_group_id']; ?>" selected="selected"><?= $attribute_group['name']; ?></option>
                <?php } else { ?>
                    <option value="<?= $attribute_group['attribute_group_id']; ?>"><?= $attribute_group['name']; ?></option>
                <?php } ?>
            <?php } ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="input-import_update_field"><?= $entry_update_field; ?></label>
    <div class="col-sm-10">
        <select name="<?= $import_update_field_name ?>" id="input-import_update_field" class="form-control">
            <?php foreach ($fields_to_update as $field) { ?>
                <?php if ($field == $import_update_field) { ?>
                    <option value="<?= $field; ?>" selected="selected"><?= $field; ?></option>
                <?php } else { ?>
                    <option value="<?= $field; ?>"><?= $field; ?></option>
                <?php } ?>
            <?php } ?>
        </select>
    </div>
</div>

<div class="progress">
    <div class="progress-bar progress-bar-import" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
         style="min-width: 2em; width: 0%;">
        0%
    </div>
</div>
<div class="form-group">
    <div class="col-md-12">
        <a class="btn btn-warning center-block" onclick="startGenerateImport()">
            <strong>
                <i class="fa fa-play-circle-o" aria-hidden="true"></i>
                <?= $entry_import; ?>
            </strong>
        </a>
        <div class="alert alert-warning center-block text-center" role="alert" style="margin-top: 20px;">
            <strong class="text-danger">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                <?= $entry_import_danger; ?>
            </strong>
        </div>

        <div class="alert alert-logger-danger-import alert-warning center-block text-center" role="alert" style="margin-top: 20px;display: none;">
            <strong class="text-danger">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                <div class="logger-danger-import"></div>
            </strong>
        </div>

    </div>
</div>


<script type="text/javascript">

    function updateProgressImport(percentage) {
        if (percentage > 100) percentage = 100;
        $('.progress-bar-import').attr('aria-valuenow', percentage);
        $('.progress-bar-import').css('width', percentage + '%');
        $('.progress-bar-import').html(percentage + '%');
    }

    function startGenerateImport() {
        updateProgressImport(0);
        $('.logger-danger-import').html('');
        $('.alert-logger-danger-import').hide();
        generateImport(1);
    }

    function generateImport(page) {

        $.ajax({
            url: 'index.php?route=extension/module/google_sheets/import&token=<?= $token; ?>',
            dataType: 'json',
            data: 'page=' + page,
            type: 'post',
            success: function (json) {

                if (json['error']) {
                    $('.alert-logger-danger-import').show();
                    $('.logger-danger-import').append(json['error']);

                } else if (json['page']) {

                    generateImport(json['page']);
                    updateProgressImport(json['progress']);

                } else {
                    updateProgressImport(100);
                }
            }
        });

    }
</script>
