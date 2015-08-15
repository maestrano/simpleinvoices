<?php

/**
 * Maestrano map table functions
 *
 * @author root
 */

class MnoSoaDB extends MnoSoaBaseDB {
    /**
    * Update identifier map table
    * @param  	string 	local_id                Local entity identifier
    * @param    string  local_entity_name       Local entity name
    * @param	string	mno_id                  Maestrano entity identifier
    * @param	string	mno_entity_name         Maestrano entity name
    *
    * @return 	boolean Record inserted
    */            
    public function addIdMapEntry($local_id, $local_entity_name, $mno_id, $mno_entity_name) {	
      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
	// Fetch record
      $query = "INSERT INTO mno_id_map (mno_entity_guid, mno_entity_name, app_entity_id, app_entity_name, db_timestamp) VALUES "
      . "(:mno_entity_guid, :mno_entity_name, :app_entity_id, :app_entity_name, UTC_TIMESTAMP)";	
      $result = $this->_db->query($query,
       ':mno_entity_guid', $mno_id,
       ':mno_entity_name', strtoupper($mno_entity_name),
       ':app_entity_id', $local_id,
       ':app_entity_name', strtoupper($local_entity_name)
       );

      $this->_log->debug("addIdMapEntry query = ".$query);

      return $result;
    }
    
    /**
    * Get Maestrano GUID when provided with a local identifier
    * @param  	string 	local_id                Local entity identifier
    * @param    string  local_entity_name       Local entity name
    *
    * @return 	boolean Record found	
    */
    public function getMnoIdByLocalIdName($local_id, $local_entity_name) {
      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
      $mno_entity = null;

	// Fetch record
      $query = "SELECT mno_entity_guid, mno_entity_name, deleted_flag from mno_id_map where app_entity_id=:app_entity_id and app_entity_name=:app_entity_name";
      $result = dbQuery($query,
       ':app_entity_id', $local_id,
       ':app_entity_name', strtoupper($local_entity_name)
       );

      $row = $result->fetch();

	// Return id value
      if ($row) {
        $mno_entity_guid = trim($row["mno_entity_guid"]);
        $mno_entity_name = trim($row["mno_entity_name"]);
        $deleted_flag = trim($row["deleted_flag"]);

        if (!empty($mno_entity_guid) && !empty($mno_entity_name)) {
          $mno_entity = (object) array (
            "_id" => $mno_entity_guid,
            "_entity" => $mno_entity_name,
            "_deleted_flag" => $deleted_flag
            );
        }
      }

      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . "returning mno_entity = ".json_encode($mno_entity));
      return $mno_entity;
    }
    
    public function getLocalIdByMnoIdName($mno_id, $mno_entity_name) {
      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
      $local_entity = null;

	// Fetch record
      $query = "SELECT app_entity_id, app_entity_name, deleted_flag from mno_id_map where mno_entity_guid=:mno_entity_guid and mno_entity_name=:mno_entity_name";
      $result = dbQuery($query,
       'mno_entity_guid', $mno_id,
       'mno_entity_name', strtoupper($mno_entity_name)
       );

      $row = $result->fetch();

	// Return id value
      if ($row) {
        $app_entity_id = trim($row["app_entity_id"]);
        $app_entity_name = trim($row["app_entity_name"]);
        $deleted_flag = trim($row["deleted_flag"]);

        if (!empty($app_entity_id) && !empty($app_entity_name)) {
          $local_entity = (object) array (
            "_id" => $app_entity_id,
            "_entity" => $app_entity_name,
            "_deleted_flag" => $deleted_flag
            );
        }
      }

      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . "returning mno_entity = ".json_encode($local_entity));
      return $local_entity;
    }
    
    public function deleteIdMapEntry($local_id, $local_entity_name) {
      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
        // Logically delete record
      $query = "UPDATE mno_id_map SET deleted_flag=1 WHERE app_entity_id=:app_entity_id and app_entity_name=:app_entity_name";
      $result = $this->_db->query($query,
       ':app_entity_id', $local_id,
       ':app_entity_name', strtoupper($local_entity_name)
       );

      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " query = ".$query);

      return $result;
    }
    
    public function undeleteIdMapEntry($local_id, $local_entity_name) {
      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");

      $query = "UPDATE mno_id_map SET deleted_flag=0 WHERE app_entity_id=:app_entity_id and app_entity_name=:app_entity_name";
      $result = $this->_db->query($query,
       ':app_entity_id', $local_id,
       ':app_entity_name', strtoupper($local_entity_name)
       );

      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " query = ".$query);
      return $result;
    }

    public function hardDeleteIdMapEntry($local_id, $local_entity_name) {
      $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " start");
        // Hard delete record
      $query = "DELETE FROM mno_id_map WHERE app_entity_id='" . $local_id . "' and app_entity_name='" . strtoupper($local_entity_name) . "'";
      $result = $this->_db->query($query);

      if ($result) {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " return true");
        return true;
      } else {
        $this->_log->debug(__CLASS__ . ' ' . __FUNCTION__ . " return false");
        return false;
      }
    }
  }

  ?>