<?php $this->load->view("partial/header"); ?>

<?php if($this->session->flashdata('notice')):?>   
<div class="alert alert-<?=$this->session->notice['class']?> alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <?php echo $this->session->notice['message']?>
</div>
<?php endif;?>
<?php $this->load->view("settings_form");?>
<?php $this->load->view("partial/footer"); ?>
