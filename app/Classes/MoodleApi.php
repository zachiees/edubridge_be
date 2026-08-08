<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class MoodleApi
{
    private $api_token;
    private $api_url;
    public function __construct(){
        $this->api_token = env('LMS_API_KEY','');
        $this->api_url = env('LMS_API_URL','')."/webservice/rest/server.php";
    }
    public function login(string $username, string $password){
        $login_url = env('LMS_API_URL','')."/login/token.php?service=moodle_mobile_app";
        $credentials = ['username' => $username,
                        'password' => $password];
        return Http::withOptions(['verify' => false])
                    ->asForm()
                    ->acceptJson()
                    ->timeout(30)
                    ->post($login_url,$credentials)
                    ->json();
    }
    public function getSiteInfo(){
        return $this->sendPost("core_webservice_get_site_info");
    }
    public function findUserByField($field,$value){
        $params = ["criteria" => [['key' => $field,'value'=>$value]] ];
        return $this->sendPost("core_user_get_users", $params)["users"][0]??null;
    }
    public function getRoles(){
        return $this->sendPost("local_wsgetroles_get_roles");
    }
    public function findRoleByShortName($shortname){
        $params = [ 'shortnames'=>[$shortname] ];
        return $this->sendPost("local_wsgetroles_get_roles",$params)[0]??null;
    }
    public function findCourseById($id){
        $params = [ 'options'=>["ids"=>[$id]]];
        return $this->sendPost("core_course_get_courses", $params)[0]??null;
    }
    public function findCoursesByIds(Array $ids){
        $params = [ 'options'=>["ids"=>$ids]];
        return $ids? $this->sendPost("core_course_get_courses", $params):[];
    }
    public function courseEnrolment(Array $enrollments){
        $params = [ 'enrolments' =>$enrollments ];
        return $enrollments? $this->sendPost('enrol_manual_enrol_users', $params):[];
    }
    public function courseUnEnrolment($user_id, $role_id,Array $course_ids){
        $items = array_map(fn($el) => [ 'userid' => $user_id , 'courseid'=> $el , 'roleid'=>$role_id ]  ,$course_ids);
        $params = [ 'enrolments' => $items ];
        return $items? $this->sendPost('enrol_manual_unenrol_users', $params):[];
    }
    public function assignRole($user_id, $role_id,$context_id = null){
        $item = [];
        $item['userid'] = $user_id;
        $item['roleid'] = $role_id;
        if($context_id){
            $item['contextid'] = $context_id;
        }
        $assignments[] = $item;
        $params = [ 'assignments' => $assignments ];
        return $this->sendPost('core_role_assign_roles ', $params );
    }
    public function courseCategories(){
        return $this->sendPost('core_course_get_categories');
    }
    public function courses($category_id=null){
        $params = ["field"=>'category','value'=>$category_id];
        return $this->sendPost('core_course_get_courses_by_field', $category_id ? $params:[])["courses"];
    }
    public function userCourses($userid){
        $params = ['userid'=>$userid];
        return $this->sendPost('core_enrol_get_users_courses', $params);
    }
    public function createUser($user){
        $params = ["users"=>[$user]];
        return $user? $this->sendPost('core_user_create_users', $params):[];
    }
    public function suspend($userid,$suspend = false){
        $params  = ["users"=>[["id"=>$userid, "suspended"=> $suspend]]];
        return $this->sendPost('core_user_update_users',$params);
    }
    public function resetPassword($username){
        return $this->sendPost('core_auth_request_password_reset',["username"=>$username]);
    }
    public function getUsers(){
        $criteria['key'] = 'deleted';
        $criteria['value'] = '0';
        return $this->sendPost('core_user_get_users', ['criteria'=>[$criteria]]);
    }
    public function getCohorts(){
        return $this->sendPost('core_cohort_get_cohorts');
    }
    public function getCohortMembers($id){
        return $this->sendPost('core_cohort_get_cohort_members',['cohortids'=>[$id]]);
    }
    public function getCourseUserProfile($userid,$courseid){
        $user = ['userid'=>$userid,'courseid'=>$courseid];
        return $this->sendPost('core_user_get_course_user_profiles',['userlist'=>[$user]]);
    }
    public function getUserByRole($role_id,$context){
        /*
         * THIS FUNCTION REQUIRES LOCAL PLUGIN TO BE INSTALLED
         *
         * context = system | coursecat | course |  module | user
         * instanceid (optional) = (0 for system) search web for complete list
         *
         * */
        $params  = ["roleid"=>$role_id, "contextlevel"=> $context];
        return $this->sendPost('local_wsgetusersbyrole_get_users_by_role', $params);

    }
    //
    public function sendPost($wsfunction,$params=[]){
        $headers = ['wstoken'=>$this->api_token,
                    'moodlewsrestformat'=>"json",
                    'wsfunction'=>$wsfunction];
        $res = Http::withoutVerifying()
            ->asForm()
            ->acceptJson()
            ->retry(3,100)
            ->timeout(600)
            ->post($this->api_url,array_merge($params,$headers))
            ->json();
        if($res && array_key_exists('exception',$res)){
            return response($res,Response::HTTP_BAD_REQUEST);
        }
        return $res;
    }
}
