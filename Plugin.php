<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Bangumi追番表
 * 
 * @package WikimoeBangumi
 * @author 广树
 * @version 1.0.0
 * @link http://www.wikimoe.com
 */
class WikimoeBangumi_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        //Typecho_Plugin::factory('Widget_Archive')->header = array('WikimoeBangumi_Plugin', 'header');
        //Typecho_Plugin::factory('Widget_Archive')->footer = array('WikimoeBangumi_Plugin', 'footer');
		Helper::addRoute("route_WikimoeBangumi","/WikimoeBangumi","WikimoeBangumi_Action",'action');
		//Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('WikimoeBangumi_Plugin', 'setak');
		//Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('WikimoeBangumi_Plugin', 'setak');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
		Helper::removeRoute("route_WikimoeBangumi");
	}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
       /**表单设置 */
		$userID = new Typecho_Widget_Helper_Form_Element_Text('userID', NULL, NULL, _t('输入Bangumi账号'));
        $form->addInput($userID);
		$password = new Typecho_Widget_Helper_Form_Element_Password('password', NULL, NULL, _t('输入Bangumi密码（推荐设置成不是常用的密码）'));
        $form->addInput($password);
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
	 
	 /**
     * 页头输出CSS
     *
     * @access public
     * @param unknown header
     * @return unknown
     */
    public static function header() {
        $Path = Helper::options()->pluginUrl . '/WikimoeBangumi/';
        echo '<link rel="stylesheet" type="text/css" href="' . $Path . 'css/css.css" />';
    }
	
	public static function footer() {
        $Path = Helper::options()->pluginUrl . '/WikimoeBangumi/';
        echo '<script src="'. $Path .'js/js.js"></script>';
    }
	
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
   	public static function output()
    {
		$Path = Helper::options()->pluginUrl . '/WikimoeBangumi/';
		echo '<link rel="stylesheet" type="text/css" href="' . $Path . 'css/css.css" />';
        echo '<div id="bangumiBody">
        	<div class="bangumi_loading">
            <div class="loading-anim">
                <div class="border out"></div>
                <div class="border in"></div>
                <div class="border mid"></div>
                <div class="circle">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
                <div class="bangumi_loading_text">追番数据加载中...</div>
            </div>
            </div>

        
        </div>
        
        <div style="clear:both"></div>';
		echo "
		<script>
		setTimeout(function(){
			jQuery.ajax({
				type: 'GET',
				url: '". Helper::options()->siteUrl ."index.php/WikimoeBangumi',
				success: function(res) {
					$('#bangumiBody').empty().append(res);
			
				},
				error:function(){
					$('#bangumiBody').empty().text('加载失败');
				}
			});
		},500)
		</script>
		
		";
    }
}