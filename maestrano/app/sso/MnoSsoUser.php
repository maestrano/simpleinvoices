<?php

/**
 * Configure App specific behavior for
 * Maestrano SSO
 */
class MnoSsoUser extends Maestrano_Sso_User
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
  public function __construct($resp)
  {
    // Call Parent
    parent::__construct($resp);

    // Assign new attributes
    $this->db = db::getInstance();
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
		$result = $this->db->query("
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
  * Find or Create a user based on the SAML response parameter and Add the user to current session
  */
  public function findOrCreate() {
    // Find user by uid or email
    $local_id = $this->getLocalIdByUid();
    if($local_id == null) { $local_id = $this->getLocalIdByEmail(); }

    if ($local_id) {
      // User found, load it
      $this->local_id = $local_id;
      $this->syncLocalDetails();
    } else {
      // New user, create it
      $this->local_id = $this->createLocalUser();
      $this->setLocalUid();
    }

    // Add user to current session
    $this->setInSession();
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
    $q = $this->db->query($sql,
      ':email',$this->email,
      ':password',$this->generatePassword(),
      ':role',$this->getRoleValueToAssign(),
      ':domain_id',1,
      ':enabled',1);

    $lid = intval($this->db->lastInsertId());

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
    $result = $this->db->query("SELECT id FROM si_user WHERE mno_uid = :uid LIMIT 1", ':uid', $this->uid)->fetch();

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
    $result = $this->db->query("SELECT id FROM si_user WHERE email = :email LIMIT 1", ':email', $this->email)->fetch();

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
       $upd = $this->db->query("UPDATE si_user SET email = :email
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
      $upd = $this->db->query("UPDATE si_user SET mno_uid = :uid WHERE id = :local_id",':uid',$this->uid, ':local_id', $this->local_id);
      return $upd;
    }

    return false;
  }

  /**
  * Generate a random password.
  * Convenient to set dummy passwords on users
  *
  * @return string a random password
  */
  protected function generatePassword() {
    $length = 20;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }
}
