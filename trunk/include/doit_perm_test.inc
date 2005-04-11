<SCRIPT language="php">
//$Id$

// prepare sample user data, we'll use it for other functions too

  $username = "madebeer@igc.org";
  $password = "bob";

  $array[mail] = $username;
  $array[uid] = 'madebeer';
  $array["givenname"] = "michael";
  $array["sn"] = "de Beer";
  $array["userpassword"] = $password;

// AddUser

  $user_id = AddUser($array);

// AuthenticateUsername

   $auth_result = AuthenticateUsername($username, $password);
   ptest( ($auth_result == $user_id), "AuthenticateUsername (good passwd)");
//  This may fail, if AddUser added a user without a unique email address
#   echo "auth_result = $auth_result\n";
#   echo "user_id = $user_id\n";

   $auth_result = AuthenticateUsername($username, 'notpassword');
#   echo "second auth_result $auth_result\n";
   ptest( (! $auth_result) , "AuthenticateUsername (bad passwd)" );

// FindUsers

  $array[mail] = "chris@ecn.cz";
  $array[uid] = "chris";
  $array["givenname"] = "chris";
  $array["sn"] = "de Beer";
  $array["userpassword"] = "bob3";

  $user_id2 = AddUser($array);

  $array[mail] = "rock@ecn.cz";
  $array[uid] = "rock";
  $array["givenname"] = "chris";
  $array["sn"] = "Rock";
  $array["userpassword"] = "bob3";

  $user_id3 = AddUser($array);

  $found_users = FindUsers ("chris");
  ptest( (count($found_users) == 2) , "FindUsers()" );

// DelUser

  DelUser($user_id);    
  $auth_result = AuthenticateUsername($username, $password);
  ptest( (! $auth_result) , "DelUser()" );

  $array[mail] = $username;
  $array[uid] = 'madebeer';
  $array["givenname"] = "michael";
  $array["sn"] = "de Beer";
  $array["userpassword"] = $password;
  $user_id = AddUser($array);

// AddGroup

   $group[name] = "Test Org";
   $group[description] = "a very fine org, if you like those things";
   $org_id = AddGroup($group);

   $org_info = GetIDsInfo($org_id);
   ptest( ( $org_info[name] == $group[name]), "AddGroup/GetIDsInfo");

// FindGroups

   $group[name] = "Test Org the second";
   $group[description] = "should be found with FindGroups Too";
   $org_id2 = AddGroup($group);

   $group[name] = "Third Org";
   $group[description] = "should not be found with FindGroups";
   $org_id3 = AddGroup($group);

   $found_groups = FindGroups("Test");

   while (list($key, $val) = each($found_groups))
      echo "$key => $val[name]\n";

   ptest( (count($found_groups) == 2) , "FindGroups()" );
   ptest( ($found_groups[$org_id][name] == "Test Org") , 
          "FindGroups(detail)" );

// DelGroup
   
   DelGroup($org_id);
   $found_groups = FindGroups("Test");
   ptest( (count($found_groups) == 1) , "DelGroup()" );

   $org_id = AddGroup($group);

// AddGroupMember & GetGroupMembers & GetIDsInfo

   AddGroupMember( $org_id,  $user_id2 );
   AddGroupMember( $org_id2, $user_id2 );
   AddGroupMember( $org_id2, $user_id3 );

   $list_users = GetGroupMembers($org_id2);
   ptest( (count($list_users) == 2) , "AddGroupMember & GetGroupMembers" );
   ptest( ($list_users[$user_id2][mail] == "chris@ecn.cz") , 
          "GetGroupMembers & GetIDsInfo" );

// GetMembership

   $list_groups = GetMembership($user_id2);
   ptest( (count($list_groups) == 3) , "GetMembership" );
#   echo $list_groups
#   echo "list_groups =";
#   echo count($list_groups);
#   echo "\n";
#   echo "FINAL list_groups: ";
#   echo $list_groups;
#   p_array($list_groups);

// DelGroupMember

   DelGroupMember( $org_id2, $user_id2 );
   $list_users = GetGroupMembers($org_id2);
   ptest( (count($list_users) == 1) , "DelGroupMember" );

// PermObject (not used in sql)

   ptest( (AddPermObject( $sliceid,'slice')) , "AddPermObject(consistency)" );
   ptest( (DelPermObject( $sliceid,'slice')) , "DelPermObject(consistency)" );


// AddPerm & GetObjectsPerms

   # keep these to under 32 characters long
   $sliceid = 'sliceid_from_mysql_datastore';
   $sliceid2 = 'another_sliceid_from_mysql';
   $perm = 1;
   $perm2 = 2;

   AddPerm($user_id2,  $sliceid,  'slice', $perm);
   AddPerm($user_id3,  $sliceid,  'slice', $perm);
   AddPerm($org_id2, $sliceid,  'slice', $perm2);
   AddPerm($org_id2, $sliceid2, 'slice', $perm2);

   $objects_perms = GetObjectsPerms($sliceid,'slice');
   ptest( 
          ( (count($objects_perms) == 3) &&
           ($objects_perms[$user_id2][perm] == $perm) &&
           ($objects_perms[$org_id2][perm] == $perm2)), 
         "AddPerm & GetObjectsPerms" );

/* -------------------------        extra tests:

ptest  ( (count($objects_perms) == 3), "detail1");
ptest  ( ($objects_perms[$user_id2][perm] == $perm), "detail2");
ptest  ( ($objects_perms[$org_id2][perm] == $perm2), "detail3");

echo  (count($objects_perms) == 3);
echo ".";
echo  ($objects_perms[$user_id2][perm] == $perm);
echo ".";
echo  ($objects_perms[$org_id2][perm] == $perm2);
echo ".";
echo "\n";

   echo "raw objects_perms:[$objects_perms]";
   while (list($key, $val) = each($objects_perms)) echo "$key => $val\n";
-----------------------                               */

// DelPerm

   DelPerm($user_id2,  $sliceid,  'slice');
   $objects_perms = GetObjectsPerms($sliceid,'slice');
   ptest( (count($objects_perms) == 2), "DelPerm");

// ChangePerm

   ChangePerm($user_id3,  $sliceid,  'slice', $perm2);
   $objects_perms = GetObjectsPerms($sliceid,'slice');
   ptest( $objects_perms[$user_id3][perm] == $perm2, "ChangePerm");

// GetIDPerms

   AddPerm($user_id3,  $sliceid2,  'slice', $perm);
   $id3_perms = GetIDPerms ($user_id3, 'slice');
   ptest( 
          ( (count($id3_perms) == 2) &&
           ($id3_perms[$sliceid] == $perm2)
            && ($id3_perms[substr ($sliceid2,0,32)] == $perm)
          ), 
         "GetIDPerms" );
   while (list($key, $val) = each($id3_perms)) echo "$key => $val\n";

</SCRIPT>