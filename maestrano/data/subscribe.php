<?php

// Load library
if (!defined('MAESTRANO_ROOT')) { define("MAESTRANO_ROOT", realpath(dirname(__FILE__) . "/..")); }
require_once MAESTRANO_ROOT . '/init.php';
require_once MAESTRANO_ROOT . '/connec/init.php';

// Default  user domain
global $auth_session;
if(!isset($auth_session) || !isset($auth_session->domain_id)) {
	$auth_session->domain_id = 1;
}

try {
  if(!Maestrano::param('connec.enabled')) { return false; }

  $notification = json_decode(file_get_contents('php://input'), false);
  $entity_name = strtoupper(trim($notification->entity));
  $entity_id = $notification->id;

  error_log("Received notification = ". json_encode($notification));

  switch ($entity_name) {
    case "COMPANYS":
      $mapper = new CompanyMapper();
      $mapper->fetchConnecResource($entity_id);
      break;
		case "ORGANIZATIONS":
      $mapper = new OrganizationMapper();
      $mapper->fetchConnecResource($entity_id);
      break;
    // case "TAXCODES":
    //   $mapper = new TaxCodeMapper();
    //   $mapper->fetchConnecResource($entity_id);
    //   break;
    // case "PEOPLE":
    //   $mapper = new PersonMapper();
    //   $mapper->fetchConnecResource($entity_id);
    //   break;
    // case "ITEMS":
    //   $mapper = new ItemMapper();
    //   $mapper->fetchConnecResource($entity_id);
    //   break;
    // case "INVOICES":
    //   $mapper = new InvoiceMapper();
    //   $mapper->fetchConnecResource($entity_id);
    //   break;
    // case "PAYMENTS":
    //   $mapper = new PaymentMapper();
    //   $mapper->fetchConnecResource($entity_id);
    //   break;
  }
} catch (Exception $e) {
  error_log("Caught exception in subscribe " . json_encode($e->getMessage()));
}

?>
