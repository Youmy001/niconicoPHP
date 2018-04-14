<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/28
 * Time: 2:02
 */

namespace NicoPHP;


class NiconicoUrls {

	private static $domain_base = ".nicovideo.jp/";

	private static $live_api_community_url_base = "http://watch.live.nicovideo.jp/api/";
	private static $live_api_official_or_channel_url_base = "http://ow.live.nicovideo.jp/api/";
	private static $live_api_external_url_base = "http://ext.live.nicovideo.jp/api/";

	private static $ext_url_base = "http://ext.nicovideo.jp/";
	private static $ext_api_url_base = "http://ext.nicovideo.jp/api/";
	private static $ext_search_url_base = "http://ext.nicovideo.jp/search/";

	/** No Constructor. Static Class */
	private function __construct() {}

	/* Authentication */

	private static $authentication_base = "https://secure.nicovideo.jp/secure/";

	/**
	 * Authentication Login URL
	 * @return string
	 */
	public static function login_url () {
		return self::$authentication_base . "login?site=niconico";
	}

	/**
	 * Authentication Logout URL
	 * @return string
	 */
	public static function logout_url () {
		return self::$authentication_base . "logout";
	}

	/* Videos */

	private static $video_url_base = "http://www.nicovideo.jp/";
	private static $video_api_url_base = "http://www.nicovideo.jp/api/";
	private static $video_flapi_url_base = "http://flapi.nicovideo.jp/api/";
	public static $video_keyword_search_api_url = "http://ext.nicovideo.jp/search/search/";
	public static $video_tag_search_url_dase = "http://ext.nicovideo.jp/search/tag/";
	public static $related_video_api_url = "http://api.ce.nicovideo.jp/nicoapi/v1/video.relation";

	/**
	 * Niconico Top Page URL Text
	 * @return string
	 */
	public static function top_page_url () {
		return self::$video_url_base;
	}

	public static function video_top_page_url () {
		return self::$video_url_base . "my/top";
	}

	public static function video_my_page_url () {
		return self::$video_url_base . "video_top";
	}

	public static function video_watch_page_url () {
		return self::$video_url_base . "watch/";
	}

	public static function video_flv_url () {
		return self::$video_flapi_url_base . 'getflv/';
	}

	public static function video_thumbnail_info_url () {
		return "http://ext.nicovideo.jp/api/getthumbinfo/";
	}

	public static function video_history_url () {
		return self::$video_api_url_base . "videoviewhistory/list";
	}

	public static function video_remove_url () {
		return self::$video_api_url_base . "videoviewhistory/remove?token=";
	}

	public static function video_thread_key_api_url () {
		return self::$video_flapi_url_base . "getthreadkey?thread=";
	}

	public static function make_keyword_search_url ($keyword = "", $page = 0, $sort = "", $order = "") {
		return self::$video_keyword_search_api_url . "{$keyword}?mode=watch&page={$page}&sort={$sort}&order={$order}";
	}

	public static function make_tag_search_url ($tag = "", $page = 0, $sort = "", $order = "") {
		return self::$video_tag_search_url_dase . "{$tag}?mode=watch&page={$page}&sort={$sort}&order={$order}";
	}

	/* Lives */

	private static $live_url_base = "http://live.nicovideo.jp/";
	private static $live_api_url_base = "http://live.nicovideo.jp/api/";

	public static function live_top_page_url () {
		return self::$live_url_base;
	}

	public static function live_my_page_url () {
		return self::$live_url_base . "my";
	}

	public static function live_watch_page_url () {
		return self::$live_url_base . "watch/";
	}

	public static function live_gate_page_url () {
		return self::$live_url_base . "gate/";
	}

	public static function live_ckey_url () {
		return self::$live_api_url_base . "getckey";
	}

	public static function live_player_status_url () {
		return self::$live_api_url_base . "getplayerstatus/";
	}

	public static function live_post_key_url () {
		return self::$live_api_url_base . "getpostkey";
	}

	public static function live_vote_url () {
		return self::$live_api_url_base . "vote";
	}

	public static function live_heartbeat_url () {
		return self::$live_api_url_base . "heartbeat";
	}

	public static function live_leave_url () {
		return self::$live_api_url_base . "leave";
	}

	public static function live_tag_revision_url () {
		return self::$live_api_url_base . "tagrev/";
	}

	public static function live_zapping_list_index_url () {
		return self::$live_api_url_base . "getzappinglist?zroute=index";
	}

	public static function live_zapping_list_recent_url () {
		return self::$live_api_url_base . "getzappinglist?zroute=recent";
	}

	public static function live_index_zero_stream_list_url () {
		return self::$live_api_url_base . "getindexzerostreamlist?status=";
	}

	public static function live_watching_reservation_list_url () {
		return self::$live_api_url_base . "watchingreservation?mode=list";
	}

	public static function live_watching_reservation_detail_list_url () {
		return self::$live_api_url_base . "watchingreservation?mode=detaillist";
	}

	/* Images */

	private static $image_url_base = "http://seiga.nicovideo.jp/";
	private static $image_api_url_base = "http://seiga.nicovideo.jp/api/";
	private static $image_ext_api_url_base = "http://ext.seiga.nicovideo.jp/api/";

	public static function image_top_page_url () {
		return self::$image_url_base;
	}

	public static function image_my_page_url () {
		return self::$image_url_base . "my";
	}

	public static function image_theme_top_page_url () {
		return self::$image_url_base . "theme/";
	}

	public static function image_illust_top_page_url () {
		return self::$image_url_base . "illust/";
	}

	public static function image_illust_adult_top_page_url () {
		return self::$image_url_base . "shunga/";
	}

	public static function image_manga_top_page_url () {
		return self::$image_url_base . "manga/";
	}

	public static function image_electronic_book_top_page_url () {
		return self::$image_url_base . "book/";
	}

	public static function image_blog_parts_url () {
		return self::$image_ext_api_url_base . "illust/blogparts?mode=";
	}

	public static function image_user_info_url () {
		return self::$image_api_url_base . "user/info?id=";
	}

	public static function image_user_data_url () {
		return self::$image_api_url_base . "user/data?id=";
	}

	/* Searches */

	private static $search_api_url_base = "http://api.search.nicovideo.jp/api/";

	public static function search_suggestion_url () {
		return "http://search.nicovideo.jp/suggestion/complete/";
	}

	/* Dictionaries */

	private static $dictionary_url_base = "http://dic.nicovideo.jp/";
	private static $dictionary_api_url_base = "http://api.nicodic.jp/";

	public static function dictionary_top_page_url () {
		return self::$dictionary_url_base;
	}

	public static function dictionary_word_exist_url () {
		return self::$dictionary_api_url_base . "e/json/";
	}

	public static function dictionary_summary_url () {
		return self::$dictionary_api_url_base . "page.summary/json/a/";
	}

	public static function dictionary_exist_url () {
		return self::$dictionary_api_url_base . "page.exist/json/";
	}

	public static function dictionary_recent_url () {
		return self::$dictionary_api_url_base . "page.created/json";
	}

	/* Apps */

	private static $app_url_base = "http://app.nicovideo.jp/";

	public static function app_top_page_url () {
		return self::$app_url_base;
	}

	public static function app_my_page_url () {
		return self::$app_url_base . "my/apps";
	}

	/* Communities */

	private static $community_url_base = "http://com.nicovideo.jp/";
	
	public static function community_search_page_url () {
		return self::$community_url_base . "search/";
	}
	
	public static function community_summary_page_url () {
		return self::$community_url_base . "community/";
	}
	
	public static function community_icon_url () {
		return "http://icon.nimg.jp/community/{0}/co{1}.jpg";
	}
	
	public static function community_small_icon_url () {
		return "http://icon.nimg.jp/community/s/{0}/co{1}.jpg";
	}
	
	public static function community_blank_icon_url () {
		return "http://icon.nimg.jp/404.jpg";
	}
	
	/* Channels */
	
	public static function channel_icon_url () {
		return "http://icon.nimg.jp/channel/ch{0}.jpg";
	}
	
	public static function channel_small_icon_url () {
		return "http://icon.nimg.jp/channel/s/ch{0}.jpg";
	}
	
	/* Users */
	
	private static $user_page_url_base = "http://www.nicovideo.jp/user/";
	private static $user_fav_api_base = "http://www.nicovideo.jp/api/watchitem/";
	
	public static function user_page_url () {
		return self::$video_url_base . "my";
	}
	
	public static function user_icon_url () {
		return "http://usericon.nimg.jp/usericon/{0}/{1}.jpg";
	}
	
	public static function user_small_icon_url () {
		return "http://usericon.nimg.jp/usericon/s/{0}/{1}.jpg";
	}
	
	public static function user_blank_icon_url () {
		return "http://uni.res.nimg.jp/img/user/thumb/blank.jpg";
	}
	
	public static function make_user_page_url ($user_id) {
		return self::$user_page_url_base . $user_id;
	}

	public static function make_user_mylist_rss_url ($user_id)  {
		return self::make_user_page_url($user_id) . "/mylist?rss=2.0";
	}
	
	public static function make_user_video_rss_url ($user_id, $page, $sort_method = null, $sort_direction = null) {
		
		if ($page < 1) {
			throw new \InvalidArgumentException("Page cannot be lower than one");
		}
		
		$url = "http://www.nicovideo.jp/user/{$user_id}/video?rss=2.0&page={$page}";
		
		if ($sort_method) {
			$url .= "&sort={$sort_method}";
		}
		
		if ($sort_direction) {
			$url .= "&order={$sort_direction}";
		}
		
		return $url;
		
	}
	
	public static function ce_user_api_url () {
		return "http://api.ce.nicovideo.jp/api/v1/user.info";
	}
	
	
	

}