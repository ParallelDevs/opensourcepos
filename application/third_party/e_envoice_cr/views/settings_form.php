<?php
echo form_open('e-envoice-cr/config/save', array(
  'id' => 'info_config_form',
  'enctype' => 'multipart/form-data',
  'class' => 'form-horizontal'));
?>
<fieldset>  
  <div class="form-group form-group-sm">	
    <?php echo form_label($this->lang->line('e_envoice_cr_env'), 'environment', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
        <?php
        echo form_dropdown(array(
          'name' => 'environment',
          'id' => 'environment',
          'class' => 'form-control input-sm required',
          'value'=>$this->config->item('e_envoice_cr_env')
            ),$environments)
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">	
    <?php echo form_label($this->lang->line('e_envoice_cr_username'), 'username', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
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
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
        <?php
        echo form_input(array(
          'name' => 'password',
          'id' => 'password',
          'class' => 'form-control input-sm required',
          'value' => $this->config->item('e_envoice_cr_password')));
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">	
    <?php echo form_label($this->lang->line('e_envoice_cr_id_type'), 'id_type', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
        <?php
        echo form_dropdown(array(
          'name' => 'id_type',
          'id' => 'id_type',
          'class' => 'form-control input-sm required',
          'value'=>$this->config->item('e_envoice_cr_id_type'),
            ),$id_types)
        ?>
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">	
    <?php echo form_label($this->lang->line('e_envoice_cr_id'), 'company_id', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
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
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
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
    <?php echo form_label($this->lang->line('e_envoice_cr_cert'), 'cert', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
          <input type="file" name="cert" size="1" />
      </div>
    </div>
  </div>
  <div class="form-group form-group-sm">	
    <?php echo form_label($this->lang->line('e_envoice_cr_cert_password'), 'cert_password', array('class' => 'control-label col-xs-2 required')); ?>
    <div class="col-xs-6">
      <div class="input-group">
        <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-home"></span></span>
          <?php
          echo form_input(array(
            'name' => 'cert_password',
            'id' => 'cert_password',
            'class' => 'form-control input-sm required',
            'value' => $this->config->item('e_envoice_cr_cert_password')));
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
