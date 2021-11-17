<div class="row">
  <div class="col-md-6">

      <?php if (isset($fields)) { ?>
          <?php foreach ($fields as $field_name => $field) { ?>
              <?php if ($field['type'] == 'input') { ?>

            <div class="form-group">
              <label class="col-sm-4 control-label"
                     for="<?= $field['type']; ?>-<?= $field_name ?>"><?= $field['label']; ?></label>
              <div class="col-sm-8">
                <input type="text" name="<?= $field_name ?>" value="<?= $field['value']; ?>"
                       id="<?= $field['type']; ?>-<?= $field_name ?>" class="form-control"/>

              </div>
            </div>
              <?php } ?>
          <?php } ?>
      <?php } ?>

  </div>
  <div class="col-md-6">
    <div class="g-5">

      <table class="f-field-set table">
        <thead>
        <td>Поле</td>
        <td>название в таблице</td>
        </thead>
        <tbody>

        <?php foreach ($parser_fields as $parser_field) { ?>
          <tr class="<?= $parser_field['disabled'] ? 'bg-info' : '' ?>">
            <td>
              <label>
                <input
                    <?= $parser_field['checked'] ? 'checked="checked"' : '' ?>
                    <?= $parser_field['disabled'] ? 'disabled="disabled" type="hidden"' : 'type="checkbox"' ?>
                         name="<?= $parser_field['name'] ?>"
                        value="1"> <?= $parser_field['label'] ?></label>
            </td>
            <td><span><?= $parser_field['name_column'] ?></span></td>
          </tr>
        <?php } ?>

        </tbody>
      </table>
      <a onclick="$(this).parent().find(':checkbox:not(:disabled)').prop('checked', true);">Выделить все</a> / <a
              onclick="$(this).parent().find(':checkbox:not(:disabled)').prop('checked', false);">Снять выделение</a>
    </div>

  </div>

</div>



