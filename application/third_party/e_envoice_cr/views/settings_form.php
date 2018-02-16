<?php
echo form_open("e_envoice_cr/save_settings", array(
  'id' => 'e_envoice_cr_settings_form',
  'enctype' => 'multipart/form-data',
  'class' => 'form-horizontal'));
?>
<fieldset>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_env'), 'environment', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-cloud"></span></span>
        <?php
        echo form_dropdown(array(
          'name' => 'environment',
          'id' => 'environment',
          'class' => 'form-control input-sm required',
            ),$environments,array($this->config->item('e_envoice_cr_env')))
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_username'), 'username', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_input(array(
          'name' => 'username',
          'id' => 'username',
          'class' => 'form-control input-sm required',
          'value' => $this->config->item('e_envoice_cr_username')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_password'), 'password', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
        <?php
        echo form_input(array(
          'name' => 'password',
          'id' => 'password',
          'class' => 'form-control input-sm required',
          'type' => 'password'));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_id_type'), 'id_type', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_dropdown(array(
          'name' => 'id_type',
          'id' => 'id_type',
          'class' => 'form-control input-sm required',
            ),$id_types, array($this->config->item('e_envoice_cr_id_type')))
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_id'), 'company_id', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_input(array(
          'name' => 'company_id',
          'id' => 'company_id',
          'class' => 'form-control input-sm required',
          'value' => $this->config->item('e_envoice_cr_id')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_name'), 'company_name', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
        <?php
        echo form_input(array(
          'name' => 'company_name',
          'id' => 'company_name',
          'class' => 'form-control input-sm required',
          'value' => $this->config->item('e_envoice_cr_name')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_currency_code'), 'currency_code', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-usd"></span></span>
        <?php
        echo form_input(array(
          'name' => 'currency_code',
          'id' => 'currency_code',
          'class' => 'form-control input-sm required',
          'value' => $this->config->item('e_envoice_cr_currency_code')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_cert'), 'cert', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-certificate"></span></span>
          <input type="file" name="cert" size="1" />
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">
    <?php echo form_label($this->lang->line('e_envoice_cr_cert_password'), 'cert_password', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
          <?php
          echo form_input(array(
            'name' => 'cert_password',
            'id' => 'cert_password',
            'class' => 'form-control input-sm required',
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
