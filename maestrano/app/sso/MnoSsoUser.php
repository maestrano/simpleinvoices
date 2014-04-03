<?php

/**
 * Configure App specific behavior for 
 * Maestrano SSO
 */
class MnoSsoUser extends MnoSsoBaseUser
{
  /**
   * Database connection
   * @var PDO
   */
  public $connection = null;
  
  
  /**
   * Extend constructor to inialize app specific objects
   *
   * @param OneLogin_Saml_Response $saml_response
   *   A SamlResponse object from Maestrano containing details
   *   about the user being authenticated
   */
  public function __construct(OneLogin_Saml_Response $saml_response, &$session = array(), $opts = array())
  {
    // Call Parent
    parent::__construct($saml_response,$session);
    
    // Assign new attributes
    $this->connection = $opts['db_connection'];
  }
  
  
  /**
   * Sign the user in the application. 
   * Parent method deals with putting the mno_uid, 
   * mno_session and mno_session_recheck in session.
   *
   * @return boolean whether the user was successfully set in session or not
   */
  protected function setInSession()
  {
		$result = $this->connection->query("
			SELECT 
				u.id, u.email, r.name as role_name, u.domain_id
			FROM 
				si_user u,  si_user_role r 
			WHERE 
				u.mno_uid = :uid AND u.role_id = r.id AND u.enabled = 1", ':uid', $this->uid
		);
		$result = $result->fetch();
    
    /*
		* chuck the user details sans password into the Zend_auth session
		*/
    //Zend_Session::start();
		$authNamespace = new Zend_Session_Namespace('Zend_Auth');
		foreach ($result as $key => $value)
		{
			$authNamespace->$key = $value;
		}
    
    return true;
  }
  
  
  /**
   * Used by createLocalUserOrDenyAccess to create a local user 
   * based on the sso user.
   * If the method returns null then access is denied
   *
   * @return the ID of the user created, null otherwise
   */
  protected function createLocalUser()
  {
    $lid = null;
    
    if ($this->accessScope() == 'private') {
      $sql = "INSERT INTO si_user (email,password,role_id,domain_id,enabled) VALUES 
                  (
                      :email,
                      MD5(:password),
                      :role,
					            :domain_id,
					            :enabled
                  )
              ";
      
      // Create user
      $q = $this->connection->query($sql,
        ':email',$this->email,
        ':password',$this->generatePassword(),
        ':role',$this->getRoleValueToAssign(),
        ':domain_id',1,
        ':enabled',1);
       
      $lid = intval($this->connection->lastInsertId());
    }
    
    return $lid;
  }
  
  /**
   * Create the role to give to the user based on context
   * If the user is the owner of the app or at least Admin
   * for each organization, then it is given the role of 'Admin'.
   * Return 'User' role otherwise
   *
   * @return the ID of the user created, null otherwise
   */
  public function getRoleValueToAssign() {
    $role_value = 1; // Administrator | only one role in SI
    
    // if ($this->app_owner) {
    //   $role_value = 1; // Admin
    // } else {
    //   foreach ($this->organizations as $organization) {
    //     if ($organization['role'] == 'Admin' || $organization['role'] == 'Super Admin') {
    //       $role_value = 1;
    //     } else {
    //       $role_value = 2;
    //     }
    //   }
    // }
    
    return $role_value;
  }
  
  /**
   * Get the ID of a local user via Maestrano UID lookup
   *
   * @return a user ID if found, null otherwise
   */
  protected function getLocalIdByUid()
  {
    $result = $this->connection->query("SELECT id FROM si_user WHERE mno_uid = :uid LIMIT 1", ':uid', $this->uid)->fetch();
    
    if ($result && $result['id']) {
      return $result['id'];
    }
    
    return null;
  }
  
  /**
   * Get the ID of a local user via email lookup
   *
   * @return a user ID if found, null otherwise
   */
  protected function getLocalIdByEmail()
  {
    $result = $this->connection->query("SELECT id FROM si_user WHERE email = :email LIMIT 1", ':email', $this->email)->fetch();
    
    if ($result && $result['id']) {
      return $result['id'];
    }
    
    return null;
  }
  
  /**
   * Set all 'soft' details on the user (like name, surname, email)
   * Implementing this method is optional.
   *
   * @return boolean whether the user was synced or not
   */
   protected function syncLocalDetails()
   {
     if($this->local_id) {
       $upd = $this->connection->query("UPDATE si_user SET email = :email
       WHERE id = :local_id", ':email', $this->email, ':local_id', $this->local_id);
       return $upd;
     }
     
     return false;
   }
  
  /**
   * Set the Maestrano UID on a local user via id lookup
   *
   * @return a user ID if found, null otherwise
   */
  protected function setLocalUid()
  {
    if($this->local_id) {
      $upd = $this->connection->query("UPDATE si_user SET mno_uid = :uid WHERE id = :local_id",':uid',$this->uid, ':local_id', $this->local_id);
      return $upd;
    }
    
    return false;
  }
}