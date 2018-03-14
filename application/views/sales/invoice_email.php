<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'css/invoice_email.css';?>"/>
  <?php if(isset($e_envoice_cr_data)):?>
  <style>
    #document_version {text-align: center; position: relative; top: 0; padding-bottom: 0; margin-bottom: 0;}
    #terms {page-break-before: auto; }
    .footer {display: block; text-align: center; page-break-before: auto;}
    #image {height: auto !important; }
    #header {background: none !important; color: #222222 !important;}
  </style>
  <?php endif;?>
</head>

<body>
<?php
	if(isset($error_message))
	{
		echo "<div class='alert alert-dismissible alert-danger'>".$error_message."</div>";
		exit;
	}

	// Temporarily loads the system language for _lang to print invoice in the system language rather than user defined.
	load_language(TRUE, array('sales', 'common'));
?>

<div id="page-wrap">
	<div id="header"><?php echo $this->lang->line('sales_invoice'); ?></div>
  <?php if(isset($e_envoice_cr_data)):?>
  <div id="document_version">
    <?php echo $this->lang->line('e_envoice_cr_document_version').': '.$e_envoice_cr_data['document_version'];?>
  </div>
  <?php endif;?>
	<table id="info">
		<tr>
			<td id="logo">
				<?php if($this->config->item('company_logo') != '')
				{
				?>
					<img id="image" src="<?php echo 'uploads/' . $this->config->item('company_logo'); ?>" alt="company_logo" />
				<?php
				}
				?>
			</td>
			<td id="customer-title">
				<pre><?php if(isset($customer)) { echo $customer_info; } ?></pre>
			</td>
		</tr>
		<tr>
			<td id="company-title">
				<pre><?php echo $this->config->item('company'); ?></pre>
        <?php if(isset($e_envoice_cr_data)):?>
        <div>
        <?php echo $this->lang->line('e_envoice_cr_document_id').': '.$e_envoice_cr_data['emitter_id'];?>
        </div>
        <div>
        <?php echo $this->lang->line('e_envoice_cr_commercial_name').': '.$e_envoice_cr_data['emitter_company_name'];?>
        </div>
        <div><?php echo $this->config->item('email'); ?></div>
        <div>
          <?php
              $address = ucwords(strtolower($e_envoice_cr_data['emitter_province'])).', ';
              $address .= ucwords(strtolower($e_envoice_cr_data['emitter_canton'])).', ';
              $address .= ucwords(strtolower($e_envoice_cr_data['emitter_distrit'])).', ';
              $address .= ucwords(strtolower($e_envoice_cr_data['emitter_neighborhood']));
              echo $address;
          ?>
        </div>
        <?php endif;?>
				<pre><?php echo $company_info; ?></pre>
			</td>
			<td id="meta">
				<table align="right">
				<tr>
					<td class="meta-head"><?php echo $this->lang->line('sales_invoice_number');?> </td>
					<td><div><?php echo $invoice_number; ?></div></td>
				</tr>
        <?php if(isset($e_envoice_cr_data)):?>
        <tr>
          <td class="meta-head"><?php echo $this->lang->line($e_envoice_cr_data['lang_document_name']);?></td>
          <td><?php echo $e_envoice_cr_data['document_consecutive']; ?></td>
        </tr>
        <tr>
          <td class="meta-head"><?php echo $this->lang->line('e_envoice_cr_document_sale_type');?></td>
          <td><?php echo $this->lang->line($e_envoice_cr_data['lang_document_sale_type']);?></td>
        </tr>
        <?php endif;?>
				<tr>
					<td class="meta-head"><?php echo $this->lang->line('common_date'); ?></td>
					<td><div><?php echo $transaction_time; ?></div></td>
				</tr>
				<?php
				if($amount_due > 0)
				{
				?>
					<tr>
						<td class="meta-head"><?php echo $this->lang->line('sales_amount_due'); ?></td>
						<td><div class="due"><?php echo to_currency($total); ?></div></td>
					</tr>
				<?php
				}
				?>
				</table>
			</td>
		</tr>
	</table>

	<table id="items">
		<tr>
			<th><?php echo $this->lang->line('sales_item_number'); ?></th>
			<th><?php echo $this->lang->line('sales_item_name'); ?></th>
			<th><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th><?php echo $this->lang->line('sales_price'); ?></th>
			<th><?php echo $this->lang->line('sales_discount'); ?></th>
			<th><?php echo $this->lang->line('sales_customer_discount');?></th>
			<th><?php echo $this->lang->line('sales_total'); ?></th>
		</tr>

		<?php
		foreach($cart as $line=>$item)
		{
		?>
			<tr class="item-row">
				<td><?php echo $item['item_number']; ?></td>
				<td class="item-name"><?php echo $item['name']; ?></td>
				<td><?php echo to_quantity_decimals($item['quantity']); ?></td>
				<td><?php echo to_currency($item['price']); ?></td>
				<td><?php echo $item['discount'] .'%'; ?></td>
				<td><?php echo to_currency($item['discounted_total'] / $item['quantity']); ?></td>
				<td class="total-line"><?php echo to_currency($item['discounted_total']); ?></td>
			</tr>
		<?php
		}
		?>

		<tr>
			<td colspan="7" align="center"><?php echo '&nbsp;'; ?></td>
		</tr>

		<tr>
			<td colspan="4" class="blank"> </td>
			<td colspan="2" class="total-line"><?php echo $this->lang->line('sales_sub_total'); ?></td>
			<td id="subtotal" class="total-value"><?php echo to_currency($subtotal); ?></td>
		</tr>

		<?php
		foreach($taxes as $tax_group_index=>$sales_tax)
		{
		?>
			<tr>
				<td colspan="4" class="blank"> </td>
				<td colspan="2" class="total-line"><?php echo $sales_tax['tax_group']; ?></td>
				<td id="taxes" class="total-value"><?php echo to_currency_tax($sales_tax['sale_tax_amount']); ?></td>
			</tr>
		<?php
		}
		?>

		<tr>
			<td colspan="4" class="blank"> </td>
			<td colspan="2" class="total-line"><?php echo $this->lang->line('sales_total'); ?></td>
			<td id="total" class="total-value"><?php echo to_currency($total); ?></td>
		</tr>

    <?php
		$only_sale_check = FALSE;
		$show_giftcard_remainder = FALSE;
		foreach($payments as $payment_id=>$payment)
		{
			$only_sale_check |= $payment['payment_type'] == $this->lang->line('sales_check');
			$splitpayment = explode(':', $payment['payment_type']);
			$show_giftcard_remainder |= $splitpayment[0] == $this->lang->line('sales_giftcard');
		?>
			<tr>
				<td colspan="4" class="blank"> </td>
				<td colspan="2" class="total-line"><?php echo $splitpayment[0]; ?></td>
				<td class="total-value"><?php echo to_currency( $payment['payment_amount'] * -1 ); ?></td>
			</tr>
		<?php
		}

		if(isset($cur_giftcard_value) && $show_giftcard_remainder)
		{
		?>
			<tr>
				<td colspan="4" class="blank"> </td>
				<td colspan="2" class="total-line"><?php echo $this->lang->line('sales_giftcard_balance'); ?></td>
				<td class="total-value"><?php echo to_currency($cur_giftcard_value); ?></td>
			</tr>
			<?php
		}

		if(!empty($payments))
		{
		?>
		<tr>
			<td colspan="4" class="blank"> </td>
			<td colspan="2" class="total-line"><?php echo $this->lang->line($amount_change >= 0 ? ($only_sale_check ? 'sales_check_balance' : 'sales_change_due') : 'sales_amount_due') ; ?></textarea></td>
			<td class="total-value"><?php echo to_currency($amount_change); ?></td>
		</tr>
		<?php
		}
		?>

	</table>

	<div id="terms">
		<div id="sale_return_policy">
			<h5>
				<textarea rows="5" cols="6"><?php echo nl2br($this->config->item('payment_message')); ?></textarea>
				<textarea rows="5" cols="6"><?php echo $this->lang->line('sales_comments') . ': ' . (empty($comments) ? $this->config->item('invoice_default_comments') : $comments); ?></textarea>
			</h5>
			<?php echo nl2br($this->config->item('return_policy')); ?>
		</div>
		<div id='barcode'>
			<img src='data:image/png;base64,<?php echo $barcode; ?>' /><br>
			<?php echo $sale_id; ?>
		</div>
	</div>
  <?php if(isset($e_envoice_cr_data)):?>
  <div class="footer">
    <div id="document_key"><?php echo $this->lang->line('e_envoice_cr_document_key').': '.$e_envoice_cr_data['document_key'];?></div>
    <div id="document_legend"><?php echo $e_envoice_cr_data['document_legend'];?></div>
  </div>
<?php endif;?>
</div>

</body>
</html>
