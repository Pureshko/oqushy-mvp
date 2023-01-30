<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'apiKey'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /*protected $casts = [
        'email_verified_at' => 'datetime',
    ];*/

    /**
     * Generates api token function
     * 
     * @param null
     * @return string $token
     */
    public function generateToken(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';
        for ($i = 0; $i < 255; $i++) {
            $token .= $characters[rand(0, strlen($characters)-1)];
        }
        return $token;
    }

    /**
     * Output ranked list of students
     * 
     * @param null
     * @return $result
     *      $result = [
     *          array(
     *              "place" => (int)
     *              "score" => (int)
     *          )    
     *      ]
     */
    public function getStudentsRankedList($grade,$time,$single=false){
        if($single){
            $scores = $this->achievements()
                    ->join('places','places.id','=','achievements.place_id')
                    ->whereNot('achievements.status','=','NEW')
                    ->whereNot('achievements.rating','=',0)
                    ->groupBy('achievements.owner')
                    ->orderBy('score','desc')
                    ->select(DB::raw('SUM(places.score) as score'),'achievements.owner');
            $list = $this->leftJoinSub($scores,'scores','scores.owner','=','users.id')
                        ->select('users.id','score')->get()->toArray();
            return $list;  
        }
        if(gettype($grade)=='array'){
            $scores = $this->achievements()
                    ->join('places','places.id','=','achievements.place_id')
                    ->whereNot('achievements.status','=','NEW')
                    ->whereNot('achievements.rating','=',0)
                    ->groupBy('achievements.owner')
                    ->orderBy('score','desc')
                    ->select(DB::raw('SUM(places.score) as score'),'achievements.owner');
            $list = $this->leftJoinSub($scores,'scores','scores.owner','=','users.id')
                        ->join('studentgrade','studentgrade.user_id','=','users.id')
                        ->join('grades','grades.id','=','studentgrade.grade_id')
                        ->where('grades.grade','>=',$grade[0])
                        ->where('grades.grade','<=',$grade[1])
                        ->select('users.id','users.name','grades.grade','grades.letter','score')->get()->toArray();
        }else if($grade==0){
            $scores = $this->achievements()
                    ->join('places','places.id','=','achievements.place_id')
                    ->whereNot('achievements.status','=','NEW')
                    ->whereNot('achievements.rating','=',0)
                    ->groupBy('achievements.owner')
                    ->orderBy('score','desc')
                    ->select(DB::raw('SUM(places.score) as score'),'achievements.owner');
            $list = $this->leftJoinSub($scores,'scores','scores.owner','=','users.id')
                        ->join('studentgrade','studentgrade.user_id','=','users.id')
                        ->join('grades','grades.id','=','studentgrade.grade_id')
                        ->select('users.id','users.name','grades.grade','grades.letter','score')->get()->toArray();
            return $list;
        }
        $scores = $this->achievements()
                    ->join('places','places.id','=','achievements.place_id')
                    ->whereNot('achievements.status','=','NEW')
                    ->whereNot('achievements.rating','=',0)
                    ->groupBy('achievements.owner')
                    ->orderBy('score','desc')
                    ->select(DB::raw('SUM(places.score) as score'),'achievements.owner');
        $list = $this->leftJoinSub($scores,'scores','scores.owner','=','users.id')
                    ->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('grades','grades.id','=','studentgrade.grade_id')
                    ->where('grades.grade','=',$grade)
                    ->select('users.id','users.name','grades.grade','grades.letter','score')->get()->toArray();
        return $list;
    }
    public function getGradesRankedList($time=0){
        $achievements = $this->achievements()
                    ->join('places','places.id','=','achievements.place_id')
                    ->join('studentgrade','studentgrade.user_id','=','achievements.owner')
                    ->join('grades','grades.id','=','studentgrade.grade_id')
                    ->whereNot('achievements.status','=','NEW')
                    ->whereNot('achievements.rating','=',0)
                    ->groupBy('grades.id')
                    ->select('grades.id',DB::raw('SUM(places.score) as score'));
        $grades = $this->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('grades','grades.id','=','studentgrade.grade_id')
                    ->leftJoinSub($achievements,'scores','scores.id','=','grades.id')
                    ->orderBy('grades.grade','desc')
                    ->orderBy('grades.letter','asc')
                    ->select('grades.id','grades.grade','grades.letter','scores.score')
                    ->get()->toArray();
        return $grades;
    }
    public function getShanyraqRankedList(){
        $penalties = $this->penalties()
                    ->join('places','places.id','=','penalties.place_id')
                    ->whereNot('penalties.status','=','NEW')
                    ->groupBy('penalties.shanyraq_id')
                    ->select('penalties.shanyraq_id',DB::raw('SUM(places.score*penalties.factor) as score'));
        $achievements = $this->achievements()
                    ->join('places','places.id','=','achievements.place_id')
                    ->whereNot('achievements.status','=','NEW')
                    ->whereNot('achievements.rating','=',0)
                    ->groupBy('achievements.owner')
                    ->select('achievements.owner',DB::raw('SUM(places.score) as score'));
        $achievements = $this->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('shanyraqgrade','shanyraqgrade.grade_id','=','studentgrade.grade_id')
                    ->join('shanyraqs','shanyraqs.id','=','shanyraqgrade.shanyraq_id')
                    ->leftJoinSub($achievements,'achievements','achievements.owner','=','users.id')
                    ->orderBy('score','desc')
                    ->select('shanyraqs.id','achievements.score');
        $memberachievements = $this->memberachievements()
                    ->join('places','places.id','=','memberachievements.place_id')
                    ->whereNot('memberachievements.status','=','NEW')
                    ->groupBy('memberachievements.student_id')
                    ->select('memberachievements.student_id',DB::raw('SUM(memberachievements.score) as score'));
        $memberachievements = $this->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('shanyraqgrade','shanyraqgrade.grade_id','=','studentgrade.grade_id')
                    ->join('shanyraqs','shanyraqs.id','=','shanyraqgrade.shanyraq_id')
                    ->leftJoinSub($memberachievements,'memberachievements','memberachievements.student_id','=','users.id')
                    ->orderBy('score','desc')
                    ->select('shanyraqs.id','memberachievements.score');
        $groupachievements = $this->groupachievements()
                    ->join('places','places.id','=','groupachievements.place_id')
                    ->whereNot('groupachievements.status','=','NEW')
                    ->groupBy('groupachievements.shanyraq_id')
                    ->select('groupachievements.shanyraq_id',DB::raw('SUM(places.score) as score'));
        $achievementlist = $achievements->union($memberachievements)->union($groupachievements)->union($penalties);
        $shanyraqs = $this->Shanyraqs()
                    ->join('shanyraqgrade','shanyraqgrade.shanyraq_id','=','shanyraqs.id')
                    ->join('grades','grades.id','=','shanyraqgrade.grade_id')
                    ->join('studentgrade','studentgrade.grade_id','=','grades.id')
                    ->leftJoinSub($achievementlist,'achievementlist','achievementlist.id','=','shanyraqs.id')
                    ->groupBy('shanyraqs.id','shanyraqs.name','achievementlist.score')
                    ->orderBy('average','desc')
                    ->select('shanyraqs.id','shanyraqs.name','achievementlist.score as score',DB::raw('COUNT(studentgrade.user_id) as number'),DB::raw('score/COUNT(studentgrade.user_id) as average'))
                    ->get()->toArray();
        return $shanyraqs;
    }
    public function getStudentRank($token=null,$id=null){
        $list = (array)($this->getStudentsRankedList(0,0,true));
        $id = $this->where('apiKey','=',$token)->select('id')->get()->toArray();
        $found_key = array_search($id[0]['id'], array_column($list, 'id'));
        $score = $list[$found_key]['score'];
        $result = [
            "place" => $found_key+1,
            "score" => $score
        ];
        return $result;
    }
    public function getUserRoles($token=null,$id=null){
        if(!$token){
            $roles = $this->join('user_roles','user_roles.user_id','=','users.id')
                    ->join('roles','roles.id','=','user_roles.role_id')
                    ->where('apiKey','=',$token)
                    ->select('roles.name')->get()->toArray();
            return array_column($roles,'name');
        }else if(!$id){
            $roles = $this->join('user_roles','user_roles.user_id','=','users.id')
                    ->join('roles','roles.id','=','user_roles.role_id')
                    ->where('users.id','=',$id)
                    ->select('roles.name')->get()->toArray();
            return array_column($roles,'name');
        }
    }
    /**
     * Receive user information
     * 
     * @param $token
     * @return array $user
     *     $user = [
     *          "email" => (string|255)
     *          "name" => (string|255),     
     *     ]
     */
    public function getUser($token){
        $user = $this->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('grades','grades.id','=','studentgrade.grade_id')
                    ->where('apiKey',$token)
                    ->select('users.id','users.email','users.name','grades.grade','grades.letter')
                    ->get()->toArray();
        $user[0]['roles'] = $this->getUserRoles($token);
        $user = array_merge($user[0],$this->getStudentRank($token));
        return $user;
    }
    public function getUserById($id){
        $user = $this->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('grades','grades.id','=','studentgrade.grade_id')
                    ->where('users.id',$id)
                    ->select('users.id','users.email','users.name','grades.grade','grades.letter')
                    ->get()->toArray();
        $user[0]['roles'] = $this->getUserRoles(0,$id);
        $user = array_merge($user[0],$this->getStudentRank($id));
        return $user;
    }
    public function getUserId($token){
        $id = $this->where('apiKey','=',$token)
                ->select('id')->get()->toArray();
        return $id[0]['id'];
    }
    public function getStudentGradeId($token){
        return ($this->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('grades','studentgrade.grade_id','=','grades.id')
                    ->where('users.apiKey','=',$token)
                    ->select('grades.id')->get()->toArray())[0]['id'];
    }
    public function getStudentShanyraq($token){
        return $this->join('studentgrade','studentgrade.user_id','=','users.id')
                    ->join('grades','studentgrade.grade_id','=','grades.id')
                    ->join('shanyraqgrade','shanyraqgrade.grade_id','=','grades.id')
                    ->join('shanyraqs','shanyraqs.id','=','shanyraqgrade.shanyraq_id')
                    ->where('users.apiKey','=',$token)
                    ->select('shanyraqs.id')->get()->toArray();
    }
    public function setToken($id,$data){
        return $this->where('id','=',$id)->update($data);
    }
    public function achievements(){
        return new Achievements();
    }
    public function GroupAchievements(){
        return new GroupAchievements();
    }
    public function MemberAchievements(){
        return new MemberAchievements();
    }
    public function Penalties(){
        return new Penalties();
    }
    public function Shanyraqs(){
        return new Shanyraqs();
    }
}
