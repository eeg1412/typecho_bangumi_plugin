<?php
class BangumiAPI
{
  /** OooOooOooO **/
  private static $bangumiAPI = null;
  /** 静态成员 **/
  //应用程序名
  private  static $appName = "BGMYetu";
  //api链接
  private static $apiUrl = "https://api.bgm.tv";
  /** 成员 **/
  //用户名（邮箱）
  public  $userName = "";
  //密码
  public  $passWord = "";
  //用户id
  private  $userID = "";
  //auth
  private  $auth = "";
  //auth urlencoding
  private  $authEncode = "";
  //收藏
  private $myCollection;
  //登陆api
  private  $loginApi = "";
  //收藏api
  private  $collectionApi = ""; 
  /** 方法 **/
  //OooOooO
  public static function GetInstance()
      {
    if (BangumiAPI::$bangumiAPI == null) {
      BangumiAPI::$bangumiAPI = new BangumiAPI();
    }
    return BangumiAPI::$bangumiAPI;
  }
  //构造方法
  private function __construct()
      {
    //echo "构造方法";
  }
  //对象属性初始化
  public function init($_userName,$_passWord)
      {
    if ($_userName == null || $_passWord == null) {
      //程序返回
      echo "初始化参数错误！";
      return;
    }
    $this->userName = $_userName;
    $this->passWord = $_passWord;
    //登陆api
    $this->loginApi = BangumiAPI::$apiUrl . "/auth?source=" . BangumiAPI::$appName;
    //用户id为空或auth为空
    if ($this->userID == ""  || $this->authEncode == ""){
      //登陆post字符串
      $postData = array('username' => $this->userName , 'password' => $this->passWord);
      //获取登陆返回json
      $userContent = BangumiAPI::curl_post_contents($this->loginApi,$postData);
      //json to object
      $userData = json_decode($userContent);
      //存在error属性
      if (property_exists($userData, "error")) {
        //输出错误信息
        echo "登陆错误：" . $userData->error;
        //程序返回
        return;
      }
      //初始化
      $this->userID = $userData->id;
      $this->auth = $userData ->auth;
      $this->authEncode = $userData ->auth_encode;
    }
    //初始化收藏字符串
    $this->collectionApi = BangumiAPI::$apiUrl . "/user/" . $this->userID ."/collection?cat=playing";
  }
  //获取收藏json
  public function GetCollection()
      {
    if ($this->userID == "" || $this->collectionApi == "") {
      return null;
    }
    return BangumiAPI::curl_get_contents($this->collectionApi);
  }
  //格式化收藏
  public function ParseCollection()
      {
    $content = $this->GetCollection();
    if ($content == null || $content == "") {
      echo "获取失败";
      return;
    }
    //返回不是json
    if (strpos($content, "[{") != false && $content != "") {
      echo "用户不存在！";
      return;
    }
    $collData = json_decode($content);
    if (sizeof($collData) == 0 || $collData == null) {
      //echo "还没有记录哦~";
      return;
    }
    $index = 0;
    foreach ($collData as $value) {
      $name = $value->name;
      $name_cn = $value->subject->name_cn;
      $theurl = $value->subject->url;
      $img_grid =$value->subject->images->grid;
      $this->myCollection[$index++] = $value;
    }
  }
  //获取详细进度
  public function GetProgress($_subjectID)
      {
    if ($this->authEncode == "" || $this->userID == "") {
      return null;
    }
    $progressApi = BangumiAPI::$apiUrl . "/user/" . $this->userID . "/progress?subject_id=". $_subjectID . "&source=" . self::$appName . "&auth=" . authEncode;
    $content = BangumiAPI::curl_get_contents($progressApi);
    //print_r($content);
    return $content;
  }
  public function ParseProgress($_subjectID)
      {
    $content = $this->GetProgress($_subjectID);
    //不在收藏或没看过
    if ($content == "null") {
      return 0;
    }
    //在收藏中，没看过
    if ($content == "") {
      return 0;
    }
    $progressValue = json_decode($content);
    //返回剧集观看详细进度
    return $progressValue;
  }
  //打印收藏
  public function PrintCollecion($flag = true)
      {
    if ($this->myCollection == null) {
      $this->ParseCollection();
    }
    switch ($flag) {
      case true:
                    if (sizeof($this->myCollection) == 0 || $this->myCollection == null) {
        echo "还没有记录哦~";
        return;
      }
      echo "
          <style>
          a.bangumItem{
            line-height: 20px;
			white-space: nowrap;
			box-shadow: 0px 0px 3px rgba(0,0,0,0.2);
			width: 45%;
			margin: 1.5%;
			float: left;
			overflow: hidden;
			display: block;
			padding: 1%;
			height:88px;
			background: #fff;
			color: #14191e;
			font-family:-apple-system,BlinkMacSystemFont,Helvetica Neue,PingFang SC,Microsoft YaHei,Source Han Sans SC,Noto Sans CJK SC,WenQuanYi Micro Hei,sans-serif;
          }
		  a.bangumItem:hover{
			color: #14191e;
			opacity: 0.8;
			filter: saturate(150%);
			-webkit-filter: saturate(150%);
			-moz-filter: saturate(150%);
			-o-filter: saturate(150%);
			-ms-filter: saturate(150%);
		  }
          a.bangumItem img{
            width:60px;
			height:88px;
            display:inline-block;
            float:left;
            margin-right:5px;
          }
		  a.bangumItem .textBox{
            text-overflow:ellipsis;overflow:hidden;
			position: relative;
			z-index: 1;
			height: 100%;
          }
          a.bangumItem div.jinduBG{
            height:16px;
            width: 100%;
            background-color:gray;
            display:inline-block;
            border-radius:4px;
			position: absolute;
    		bottom: 3px;
          }
          a.bangumItem div.jinduFG
          {
            height:16px;
            background-color:#ff8c83;
            border-radius:4px;
			position: absolute;
			bottom: 0px;
			z-index: 1;
          }
          a.bangumItem div.jinduText
          {
            width:100%;height:auto;
            text-align:center;
            color:#fff;
            line-height:15px;
            font-size:15px;
			position: absolute;
			bottom: 0px;
			z-index: 2;
          }
		  @media screen and (max-width:1000px) { 
			   a.bangumItem{
					width:95%;
				}
			}
          </style>
        ";
      foreach ($this->myCollection as $value) {
        //print_r($value);
        //$id = $value->subject->id;
		$epsNum = '未知';
		if(@$value->subject->eps){
			$epsNum = $value->subject->eps;
		}
        $progressNum = $value->ep_status;
        $myProgress = $progressNum . "/" . $epsNum;
        $name = $value->name;
        $name_cn = $value->subject->name_cn;
		if(@!$name_cn){
			$name_cn = $name;
		}
        $air_date = $value->subject->air_date;
		$theurl = $value->subject->url;
        $img_grid =str_replace("http://", "https://", $value->subject->images->common);
		$progressWidth = 0;
		if($epsNum=='未知'){
			$progressWidth = 100;
		}else{
			$progressWidth = $progressNum / $epsNum * 100;
			if($progressWidth>100){
				$progressWidth = 100;
			}
		}
        echo "
          <a href=" . $theurl ." target='_blank' class='bangumItem'>
            <img src='$img_grid' />
            <div class='textBox'>$name_cn<br>
            $name<br>
			首播日期：$air_date<br>
            <div class='jinduBG'>
            <div class='jinduText'>进度:$myProgress</div>
            <div class='jinduFG' style='width:" . $progressWidth . "%;'>
            </div>
            </div>
            </div>
          </a>";
		  //echo print_r($value);
      }
      break;
      case false:
                echo $myCollection;
      break;
      default:
            break;
    }
  }
  //get获取内容
  private static function curl_get_contents($_url)
      {
    //echo "The GET Url You Request is <span style='color:#ff8c83'>" . $_url . "</span><br/>";
    $myCurl = curl_init($_url);
    //不验证证书
    curl_setopt($myCurl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($myCurl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($myCurl,  CURLOPT_HEADER, false);
    //获取
    $content = curl_exec($myCurl);
    //关闭
    curl_close($myCurl);
    return $content;
  }
  //post获取内容
  private static function curl_post_contents($_url,$_postdata)
      {
    //echo "The POST Url You Request is <span style='color:#ff8c83'>" . $_url . "</span><br/>";
    $myCurl = curl_init($_url);
    //不验证证书
    curl_setopt($myCurl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($myCurl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($myCurl, CURLOPT_POST, 1);
    curl_setopt($myCurl, CURLOPT_POSTFIELDS, $_postdata);
    $output = curl_exec($myCurl);
    curl_close($myCurl);
    return $output;
  }
}

class WikimoeBangumi_Action extends Widget_Abstract_Contents implements Widget_Interface_Do 
{

    public function action()
    {
        $options = Helper::options();
		$userID = $options->plugin('WikimoeBangumi')->userID;
		$password = $options->plugin('WikimoeBangumi')->password;

		$bangum = BangumiAPI::GetInstance();
		$bangum->init($userID,$password);
		$bangum->ParseCollection();
		$bangum->PrintCollecion(true);
    }
}