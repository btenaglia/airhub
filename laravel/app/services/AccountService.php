<?php

namespace App\Services;

use \App\Models\Member;
use \App\Models\User;
use \JWTAuth;

/**
 * All the bussines logic for manage the account.
 * @see Look the exceptions handling in the App/Start/global.php
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class AccountService extends BaseService implements GenericServices
{
    private $userType = "";
    private $phoneBE = "";

    /**
     * Authenticate the
     * @param type $credentials email and password
     * @return $token null if the credentials are not valid
     */
    public function authenticateByCredentials($credentials)
    {
        try {
            $user = User::findByCredentials($credentials);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            //return null if there is no model for the credentials passed by param
            return null;
        }

        if ($user === null) {
            return null;
        }

        //remove private data
        $user->setPassword('');

        return ['token' => $this->createToken($user), 'user' => $user];
    }

    public function authenticateAppUserByCredentials($credentials)
    {
        try {
            $user = User::findAppUserByCredentials($credentials);
            $user2 = User::with('getMember')->where('id', $user->getId())->get();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            //return null if there is no model for the credentials passed by param
            return null;
        }

        if ($user === null) {
            return null;
        }

        /*Update id Onesignal*/
        if (isset($credentials['id_onesignal'])) {
            $user->setIdOnesignal($credentials['id_onesignal']);
            $success = $user->update();
        }

        /*Update id Firebase*/
        if (isset($credentials['fcm_token_device'])) {
            $user->setIdFirebase($credentials['fcm_token_device']);
            $success = $user->update();
        }

        //remove private data
        $user->setPassword('');

        return ['token' => $this->createToken($user), 'user' => $user2];
    }

    public function authenticateAppUserByFacebook($credentials)
    {
        // $user = User::with('getMember')->where("facebooktoken","=",$credentials['tokenfb'])->get();

        try {
            //$user = User::findAppUserByCredentials($credentials);
            $resfacebook = $this->facebookcheck($credentials["tokenfb"]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            //return null if there is no model for the credentials passed by param
            return null;
        }

        if (array_key_exists('error', $resfacebook)) {
            //$facebookresult = array('Error'=>$resfacebook['error']['message']);
            return null;
        }

        $id = $resfacebook['id'];
        
        try {
            $user = User::with('getMember')->where("facebookid","=",$id)->get();
            // $user = User::findAppUserByIdFB($id);
          
            return $user;
            /*Update id Onesignal*/
            if (isset($credentials['id_onesignal'])) {
                $user->setIdOnesignal($credentials['id_onesignal']);
                $success = $user->update();
            }

            /*Update id Firebase*/
            if (isset($credentials['fcm_token_device'])) {
                $user->setIdFirebase($credentials['fcm_token_device']);
                $success = $user->update();
            }

            $user->setPassword('');
            return ['token' => $this->createToken($user), 'user' => $user];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {

            $idonesignal = '';
            if (isset($credentials["id_onesignal"])) {
                $idonesignal = $credentials["id_onesignal"];
            }

            $idfirebase = '';
            if (isset($credentials["fcm_token_device"])) {
                $idonesignal = $credentials["fcm_token_device"];
            }

            $input = array(
                'name' => (isset($resfacebook['first_name']) ? $resfacebook['first_name'] : $resfacebook['name']),
                'last_name' => (isset($resfacebook['last_name']) ? $resfacebook['last_name'] : ''),
                'email' => $id . '@facebook.com',
                'password' => substr($credentials["tokenfb"], -8),
                'facebookid' => $id,
                'facebooktoken' => $credentials["tokenfb"],
                'id_onesignal' => $idonesignal,
                'fcm_token_device' => $idfirebase,
                'create_mode' => 'Facebook',
            );

            $success = $this->createMobileUser($input);
            if ($success) {
                $user = User::findAppUserByIdFB($id);
                $user->setPassword('');
                return ['token' => $this->createToken($user), 'user' => $user];
            } else {
                return null;
            }
        }
    }

    public function authenticateAppUserByFacebook2($credentials)
    {
        try {
            //$user = User::findAppUserByCredentials($credentials);
            $resfacebook = $this->facebookcheck($credentials["tokenfb"]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            //return null if there is no model for the credentials passed by param
            return null;
        }

        if (array_key_exists('error', $resfacebook)) {
            //$facebookresult = array('Error'=>$resfacebook['error']['message']);
            return null;
        }

        $id = $resfacebook['id'];

        var_dump($resfacebook);
        exit(0);

        try {
            $user = User::findAppUserByIdFB($id);
            $user->setPassword('');
            return ['token' => $this->createToken($user), 'user' => $user];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {

            $input = array(
                'name' => (isset($resfacebook['first_name']) ? $resfacebook['first_name'] : $resfacebook['name']),
                'last_name' => (isset($resfacebook['last_name']) ? $resfacebook['last_name'] : ''),
                'email' => $id . '@facebook.com',
                'password' => substr($credentials["tokenfb"], -8),
                'facebookid' => $id,
                'facebooktoken' => $credentials["tokenfb"],
                'create_mode' => 'Facebook',
            );

            $success = $this->createMobileUser($input);
            if ($success) {
                $user = User::findAppUserByIdFB($id);
                $user->setPassword('');
                return ['token' => $this->createToken($user), 'user' => $user];
            } else {
                return null;
            }
        }
    }

    public function getUserData()
    {
        $user =  $this->getCurrentUser();
        $user2 = User::with('GetMember')->find($user->id);
        if ($user2 === null) {
            return null;
        }

        //remove private data
        $user->setPassword('');

        return $user2;
    }

    /**
     * Create new account in the app for backend users
     * @param type $input all the data filled by the user
     * @return integer id of the user created
     */
    public function create($input)
    {
        $user = new User();
        if (isset($input['complete_name'])) {
            $user->setCompleteName($input['complete_name']);
        }

        if (isset($input['name'])) {
            $user->setName($input['name']);
        }

        if (isset($input['last_name'])) {
            $user->setLastName($input['last_name']);
        }

        if (isset($input['name']) && isset($input['last_name'])) {
            $user->setCompleteName((strlen(trim($input['last_name'])) > 0 ? trim($input['last_name']) . ', ' : '') . trim($input['name']));
        }

        $user->setEmail($input['email']);
        $user->setPassword(\Crypt::encrypt($input['password']));
        $user->setUserType($this->userType);

        if ($this->userType === User::USER_TYPE_APP) {
            if (isset($input['id_onesignal'])) {
                $user->setIdOnesignal($input['id_onesignal']);
            }

            if (isset($input['fcm_token_device'])) {
                $user->setIdFirebase($input['fcm_token_device']);
            }

            if (isset($input['promo_code'])) {
                $user->setPromoCode($input['promo_code']);
            }

            if (isset($input['cell_phone'])) {
                $user->setCellPhone($input['cell_phone']);
            }

            if (isset($input['body_weight'])) {
                $user->setBodyWeight($input['body_weight']);
            }

            if (isset($input['address'])) {
                $user->setAddress($input['address']);
            }

            if (isset($input['city'])) {
                $user->setCity($input['city']);
            }

            if (isset($input['state'])) {
                $user->setState($input['state']);
            }

            if (isset($input['country'])) {
                $user->setCountry($input['country']);
            }

            if (isset($input['zipcode'])) {
                $user->setZipcode($input['zipcode']);
            }

            if (isset($input['member_id'])) {
                $user->member_id = $input['member_id'];
            }

            if (isset($input['facebookid'])) {
                $user->setFacebookik($input['facebookid']);
            }

            if (isset($input['facebooktoken'])) {
                $user->setFacebooktoken($input['facebooktoken']);
            }
        }

        if (strlen($this->phoneBE) > 1) {
            $user->setCellPhone($this->phoneBE);
        }
        /*Phone Admin*/

        if (isset($input['create_mode'])) {
            $user->setCreateMode($input['create_mode']);
        } else {
            $user->setCreateMode('Normal');
        }

        return $user->save();
    }

    /**
     * Create new account in the app for backend users
     * @param type $input all the data filled by the user
     * @return integer id of the user created
     */
    public function createBackendUser($input)
    {
        $this->userType = User::USER_TYPE_ADMIN;
        //$input['cell_phone'] = $this->mt_phone(12);
        $this->phoneBE = $this->mt_phone(12);
        return $this->create($input);
    }

    /**
     * Create new account in the app for the mobile app users
     * @param type $input all the data filled by the user
     * @return integer id of the user created
     */
    public function createMobileUser($input)
    {
        $member = Member::where("description", '=', 'Daily')->first();
        $this->userType = User::USER_TYPE_APP;
        $input['member_id'] = $member->id;
        return $this->create($input);
    }
    /**
     * Create new account in the app for the mobile app users
     * @param type $input all the data filled by the user
     * @return integer id of the user created
     */
    public function createMobileUser2($input)
    {

        $user = new User();
        $user->user_type = User::USER_TYPE_APP;
        $user->name = $input['name'];
        $user->member_id = $input['member_id'];
        $user->last_name = $input['last_name'];
        $user->address = $input['address'];
        $user->city = $input['city'];
        $user->body_weight = $input['body_weight'];
        $user->cell_phone = $input['cell_phone'];
        $user->country = $input['country'];
        $user->email = $input['email'];
        $user->setPassword(\Crypt::encrypt($input['password']));
        $user->state = $input['state'];
        $user->zipcode = $input['zipcode'];
        $user->verified = $input['verified'];
        $user->complete_name = $input['last_name'] . ',' . $input['name'];
        $user->save();
        return true;
    }
    /**
     * @param type $email is the unique identifier that the user knows
     * @return boolean if the user exists in the database
     */
    public function userExists($email)
    {
        //If the user doesn't exists an exception is thrown
        try {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                User::findByEmail($email);
            } else {
                User::findByCellphone($email);
            }

            return true;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return false;
        }
    }

    public function userExistsBE($email)
    {
        //If the user doesn't exists an exception is thrown
        try {
            User::findByEmail($email);
            return true;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return false;
        }
    }

    /**
     * Create a token and return this
     * @param type $user current
     * @return token
     */
    public function createToken($user)
    {
        //carth the exception if the user doesn't exists
        $customClaims = ["user_type" => $user->getUserType()];
        try {
            if (!$token = JWTAuth::fromUser($user, $customClaims)) {
                $token = null;
            }
        } catch (\ErrorException $ex) {
            $token = null;
        }

        return $token;
    }

    /**
     * @deprecated Implemented in the client-side
     * @return boolean true if success de logout of the user
     */
    public function logout()
    {
        $token = JWTAuth::getToken();
        return JWTAuth::invalidate($token);
    }

    public function all()
    {
        return User::findAllActives('App\Models\User');
    }

    public function destroy($id)
    {
        $user = $this->find($id);

        //Inactive
        //$user->setActive(false);
        //return $user->update();

        //Delete
        return $user->delete();
    }

    public function edit($id, $input)
    {
        $modify_m = true;
        $modify_c = true;

        $user = $this->find($id);

        if (isset($input['email'])) {
            if ($user->email != trim($input['email'])) {
                if ($this->userExists($input['email'])) {
                    $modify_m = false;
                } else {
                    $modify_m = true;
                }
            } else {
                $modify_m = true;
            }
        }

        if (isset($input['cell_phone'])) {
            if ($user->cell_phone != trim($input['cell_phone'])) {
                if ($this->userExists($input['cell_phone'])) {
                    $modify_c = false;
                } else {
                    $modify_c = true;
                }
            } else {
                $modify_c = true;
            }
        }

        if ($modify_m && $modify_c) {

            if (isset($input['complete_name'])) {
                $user->setCompleteName($input['complete_name']);
            }

            if (isset($input['name'])) {
                $user->setName($input['name']);
            }

            if (isset($input['last_name'])) {
                $user->setLastName($input['last_name']);
            }

            if (isset($input['name']) && isset($input['last_name'])) {
                //$user->setCompleteName(trim($input['last_name']).', '.trim($input['name']));
                $user->setCompleteName((strlen(trim($input['last_name'])) > 0 ? trim($input['last_name']) . ', ' : '') . trim($input['name']));
            }

            if ($user->getUserType() === User::USER_TYPE_APP) {
                if (isset($input['id_onesignal'])) {
                    $user->setIdOnesignal($input['id_onesignal']);
                }

                if (isset($input['fcm_token_device'])) {
                    $user->setIdFirebase($input['fcm_token_device']);
                }

                if (isset($input['email'])) {
                    $user->setEmail($input['email']);
                }

                if (isset($input['promo_code'])) {
                    $user->setPromoCode($input['promo_code']);
                }

                if (isset($input['cell_phone'])) {
                    $user->setCellPhone($input['cell_phone']);
                }

                if (isset($input['body_weight'])) {
                    $user->setBodyWeight($input['body_weight']);
                }

                if (isset($input['address'])) {
                    $user->setAddress($input['address']);
                }

                if (isset($input['member_id'])) {
                    $user->member_id = $input['member_id'];
                }

                if (isset($input['city'])) {
                    $user->setCity($input['city']);
                }

                if (isset($input['state'])) {
                    $user->setState($input['state']);
                }

                if (isset($input['country'])) {
                    $user->setCountry($input['country']);
                }

                if (isset($input['zipcode'])) {
                    $user->setZipcode($input['zipcode']);
                }

                if (isset($input['verified'])) {
                    $user->setVerified($input['verified']);
                }
            }

            $success = $user->update();

            if ($success) {
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function recoverPassword($email)
    {
        $user = User::where('email', '=', $email)->first();
        $newpassword = $this->mt_rand_str(10);
        $usersave = User::find($user->id);
        $usersave->setPassword(\Crypt::encrypt($newpassword));
        $usersave->save();
        $mailService = $this->getService('Mail');
        $sendData = (object) ['Email' => $email, 'Newpassword' => $newpassword];
        return $mailService->sendRecoverPassword($sendData);
    }

    private function mt_rand_str($l, $c = 'abcdefghjkmnpqrstuxyzABCDEFGHJKMNPQRSTUXYZ23456789')
    {
        for ($s = '', $cl = strlen($c) - 1, $i = 0; $i < $l; $s .= $c[mt_rand(0, $cl)], ++$i);
        return $s;
    }

    private function mt_phone($l, $c = '0123456789')
    {
        for ($s = '', $cl = strlen($c) - 1, $i = 0; $i < $l; $s .= $c[mt_rand(0, $cl)], ++$i);
        return $s;
    }

    private function facebookcheck($token)
    {

        $urlbase = 'https://graph.facebook.com/me?access_token=';

        $url = $urlbase . $token; //rawurlencode()

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Client '.phpversion());
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:35.0) Gecko/20100101 Firefox/35.0');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $resultjson = json_decode($result, true);

        curl_close($ch);

        return $resultjson;
    }
    public function validateAccessToken($token){
        $user = JWTAuth::toUser($token);
        $usernew = User::with('getMember')->find($user->id);
        return $usernew;
    }
}
