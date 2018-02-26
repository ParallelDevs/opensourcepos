<?php
echo form_open("e_envoice_cr/save_settings", array(
  'id' => 'e_envoice_cr_settings_form',
  'enctype' => 'multipart/form-data',
  'class' => 'form-horizontal'));
?>
<fieldset>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_env'), 'environment', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-cloud"></span></span>
        <?php
        echo form_dropdown(array(
          'name' => 'environment',
          'id' => 'environment',
          'class' => 'form-control input-sm',
            ), $environments, array($this->config->item('e_envoice_cr_env')))
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_username'), 'username', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_input(array(
          'name' => 'username',
          'id' => 'username',
          'class' => 'form-control input-sm',
          'value' => $this->config->item('e_envoice_cr_username')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_password'), 'password', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
        <?php
        echo form_input(array(
          'name' => 'password',
          'id' => 'password',
          'class' => 'form-control input-sm',
          'type' => 'password'));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_id_type'), 'id_type', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_dropdown(array(
          'name' => 'id_type',
          'id' => 'id_type',
          'class' => 'form-control input-sm',
            ), $id_types, array($this->config->item('e_envoice_cr_id_type')))
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_id'), 'company_id', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_input(array(
          'name' => 'company_id',
          'id' => 'company_id',
          'class' => 'form-control input-sm',
          'value' => $this->config->item('e_envoice_cr_id')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_commercial_name'), 'company_name', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_input(array(
          'name' => 'company_name',
          'id' => 'company_name',
          'class' => 'form-control input-sm',
          'value' => $this->config->item('e_envoice_cr_commercial_name')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_address'), '', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-cloud"></span></span>
        <?php
        echo form_dropdown(array(
          'name' => 'province',
          'id' => 'province',
          'class' => 'form-control input-sm',
            ), $provinces, array($this->config->item('e_envoice_cr_address_province')))
        ?>
        <?php
        echo form_dropdown(array(
          'name' => 'canton',
          'id' => 'canton',
          'class' => 'form-control input-sm',
            ), $cantones, array($this->config->item('e_envoice_cr_address_canton')))
        ?>
        <?php
        echo form_dropdown(array(
          'name' => 'distrit',
          'id' => 'distrit',
          'class' => 'form-control input-sm',
            ), $distrits, array($this->config->item('e_envoice_cr_address_distrit')))
        ?>
        <?php
        echo form_dropdown(array(
          'name' => 'neighborhood',
          'id' => 'neighborhood',
          'class' => 'form-control input-sm',
            ), $neighborhoods, array($this->config->item('e_envoice_cr_address_neighborhood')))
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_resolution_number'), 'resolution_number', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
        <?php
        echo form_input(array(
          'name' => 'resolution_number',
          'id' => 'resolution_number',
          'class' => 'form-control input-sm',
          'value' => $this->config->item('e_envoice_cr_resolution_number')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_resolution_date'), 'resolution_date', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
        <?php
        echo form_input(array(
          'name' => 'resolution_date',
          'id' => 'resolution_date',
          'class' => 'form-control input-sm',
          'value' => $this->config->item('e_envoice_cr_resolution_date')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_cert'), 'cert', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-certificate"></span></span>
        <input type="file" id="cert" name="cert" size="1" accept=".p12"/>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_cert_password'), 'cert_password', array('class' => 'control-label col-xs-2 ')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
          <?php
          echo form_input(array(
            'name' => 'cert_password',
            'id' => 'cert_password',
            'class' => 'form-control input-sm ',
            'type' => 'password'));
          ?>
      </div>
    </div>
  </div>
  <?php
  echo form_submit(array(
    'name' => 'submit_form',
    'id' => 'submit_form',
    'value' => $this->lang->line('common_submit'),
    'class' => 'btn btn-primary btn-sm pull-right'));
  ?>
</fieldset>
<?php echo form_close(); ?>
<script type="text/javascript">
  $(document).ready(function () {
    $('select[name="province"]').on('change', function () {
      var provinceCode = $(this).val();

      if (provinceCode) {
        $.ajax({
          url: '/e_envoice_cr/address_canton',
          type: "GET",
          dataType: "json",
          data: {'province_code': provinceCode},
          success: function (data) {
            $('select[name="canton"]').empty();
            $('select[name="distrit"]').empty();
            $('select[name="neighborhood"]').empty();
            $.each(data, function (key, value) {
              $('select[name="canton"]').append('<option value="' + value.code + '">' + value.name + '</option>');
            });
          }
        });

      } else {
        $('select[name="canton"]').empty();
        $('select[name="distrit"]').empty();
        $('select[name="neighborhood"]').empty();
      }
    });

    $('select[name="canton"]').on('change', function () {
      var provinceId = $('select[name="province"]').val();
      var cantonId = $(this).val();

      if (provinceId && cantonId) {
        $.ajax({
          url: '/e_envoice_cr/address_distrit',
          type: "GET",
          dataType: "json",
          data: {
            'province_code': provinceId,
            'canton_code': cantonId
          },
          success: function (data) {
            $('select[name="distrit"]').empty();
            $('select[name="neighborhood"]').empty();
            $.each(data, function (key, value) {
              $('select[name="distrit"]').append('<option value="' + value.code + '">' + value.name + '</option>');
            });
          }
        });

      } else {
        $('select[name="distrit"]').empty();
        $('select[name="neighborhood"]').empty();
      }
    });

    $('select[name="distrit"]').on('change', function () {
      var provinceId = $('select[name="province"]').val();
      var cantonId = $('select[name="canton"]').val();
      var distritId = $(this).val();

      if (provinceId && cantonId && distritId) {
        $.ajax({
          url: '/e_envoice_cr/address_neighborhood',
          type: "GET",
          dataType: "json",
          data: {
            'province_code': provinceId,
            'canton_code': cantonId,
            'distrit_code': distritId
          },
          success: function (data) {
            $('select[name="neighborhood"]').empty();
            $.each(data, function (key, value) {
              $('select[name="neighborhood"]').append('<option value="' + value.code + '">' + value.name + '</option>');
            });
          }
        });

      } else {
        $('select[name="neighborhood"]').empty();
      }
    });
  });

</script>
