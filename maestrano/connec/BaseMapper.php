<?php

require_once 'MnoIdMap.php';

/**
* Map Connec Resource representation to/from OrangeHRM Model
* You need to extend this class an implement the following methods:
* - getId($model) Returns the SimpleInvoices entity local id
* - loadModelById($local_id) Loads the SimpleInvoices entity by its id
* - mapConnecResourceToModel($cnc_hash, $model) Maps the Connec resource to the OrangeHRM entity
* - mapModelToConnecResource($model) Maps the SimpleInvoices entity into a Connec resource
* - persistLocalModel($model) Saves the SimpleInvoices entity
* - matchLocalModel($cnc_hash) (Optional) Returns an SimpleInvoices entity matched by attributes
* - initializeNewModel() (Optional) Return a new instance of the SimpleInvoices entity
*/
abstract class BaseMapper {
  private $_connec_client;

  protected $connec_entity_name = 'Model';
  protected $local_entity_name = 'Model';

  protected $connec_resource_name = 'models';
  protected $connec_resource_endpoint = 'models';

  public function __construct() {
    $this->_connec_client = new Maestrano_Connec_Client();
  }

  // Transform a date in format 'Y-m-d' into a time in ISO format
  public function dateStringToTime($date_str) {
    $date = DateTime::createFromFormat('Y-m-d', $date_str);
    if(!$date) { return null; }
    return $date->format("c");
  }

  // Transform a time in ISO format into a date in format 'Y-m-d'
  public function timeToDateString($time_str) {
    $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $time_str);
    if(!$date) { return null; }
    return $date->format("Y-m-d");
  }

  protected function is_set($variable) {
    return (!is_null($variable) && isset($variable) && !empty($variable) && !(is_string($variable) && trim($variable)===''));
  }

  // Overwrite me!
  // Return the Model local id
  abstract protected function getId($model);

  // Overwrite me!
  // Return a local Model by id
  abstract protected function loadModelById($local_id);

  // Overwrite me!
  // Map the Connec resource attributes onto the OrangeHRM model
  abstract protected function mapConnecResourceToModel($cnc_hash, $model);

  // Overwrite me!
  // Map the OrangeHRM model to a Connec resource hash
  abstract protected function mapModelToConnecResource($model);

  // Overwrite me!
  // Persist the OrangeHRM model
  abstract protected function persistLocalModel($modell, $cnc_hash);

  // Overwrite me!
  // Optional: Match a local Model from hash attributes
  protected function matchLocalModel($cnc_hash) {
    return null;
  }

  // Overwrite me!
  // Optional: Check the hash is valid for mapping
  protected function validate($cnc_hash) {
    return true;
  }

  // Overwrite me!
  // Optional: Method called after pushing an Entity to Connec
  // Add any custom logic to map the response back to the model
  public function processConnecResponse($resource_hash, $model) {
    return $model;
  }

  // Overwrite me!
  // Optional: Returns the Connec! Resource ID. When dealing with embedded documents, ID is unique only within the embedded collection.
  // In this case it is advised to prefix the Embedded Document ID with the Parent Document ID (eg: PARENT_ID#EMBEDDED_DOCUMENT_ID)
  protected function getConnecResourceId($cnc_hash) {
    return $cnc_hash['id'];
  }

  // Overwrite me!
  // Optional: define how to initialize a new model in SimpleInvoices
  protected function initializeNewModel() {
    return new $this->local_entity_name();
  }

  public function getConnecResourceName() {
    return $this->connec_resource_name;
  }

  // Load a local Model by its Connec! id. If it does not exist locally, it is fetched from Connec! first
  public function loadModelByConnecId($entity_id) {
    error_log("load local model by connec id entity_name=$this->connec_entity_name, entity_id=$entity_id");

    if(is_null($entity_id)) { return null; }

    $mno_id_map = MnoIdMap::findMnoIdMapByMnoIdAndEntityName($entity_id, $this->connec_entity_name);
    if(!$mno_id_map) {
      // Entity does not exist locally, fetch it from Connec!
      return $this->fetchConnecResource($entity_id);
    } else {
      // Load existing entity
      return $this->loadModelById($mno_id_map['app_entity_id']);
    }
  }

  // Fetch and persist a Connec! resounce by id
  public function fetchConnecResource($entity_id) {
    error_log("fetch connec resource entity_name=$this->connec_entity_name, entity_id=$entity_id");

    $msg = $this->_connec_client->get("$this->connec_resource_endpoint/$entity_id");
    $code = $msg['code'];

    if($code != 200) {
      error_log("cannot fetch Connec! entity code=$code, entity_name=$this->connec_entity_name, entity_id=$entity_id");
    } else {
      $result = json_decode($msg['body'], true);
      error_log("processing entity_name=$this->connec_entity_name entity=". json_encode($result));
      return $this->saveConnecResource($result[$this->connec_resource_name]);
    }
    return false;
  }

  // Persist a list of Connec Resources as OrangeHRM Models
  public function persistAll($resources_hash) {
    if(!is_null($resources_hash)) {
      // If this is an associative array, map its content
      if(array_values($resources_hash) !== $resources_hash) {
        try {
          $this->saveConnecResource($resources_hash);
        } catch (Exception $e) {
          error_log("Error when processing entity=".$this->connec_entity_name.", id=".$resource_hash['id'].", message=" . $e->getMessage());
        }
      } else {
        foreach($resources_hash as $resource_hash) {
          try {
            $this->saveConnecResource($resource_hash);
          } catch (Exception $e) {
            error_log("Error when processing entity=".$this->connec_entity_name.", id=".$resource_hash['id'].", message=" . $e->getMessage());
          }
        }
      }
    }
  }

  // Map a Connec Resource to a Model
  public function saveConnecResource($cnc_hash, $persist=true, $model=null, $retry=true) {
    error_log("saveConnecResource entity=$this->connec_entity_name, hash=" . json_encode($cnc_hash));

    if(!$this->validate($cnc_hash)) { return null; }

    // Load existing Model or create a new instance
    try {
      if(is_null($model)) {
        $model = $this->findOrInitializeModel($cnc_hash);
        if(is_null($model)) {
          error_log("model cannot be initialized and will not be saved");
          return null;
        }
      }

      // Update the model attributes
      error_log("mapConnecResourceToModel entity=$this->connec_entity_name");
      $this->mapConnecResourceToModel($cnc_hash, $model);

      // Save and map the Model id to the Connec resource id
      if($persist) {
        $this->persistLocalModel($model, $cnc_hash);
        $this->findOrCreateIdMap($cnc_hash, $model);
      }

      return $model;
    } catch (Exception $e) {
      error_log("Error when saving Connec resource entity=".$this->connec_entity_name.", error=" . $e->getMessage());
      if($retry) {
        // Can fail due to concurrent persists using the same PK, so give it another chance
        error_log("retrying saveConnecResource entity=$this->connec_entity_name");
        return $this->saveConnecResource($cnc_hash, $persist, $model, false);
      }
    }
    return null;
  }

  // Map a Connec Resource to a Model
  public function findOrCreateIdMap($cnc_hash, $model) {
    $local_id = $this->getId($model);
    $mno_id = $this->getConnecResourceId($cnc_hash);
    error_log("findOrCreateIdMap entity=$this->connec_entity_name, local_id=$local_id, entity_id=" .$mno_id);

    if($local_id == 0 || is_null($mno_id)) { return null; }

    $mno_id_map = MnoIdMap::findMnoIdMapByLocalIdAndEntityName($local_id, $this->local_entity_name);
    if(!$mno_id_map) {
      error_log("map connec resource entity=$this->connec_entity_name, id=" . $mno_id . ", local_id=$local_id");
      return MnoIdMap::addMnoIdMap($local_id, $this->local_entity_name, $mno_id, $this->connec_entity_name);
    }

    return $mno_id_map;
  }

  // Find a Connec! ID or persist the model and return the id
  public function findIdMapOrPersist($model) {
    $local_id = $this->getId($model);

    $mno_id_map = MnoIdMap::findMnoIdMapByLocalIdAndEntityName($local_id, $this->local_entity_name);
    if(!$mno_id_map) {
      $this->processLocalUpdate($model);
      $mno_id_map = MnoIdMap::findMnoIdMapByLocalIdAndEntityName($local_id, $this->local_entity_name);
    }

    return $mno_id_map;
  }


  // Process a Model update event
  // $pushToConnec: option to notify Connec! of the model update
  // $delete:       option to soft delete the local entity mapping amd ignore further Connec! updates
  public function processLocalUpdate($model, $pushToConnec=true, $delete=false) {
    $pushToConnec = $pushToConnec && Maestrano::param('connec.enabled');

    error_log("process local update entity=$this->connec_entity_name, local_id=" . $this->getId($model) . ", pushToConnec=$pushToConnec, delete=$delete");

    if($pushToConnec) {
      $this->pushToConnec($model);
    }

    if($delete) {
      $this->flagAsDeleted($model);
    }
  }

  // Find a model matching the Connec resource or initialize a new one
  protected function findOrInitializeModel($cnc_hash) {
    $model = null;

    // Find local Model if exists
    $mno_id = $this->getConnecResourceId($cnc_hash);
    $mno_id_map = MnoIdMap::findMnoIdMapByMnoIdAndEntityName($mno_id, $this->connec_entity_name);

    error_log("find or initialize entity=$this->connec_entity_name, mno_id=$mno_id, mno_id_map=" . json_encode($mno_id_map));

    if($mno_id_map) {
      // Ignore updates for deleted Models
      if($mno_id_map['deleted_flag'] == 1) {
        error_log("ignore update for locally deleted entity=$this->connec_entity_name, mno_id=$mno_id");
        return null;
      }

      // Load the locally mapped Model
      $model = $this->loadModelById($mno_id_map['app_entity_id']);
    }

    // Match a local Model from hash attributes
    if($model == null) { $model = $this->matchLocalModel($cnc_hash); }

    // Create a new Model if none found
    if($model == null) { $model = $this->initializeNewModel(); }

    return $model;
  }

  // Transform a Model into a Connec Resource and push it to Connec
  protected function pushToConnec($model, $saveResult=false) {
    // Transform the Model into a Connec hash
    $cnc_hash = $this->mapModelToConnecResource($model);
    $hash = array($this->connec_resource_name => $cnc_hash);
    // Find Connec resource id
    $local_id = $this->getId($model);
    $mno_id_map = MnoIdMap::findMnoIdMapByLocalIdAndEntityName($local_id, $this->local_entity_name);

    if($mno_id_map) {
      // Update resource
      $url = $this->connec_resource_endpoint . '/' . $mno_id_map['mno_entity_guid'];
      error_log("updating entity=$this->local_entity_name, url=$url, id=$local_id hash=" . json_encode($hash));
      $response = $this->_connec_client->put($url, $hash);
    } else {
      // Create resource
      $url = $this->connec_resource_endpoint;
      error_log("creating entity=$this->local_entity_name, url=$url, hash=" . json_encode($hash));
      $response = $this->_connec_client->post($url, $hash);
    }

    // Process Connec response
    $code = $response['code'];
    $body = $response['body'];
    if($code >= 300) {
      error_log("Cannot push to Connec! entity_name=$this->local_entity_name, code=$code, body=$body");
      return false;
    } else {
      error_log("Processing Connec! response code=$code, body=$body");
      $result = json_decode($body, true);
      if($saveResult) {
        // Save the complete response
        error_log("saving back entity_name=$this->local_entity_name");
        return $this->saveConnecResource($result[$this->connec_resource_name], true, $model);
      } else {
        // Map the Connec! ID with the local one
        error_log("mapping back entity_name=$this->local_entity_name");
        $this->findOrCreateIdMap($result[$this->connec_resource_name], $model);

        // Custom response processing
        error_log("processing back entity_name=$this->local_entity_name");
        $this->processConnecResponse($result[$this->connec_resource_name], $model);
        return $model;
      }
    }
  }

  // Flag the local Model mapping as deleted to ignore further updates
  protected function flagAsDeleted($model) {
    $local_id = $this->getId($model);
    error_log("flag as deleted entity=$this->connec_entity_name, local_id=$local_id");
    MnoIdMap::deleteMnoIdMap($local_id, $this->local_entity_name);
  }

  // Dynamically find mappers
  public static function getMappers() {
    $mappers = array();
    foreach(get_declared_classes() as $class) {
      if(is_subclass_of($class, 'BaseMapper')) {
        $mappers[] = $class;
      }
    }
    return $mappers;
  }
}
