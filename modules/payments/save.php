<?php

//stop the direct browsing to this file - let index.php handle which files get displayed
checkLogin();

# Deal with op and add some basic sanity checking

#insert - process payment
#op=pay_selected_invoice means the user came from the print_view or manage_invoces
#op=pay_invoice means the user came from the process_paymen page

global $db_server;
global $auth_session;
if ( isset($_POST['process_payment']) ) {
  $payment = new payment();
  $payment->ac_inv_id = $_POST['invoice_id'];
  $payment->ac_amount = $_POST['ac_amount'];
  $payment->ac_notes = $_POST['ac_notes'];
  $payment->ac_date = $_POST['ac_date'];
  $payment->ac_payment_type = $_POST['ac_payment_type'];
  $result = $payment->insert();

  $saved = !empty($result) ? "true" : "false";
  if($saved =='true')
  {
    $display_block =  $LANG['save_payment_success'];

    $id = lastInsertId();
    $_POST['id'] = $id;

    // Maestrano hook - push invocie
    // $maestrano = MaestranoService::getInstance();
    // if ($maestrano->isSoaEnabled() and $maestrano->getSoaUrl()) {
    //   $mno_payment = new MnoSoaPayment($db, new MnoSoaBaseLogger());
    //   $mno_payment->send($_POST, true);
    // }

  } else {
    $display_block =  $LANG['save_payment_failure']."<br />".$sql;
  }

  $refresh_total = "<meta http-equiv='refresh' content='27;url=index.php?module=payments&view=manage' />";
}

$smarty->assign('display_block', $display_block);

$smarty -> assign('pageActive', 'payment');
$smarty -> assign('active_tab', '#money');
?>
