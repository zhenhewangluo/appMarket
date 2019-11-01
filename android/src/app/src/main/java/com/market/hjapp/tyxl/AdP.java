package com.market.hjapp.tyxl;

import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.drawable.Drawable;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.RotateAnimation;
import android.widget.Button;
import android.widget.Gallery;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.adapter.GridViewImageAdapter;
import com.market.hjapp.tyxl.adapter.ImageAndTextListAdapter;
import com.market.hjapp.tyxl.object.Actives;
import com.market.hjapp.tyxl.object.AsyncImageLoader3;
import com.market.hjapp.tyxl.object.CustomDialog;
import com.market.hjapp.tyxl.object.HttpUrl;
import com.market.hjapp.tyxl.object.ImageAndText;
import com.market.hjapp.tyxl.object.MD5;
import com.market.hjapp.tyxl.object.Records;
import com.market.hjapp.ui.activity.BrowseSuggestedAppListActivity;

public class AdP extends Activity implements Runnable {
	/***
	 * 活动展示页面
	 */
	// 分栏内容
	View mFirstHeaderTab;
	View mFirstPressedHeaderTab;
	View mSecondHeaderTab;
	View mSecondPressedHeaderTab;
	View mThirdHeaderTab;
	View mThirdPressedHeaderTab;

	View mTreasure;
	View mExchange;
	View mLottery;

	// 提示框标记
	private static final int DIALOG_WBE = 1;
	private static final int CUSTOM_DIALOG = 2;
	private static final int Result_DIALOG = 3;
	private static final int Back_DIALOG = 4;
	private static final int noActive_DIALOG = 5;

	public static ArrayList<Gift> Gift_vector = new ArrayList<Gift>();
	public static ArrayList<Gift> Chou_Gift_vector = new ArrayList<Gift>();
	MD5 md5;
	// 总积分
	TextView myScore;
	// 用户积分
	public static String Str_myScore;
	public static String Str_lotteryScore;
	static AdP gift;
	static ArrayList<GiftInfo> contactList = new ArrayList<GiftInfo>();
	// 专区数据
	public static ArrayList<Actives> A_vector = new ArrayList<Actives>();
	// static ArrayList<GiftInfo> gridviewList = new ArrayList<GiftInfo>();
	// protected GiftAdapter ca;
	protected GridViewImageAdapter gv;
	protected static int giftItem;
	public Bitmap lotteryBitmap;
	// ==============================================
	// 登陆结果-- 0 失败 、1成功
	private static String Login;
	String activesId = "";
	String activesState = "";
	private AsyncImageLoader3 asyncImageLoader;
	// ==============================================
	Button chou_jiang, invite, support;
	int mCurHeaderTab = -1;

	GridView gridview;
	private ListView listView;
	LinearLayout unland_renqi, land_renqi, lay_myScore, unland, outline,
			online;
	ImageAndTextListAdapter adapter = null;
	GridViewImageAdapter gridviewAdapter;
	List<ImageAndText> dataArray = new ArrayList<ImageAndText>();
	List<ImageAndText> Chou_dataArray = new ArrayList<ImageAndText>();
	ImageView img, imageView;
	String str_exchange_product_isgoods;
	TextView active_participate, describe, time, intro, votecount, voteranking;

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		// setContentView(R.layout.adp_activity);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.participate);

		new Thread(this).start();
		gift = this;
		md5 = new MD5();

		DisplayMetrics dm = new DisplayMetrics();
		getWindowManager().getDefaultDisplay().getMetrics(dm);
		String strOpt = "手机分辨率为：" + dm.widthPixels + "X" + dm.heightPixels;
		Log.v("手机分辨率为：", strOpt);
		System.out.println(strOpt);

		/**
		 * mTreasure-我要夺宝、mExchange-积分换礼、mLottery-抽奖
		 */
		/*
		 * 注释掉兑奖抽奖 mTreasure = findViewById(R.id.treasure); mExchange =
		 * findViewById(R.id.exchange); mLottery = findViewById(R.id.lottery);
		 * myScore = (TextView) findViewById(R.id.myScore);
		 * 
		 * TextView pressed, unpressed; View headInfo = (View)
		 * findViewById(R.id.header_info); pressed = (TextView) headInfo
		 * .findViewById(R.id.first_headertab_pressed_text); unpressed =
		 * (TextView) headInfo.findViewById(R.id.first_headertab);
		 * pressed.setText(R.string.set_treasure);
		 * unpressed.setText(R.string.set_treasure);
		 * 
		 * pressed = (TextView) headInfo
		 * .findViewById(R.id.second_headertab_pressed_text); unpressed =
		 * (TextView) headInfo.findViewById(R.id.second_headertab);
		 * pressed.setText(R.string.set_exchange);
		 * unpressed.setText(R.string.set_exchange);
		 * 
		 * pressed = (TextView) headInfo
		 * .findViewById(R.id.third_headertab_pressed_text); unpressed =
		 * (TextView) headInfo.findViewById(R.id.third_headertab);
		 * pressed.setText(R.string.set_lottery);
		 * unpressed.setText(R.string.set_lottery);
		 * 
		 * lotteryBitmap = BitmapFactory.decodeResource(gift.getResources(),
		 * R.drawable.default_lottery);
		 * 
		 * gridview = (GridView) findViewById(R.id.gridView1);
		 * gridview.setNumColumns(3); gridview.setClickable(false);
		 * gridview.setLongClickable(false); gridview.setClipChildren(false);
		 * listView = (ListView) findViewById(R.id.listView);
		 */
		imageView = (ImageView) findViewById(R.id.imageView1);
		describe = (TextView) findViewById(R.id.textView2);
		time = (TextView) findViewById(R.id.textView4);
		intro = (TextView) findViewById(R.id.textView6);
		votecount = (TextView) findViewById(R.id.textView7);
		voteranking = (TextView) findViewById(R.id.textView8);
		unland_renqi = (LinearLayout) findViewById(R.id.linearLayout5);
		land_renqi = (LinearLayout) findViewById(R.id.linearLayout4);
		lay_myScore = (LinearLayout) findViewById(R.id.my_Score);
		unland = (LinearLayout) findViewById(R.id.lay_unland);
		online = (LinearLayout) findViewById(R.id.linearLayout2);
		outline = (LinearLayout) findViewById(R.id.linearLayout6);

		// chou_jiang = (Button) findViewById(R.id.chou_jiang);
		invite = (Button) findViewById(R.id.invite);
		support = (Button) findViewById(R.id.support);

		// chou_jiang.setOnClickListener(new btnClickListener());
		invite.setOnClickListener(new btnClickListener());
		support.setOnClickListener(new btnClickListener());

		// 获取活动信息
		GetActivesWeb();

		/**
		 * 分类界面内容
		 */
		// initHeaderTabs();
		// if (!Records.loadStringRecord(gift,
		// AdPlatform.Rms_Registration).equals("")) {
		// // 读取记录 手机号、密码
		// String[] Str = Common
		// .mySplict(Records.loadStringRecord(gift,
		// AdPlatform.Rms_Registration), '|');
		// if (Str.length > 0) {
		// AdPlatform.phone = Str[0];
		// AdPlatform.password = Str[1];
		// }
		// // 结束线程
		// AdPlatform.isRunLogo1 = false;
		// // new LandTask().execute("");
		// } else {
		// // // 获取主界面相关数据
		// // // 跳转主界面
		// // // 切换Activity 主界面
		// AdPlatform.isRunLogo1 = false;
		// }

		if (AdPlatform.mainState != 10) {
			new GetVoteTomeTask().execute();
		}
	}

	// 登录状态
	public static String getLogin() {
		return Login;
	}

	public static void setLogin(String login) {
		Login = login;
	}

	/**
	 * 获取兑奖列表
	 * 
	 * @author Administrator
	 * 
	 */

	public void DuiJiangDate() {

		try {
			String exchangelist_url = HttpUrl.httpStr
					+ "active/exchangelist.action?userid=" + AdPlatform.userId
					+ "&password=" + md5.getMD5ofStr(AdPlatform.password);
			String exchangelist_out = HttpUrl.setGetUrl(exchangelist_url);
			JSONObject exchangelist_jsonObject = new JSONObject(
					exchangelist_out);
			JSONArray exchangelist_jsonArray = exchangelist_jsonObject
					.getJSONArray("exchanges");

			for (int i = 0; i < exchangelist_jsonArray.length(); i++) {
				JSONObject exchangelist_jsonObject_0 = (JSONObject) exchangelist_jsonArray
						.opt(i);
				Gift gift = new Gift();
				gift.giftId = exchangelist_jsonObject_0.getString("id");
				gift.giftName = exchangelist_jsonObject_0.getString("name");
				// gift.Picurl = HttpUrl.returnBitMap(exchangelist_jsonObject_0
				// .getString("picurl"));
				int sda = Integer.parseInt(exchangelist_jsonObject_0
						.getString("begin"));
				gift.newnum = exchangelist_jsonObject_0.getString("end");// 剩余数量
				int sd = Integer.parseInt(gift.newnum);
				gift.oldnum = Integer.valueOf(sda - sd).toString();// 已兑数量
				gift.URL = exchangelist_jsonObject_0.getString("picurl");
				gift.Score = exchangelist_jsonObject_0.getString("score");

				if (!IsGiftIn(Gift_vector, gift.giftId)) {
					ImageAndText image_text = new ImageAndText(gift.URL,
							gift.giftId, gift.giftName, gift.Score, gift.URL,
							gift.oldnum, gift.newnum);
					dataArray.add(image_text);
					Gift_vector.add(gift);
				}
			}

			// lotteryScore(抽奖所需积分)??myScore(我的积分)
			JSONObject exchangelist_lottery_jsonObject = new JSONObject(
					exchangelist_out);
			Str_lotteryScore = exchangelist_lottery_jsonObject
					.getString("lotteryScore");
			Str_myScore = exchangelist_lottery_jsonObject.getString("myScore");
		} catch (Exception e) {
			WebFailureDialog(gift);
		}

	}

	private void ChouJiang() {

		try {
			String exchangelist_url = HttpUrl.httpStr
					+ "active/lotterylist.action?userid=" + AdPlatform.userId
					+ "&password=" + md5.getMD5ofStr(AdPlatform.password);
			Log.v("exchangelist_url111111", "111111111111111"
					+ exchangelist_url);
			String exchangelist_out = HttpUrl.setGetUrl(exchangelist_url);
			Log.v("exchangelist_out111111", "111111111111111"
					+ exchangelist_out);
			JSONObject exchangelist_jsonObject = new JSONObject(
					exchangelist_out);
			JSONArray exchangelist_jsonArray = exchangelist_jsonObject
					.getJSONArray("lotteryPrizeInfos");

			for (int i = 0; i < exchangelist_jsonArray.length(); i++) {
				JSONObject exchangelist_jsonObject_0 = (JSONObject) exchangelist_jsonArray
						.opt(i);
				Gift gift = new Gift();
				gift.giftId = exchangelist_jsonObject_0.getString("id");
				gift.giftName = exchangelist_jsonObject_0.getString("name");
				// gift.Picurl = HttpUrl.returnBitMap(exchangelist_jsonObject_0
				// .getString("picurl"));
				int sda = Integer.parseInt(exchangelist_jsonObject_0
						.getString("begin"));
				gift.newnum = exchangelist_jsonObject_0.getString("end");// 剩余数量
				int sd = Integer.parseInt(gift.newnum);
				gift.oldnum = Integer.valueOf(sda - sd).toString();// 已兑数量
				gift.URL = exchangelist_jsonObject_0.getString("picurl");
				gift.Score = exchangelist_jsonObject_0.getString("score");

				Log.v("gift.URL", gift.giftId);
				if (!IsGiftIn(Chou_Gift_vector, gift.giftId)) {
					ImageAndText image_text = new ImageAndText(gift.URL,
							gift.giftId, gift.giftName, gift.Score, gift.URL,
							gift.oldnum, gift.newnum);
					Chou_dataArray.add(image_text);
					Chou_Gift_vector.add(gift);
				}
			}

			// lotteryScore(抽奖所需积分)??myScore(我的积分)
			JSONObject exchangelist_lottery_jsonObject = new JSONObject(
					exchangelist_out);
			Str_lotteryScore = exchangelist_lottery_jsonObject
					.getString("lotteryScore");
			Str_myScore = exchangelist_lottery_jsonObject.getString("myScore");
		} catch (Exception e) {
			WebFailureDialog(gift);
		}

	}

	/* 获取活动内容 */
	protected void GetActivesWeb() {
		try {
			online.setVisibility(0);
			outline.setVisibility(8);
			String actives_url;

			if (AdPlatform.mainState != 10) {
				actives_url = HttpUrl.httpStr
						+ "active/activelist.action?userid="
						+ AdPlatform.userId;
			} else {
				actives_url = HttpUrl.httpStr + "active/activelist.action";
			}
			String actives_out = HttpUrl.setGetUrl(actives_url);

			Log.v("actives_url", "" + actives_url);
			Log.v("actives_out11111111111", "" + actives_out);
			JSONObject actives_jsonObject = new JSONObject(actives_out);
			JSONArray actives_jsonArray = actives_jsonObject
					.getJSONArray("actives");
			Actives actives = null;
			for (int i = 0; i < actives_jsonArray.length(); i++) {
				JSONObject actives_jsonObject_0 = (JSONObject) actives_jsonArray
						.opt(i);
				actives = new Actives();
				// 活动id
				actives.ActivesId = actives_jsonObject_0.getString("id");
				activesId = actives.ActivesId;
				// 活动名
				actives.ActivesName = actives_jsonObject_0.getString("name");
				// 在线人数
				actives.PartinCount = getResources().getString(
						R.string.partincount)
						+ actives_jsonObject_0.getString("partincount");
				// 活动列表展示图url
				actives.Str_Picurl = actives_jsonObject_0.getString("picurl1");
				// 活动专区展示图url
				actives.Str_Picur2 = actives_jsonObject_0.getString("picurl2");
				// 人气值 专区中显示
				actives.votecount = actives_jsonObject_0.getString("votecount");
				// 人气排名 专区中显示
				actives.voteranking = actives_jsonObject_0
						.getString("voteranking");
				actives.describe = actives_jsonObject_0
						.getString("description");
				actives.start_time = actives_jsonObject_0.getString("begin");
				actives.end_time = actives_jsonObject_0.getString("end");
				if (!IsContainActives(A_vector, actives.ActivesId)) {
					A_vector.add(actives);
				}
			}
			Log.v("A_vector.size()", "" + A_vector.size());
			asyncImageLoader = new AsyncImageLoader3();
			loadImage4(A_vector.get(0).Str_Picurl, R.id.imageView1);

			describe.setText(A_vector.get(0).describe);
			// time.setText(A_vector.get(0).start_time.substring(0, 10) + "~"
			// + A_vector.get(0).end_time.substring(0, 10));
			if (actives.end_time.equals("")) {
				time.setText(actives.start_time);
			} else {
				time.setText(actives.start_time + "至" + actives.end_time);
			}
			intro.setText(A_vector.get(0).ActivesName);
			Log.v("activesID", "" + activesId);
			votecount.setText(getResources().getString(R.string.my_popularity)
					+ actives.votecount);
			voteranking.setText(getResources().getString(R.string.popularity)
					+ getResources().getString(R.string.di)
					+ actives.voteranking
					+ getResources().getString(R.string.location));
			// if (activesId.equals("10000")) {
			// Log.v("AdPlatform.mainState00000000", "" + AdPlatform.mainState);
			// unland_renqi.setVisibility(0);
			// land_renqi.setVisibility(8);
			// }
			if (AdPlatform.mainState != 10) {
				Log.v("AdPlatform.mainState11111111", "" + AdPlatform.mainState);
				unland_renqi.setVisibility(8);
				land_renqi.setVisibility(0);
			} else {
				Log.v("AdPlatform.mainState22222222", "" + AdPlatform.mainState);
				unland_renqi.setVisibility(0);
				land_renqi.setVisibility(8);

			}
			// JSONObject prizes_jsonObject = new JSONObject(actives_out);
			// JSONArray prizes_jsonArray = prizes_jsonObject
			// .getJSONArray("prizes");
			// for (int i = 0; i < prizes_jsonArray.length(); i++) {
			// JSONObject prizes_jsonObject_0 = (JSONObject) prizes_jsonArray
			// .opt(i);
			// Prizes prizes = new Prizes();
			// // 活动名
			// prizes.activeName = prizes_jsonObject_0.getString("activeName");
			// // 奖项
			// prizes.level = prizes_jsonObject_0.getString("level");
			// // 奖品
			// prizes.productName = prizes_jsonObject_0
			// .getString("productName");
			// // 中奖号码
			// prizes.winnerPhone = prizes_jsonObject_0
			// .getString("winnerPhone");
			// if (!IsContainPrizes(A_prizes, prizes.winnerPhone)) {
			// A_prizes.add(prizes);
			// }
			// }
		} catch (Exception e) {
			WebFailureDialog(gift);
			online.setVisibility(8);
			outline.setVisibility(0);
			Common.onlineOrnot = false;
			Log.v("E-GetActivesWeb", "" + e);
		}
	}

	/**
	 * 兑奖
	 * 
	 * @author Administrator
	 * 
	 */
	public void GetExchangeTask(int position) {

		String Str_exchangelogid = null;
		String Str_result = null;
		String exchange_url = HttpUrl.httpStr
				+ "active/exchange.action?userid=" + AdPlatform.userId
				+ "&password=" + md5.getMD5ofStr(AdPlatform.password)
				+ "&exchangeid=" + Gift_vector.get(position).giftId;

		String exchange_out = HttpUrl.setGetUrl(exchange_url);
		Log.v("exchange_out", "" + exchange_out);

		JSONObject exchange_product_jsonObject;
		try {
			exchange_product_jsonObject = new JSONObject(exchange_out)
					.getJSONObject("product");

			String str_exchange_product_id = exchange_product_jsonObject
					.getString("id");
			String str_exchange_product_name = exchange_product_jsonObject
					.getString("name");
			str_exchange_product_isgoods = exchange_product_jsonObject
					.getString("isgoods");

			JSONObject exchange_result_jsonObject = new JSONObject(exchange_out);
			Str_exchangelogid = exchange_result_jsonObject
					.getString("exchangelogid");
			Str_result = exchange_result_jsonObject.getString("result");
			Str_myScore = exchange_result_jsonObject.getString("myScore");
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		// result>0：成功
		// result<0：失败
		// -1：积分不够
		// -2：奖品数量不足
		if (Integer.parseInt(Str_result) > 0) {
			alertResultDialog(gift, 0, null, null, Str_exchangelogid, null, "1");
		} else if (Str_result.equals("-1")) {
			alertResultDialog(gift, 1, null, null, Str_exchangelogid, null, "1");
		} else if (Str_result.equals("-2")) {
			alertResultDialog(gift, 2, null, null, Str_exchangelogid, null, "1");
		}
		myScore.setText(getResources().getString(R.string.my_grade)
				+ Str_myScore);
		removeDialog(DIALOG_WBE);
	}

	/**
	 * 抽奖
	 * 
	 * @author Administrator
	 * 
	 */
	private void GetLotteryTask() {

		// 抽奖--
		String str_lottery_product_id = null;
		String str_lottery_product_name = null;
		// 抽奖结果
		String Str_result = null, Str_lotterylogid = null, str_lottery_product_picurl = null;
		try {
			String lottery_url = HttpUrl.httpStr
					+ "active/lottery.action?userid=" + AdPlatform.userId
					+ "&password=" + md5.getMD5ofStr(AdPlatform.password);
			Log.v("lottery_url", "" + lottery_url);
			String lottery_out = HttpUrl.setGetUrl(lottery_url);
			Log.v("lottery_out", "" + lottery_out);
			JSONObject lottery_product_jsonObject = new JSONObject(lottery_out)
					.getJSONObject("product");
			str_lottery_product_id = lottery_product_jsonObject.getString("id");
			str_exchange_product_isgoods = lottery_product_jsonObject
					.getString("isgoods");
			str_lottery_product_name = lottery_product_jsonObject
					.getString("name");
			str_lottery_product_picurl = lottery_product_jsonObject
					.getString("picurl");
			JSONObject lottery_result_jsonObject = new JSONObject(lottery_out);
			Str_lotterylogid = lottery_result_jsonObject
					.getString("lotterylogid");
			Str_result = lottery_result_jsonObject.getString("result");
			Str_myScore = lottery_result_jsonObject.getString("myScore");
			// result>=0：成功 0：未中 1：中奖
			// result<0：失败
			// -1：积分不够
			// -9：数据异常

		} catch (Exception e) {
			WebFailureDialog(gift);
		}
		if (Integer.parseInt(Str_result) > 0) {
			alertResultDialog(gift, 3, str_lottery_product_id,
					str_lottery_product_name, Str_lotterylogid,
					str_lottery_product_picurl, "2");
		} else if (Str_result.equals("0")) {
			alertResultDialog(gift, 4, null, null, Str_lotterylogid, null, "2");
		} else if (Str_result.equals("-1")) {
			alertResultDialog(gift, 5, null, null, Str_lotterylogid, null, "2");
		} else if (Str_result.equals("-9")) {
			alertResultDialog(gift, 6, null, null, Str_lotterylogid, null, "2");
		}

		myScore.setText("总积分:" + Str_myScore);
		removeDialog(DIALOG_WBE);
	}

	/**
	 * 参加活动
	 * 
	 * @author user
	 * 
	 */
	private class GetVoteTomeTask extends AsyncTask<String, String, String> {
		// 参加结果
		String str_votetome_result;

		// 返回人气值
		// String str_votetome_votecount;

		public String doInBackground(String... params) {
			try {
				// 用户id、密码、活动id
				String votetome_url = HttpUrl.httpStr
						+ "vote/makeActive.action?userid=" + AdPlatform.userId
						+ "&password=" + md5.getMD5ofStr(AdPlatform.password)
						+ "&activeid=" + activesId;
				Log.v("votetome_url", "" + votetome_url);
				String votetome_out = HttpUrl.setGetUrl(votetome_url);
				Log.v("votetome_out000000000000000000", "" + votetome_out);

				JSONObject votetome_jsonObject = new JSONObject(votetome_out);
				// 参加活动的结果 0是失败，1是成功
				str_votetome_result = votetome_jsonObject.getString("result");
				// str_votetome_votecount = votetome_jsonObject
				// .getString("votecount");

				Log.v("str_votetome_result", "" + str_votetome_result);
				// Log.v("str_votetome_votecount", "" + str_votetome_votecount);
			} catch (Exception e) {
				WebFailureDialog(gift);
				Log.v("E-GetVoteTomeTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			// if (str_votetome_result.equals("1")) {
			// ParticipateDo(Main, 0, str_votetome_votecount);
			// } else {
			// ParticipateDo(Main, 1, str_votetome_votecount);
			// }
			removeDialog(DIALOG_WBE);
		}

	}

	/**
	 * 兑奖、抽奖结果 提示框
	 * 
	 * @param activity
	 * @param Item
	 * @param id
	 * @param name
	 * @param logid
	 * @param urlImg
	 * @param type
	 */
	private void alertResultDialog(Activity activity, final int Item,
			String id, String name, final String logid, String urlImg,
			final String type) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		switch (Item) {
		case 0:// 兑奖提示
			alertDialog.setTitle(getResources().getString(R.string.exchange)
					+ getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message1));
			break;
		case 1:
			alertDialog.setTitle(getResources().getString(R.string.exchange)
					+ getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message2));
			break;
		case 2:
			alertDialog.setTitle(getResources().getString(R.string.exchange)
					+ getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message3));
			break;
		case 3:
			alertDialog.setTitle(getResources().getString(R.string.lottery)
					+ getResources().getString(R.string.alert));
			ImageView image = new ImageView(gift);
			if (urlImg.equals("") || urlImg.equals(null)
					|| urlImg.equals("null")) {
				image.setImageBitmap(lotteryBitmap);
			} else {
				image.setImageBitmap(HttpUrl.returnBitMap(urlImg));
			}
			image.setScaleType(ImageView.ScaleType.CENTER);
			alertDialog.setView(image);
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message4)
					+ name);
			break;
		case 4:
			alertDialog.setTitle(getResources().getString(R.string.lottery)
					+ getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message5));
			break;
		case 5:
			alertDialog.setTitle(getResources().getString(R.string.lottery)
					+ getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message6));
			break;
		case 6:
			alertDialog.setTitle(getResources().getString(R.string.lottery)
					+ getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message7));
			break;
		}
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						switch (Item) {
						case 0:// 兑奖成功
							if (str_exchange_product_isgoods.equals("1")) {
								Intent i = new Intent(AdP.this,
										UpdateAddress.class);
								startActivity(i);
								UpdateAddress.setLogId(logid);
								UpdateAddress.setType(type);
							}
							break;
						case 3:// 抽奖成功
							if (str_exchange_product_isgoods.equals("1")) {
								Intent i = new Intent(AdP.this,
										UpdateAddress.class);
								startActivity(i);
								UpdateAddress.setLogId(logid);
								UpdateAddress.setType(type);
							}
							break;
						}
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	// 是否在Vector有值
	private boolean IsContainActives(ArrayList<Actives> vector, String un) {
		for (int i = 0; i < vector.size(); i++) {
			if (un.equals(vector.get(i).ActivesId)) {
				return true;
			}
		}
		return false;
	}

	// 是否在LIST有值
	private boolean IsGiftIn(ArrayList<Gift> list, String un) {
		for (int i = 0; i < list.size(); i++) {
			if (un.equals(list.get(i).giftId)) {
				return true;
			}
		}
		return false;
	}

	// 是否在LIST有值
	private boolean IsGiftInfo(ArrayList<GiftInfo> list, String un) {
		for (int i = 0; i < list.size(); i++) {
			if (un.equals(list.get(i).GifId)) {
				return true;
			}
		}
		return false;
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		// CustomDialog dialog=null;
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			Intent i ;
            i = new Intent(AdP.this, BrowseSuggestedAppListActivity.class);
            startActivity(i);
            finish();
//			showDialog(Back_DIALOG);
			// CustomDialog.Builder alertDialog = new
			// CustomDialog.Builder(gift,R.layout.alertdialog);
			// alertDialog.setTitle(getResources().getString(R.string.quit));
			// alertDialog.setMessage(getResources().getString(
			// R.string.quit_affirm));
			// alertDialog.setPositiveButton(
			// getResources().getString(R.string.ok),
			// new DialogInterface.OnClickListener() {
			// public void onClick(DialogInterface dialog, int which) {
			// votecount.setText(getResources().getString(
			// R.string.my_popularity));
			// voteranking.setText(getResources().getString(
			// R.string.popularity));
			// gift.finish();
			// System.exit(0);
			// AdP.this.finish();
			// }
			// });
			// alertDialog.setNegativeButton(
			// getResources().getString(R.string.cancel),
			// new DialogInterface.OnClickListener() {
			//
			// public void onClick(DialogInterface dialog, int which) {
			// dialog.dismiss();
			// return;
			// }
			// });
			// alertDialog.create(); // 创建对话框
			// alertDialog.show(); // 显示对话框
		}
		return true;
	}

	public void MainDo(Activity activity) {
		showDialog(CUSTOM_DIALOG);
	}

	// 分栏所需
	private void initHeaderTabs() {
		/**
		 * 标题按键声明
		 */
//		mFirstHeaderTab = findViewById(R.id.first_headertab);
//		mFirstPressedHeaderTab = findViewById(R.id.first_headertab_pressed);
//
//		mSecondHeaderTab = findViewById(R.id.second_headertab);
//		mSecondPressedHeaderTab = findViewById(R.id.second_headertab_pressed);
//
//		mThirdHeaderTab = findViewById(R.id.third_headertab);
//		mThirdPressedHeaderTab = findViewById(R.id.third_headertab_pressed);

		setSelectedHeaderTab(0, false);

		/**
		 * 标题按键中第一项，按键监听
		 */
		mFirstHeaderTab.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {

				GetActivesWeb();
				setSelectedHeaderTab(0, false);
			}

		});
		/**
		 * 标题按键中第二项，按键监听
		 */
		mSecondHeaderTab.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				if (activesId.equals("0")) {
					if (AdPlatform.mainState != 10) {
						// 切换Activity 回应打招呼界面
						DuiJiangDate();
						myScore.setText(getResources().getString(
								R.string.my_grade)
								+ Str_myScore);
						adapter = new ImageAndTextListAdapter(AdP.this,
								dataArray, listView);
						listView.setAdapter(adapter);
						setSelectedHeaderTab(1, false);
						return;
					} else {
						// 返回登录界面
						Log.e("请求登录", "-------------");
						MainDo(gift);
					}
				} else {
					if (activesId.equals("1")) {
						NoActiveAlert(gift);
					} else {
						// noActive
					}
				}
			}
		});
		/**
		 * 标题按键中第三项，按键监听
		 */
		mThirdHeaderTab.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				if (activesId != "10000") {
					if (AdPlatform.mainState != 10) {
						ChouJiang();
						gridviewAdapter = new GridViewImageAdapter(AdP.this,
								Chou_dataArray, gridview);
						gridview.setAdapter(gridviewAdapter);

						setSelectedHeaderTab(2, false);
						return;
					} else {
						// 返回登录界面
						Log.e("请求登录", "-------------");
						MainDo(gift);
					}
				} else {
					NoActiveAlert(gift);
				}
			}
		});
	}

	public int aaa;
	public int bbb;
	public int ccc = 0;

	protected void setSelectedHeaderTab(int i, boolean isruntwo) {
		boolean istwo = isruntwo;

		if (mCurHeaderTab == i && !istwo)
			return;

		mCurHeaderTab = i;
		/**
		 * 联网获取数据
		 */
		saveUserLog(0);
		/**
		 * 判断当前界面时标题按键中那一项
		 */
		mFirstHeaderTab.setVisibility(i == 0 ? View.GONE : View.VISIBLE);
		mFirstPressedHeaderTab.setVisibility(i == 0 ? View.VISIBLE : View.GONE);

		mSecondHeaderTab.setVisibility(i == 1 ? View.GONE : View.VISIBLE);
		mSecondPressedHeaderTab
				.setVisibility(i == 1 ? View.VISIBLE : View.GONE);

		mThirdHeaderTab.setVisibility(i == 2 ? View.GONE : View.VISIBLE);
		mThirdPressedHeaderTab.setVisibility(i == 2 ? View.VISIBLE : View.GONE);

		/**
		 * 各界面内容
		 */
		switch (i) {
		case 0:// 我要夺宝
			mTreasure.setVisibility(View.VISIBLE);
			mExchange.setVisibility(View.GONE);
			mLottery.setVisibility(View.GONE);
			break;

		case 1:// 积分换礼
			mTreasure.setVisibility(View.GONE);
			mExchange.setVisibility(View.VISIBLE);
			mLottery.setVisibility(View.GONE);
			break;

		case 2:// 抽奖
				// if (!istwo) {
			mTreasure.setVisibility(View.GONE);
			mExchange.setVisibility(View.GONE);
			mLottery.setVisibility(View.VISIBLE);
			// }

			// if (istwo) {
			// RotateAnimation ra1 = new RotateAnimation(360 * aaa + bbb * 30,
			// 60 + 360 * aaa + bbb * 30, Animation.RELATIVE_TO_SELF,
			// 0.5f, Animation.RELATIVE_TO_SELF, 0.5f);
			// // 设置动画持续时间
			// ra1.setDuration(5000);
			// // 让动画停止在结束位置
			// ra1.setFillAfter(true);
			// img.startAnimation(ra1);
			// }
			break;
		default:
			break;
		}

	}

	/**
	 * 联网操作
	 * 
	 * @param action
	 */
	private void saveUserLog(int action) {
		if (mCurHeaderTab == 0) {
			// GeneralUtil.saveUserLogType3(AppDetailActivity.this, 8, action);
		} else if (mCurHeaderTab == 1) {
			// GeneralUtil.saveUserLogType3(AppDetailActivity.this, 9, action);
		} else if (mCurHeaderTab == 2) {
			// GeneralUtil.saveUserLogType3(AppDetailActivity.this, 10, action);
		}
	}

	public static void setGiftItem(int Item) {
		giftItem = Item;
	}

	@Override
	public void run() {
		// TODO Auto-generated method stub
		while (true) {
			if (img != null) {
				if (img.getAnimation() != null) {
					if (img.getAnimation().hasEnded()) {
					}
				}
			}
			try {
				Thread.sleep(1000);
			} catch (Exception e) {
				Log.v("TAG-E-1", "" + e);
			}
		}
	}

	private void EmptyDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage("对不起，现在列表里没有礼品信息！");
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						// 切换Activity 主界面
						// Gift_vector.clear();
						// contactList.clear();
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	private void NoActiveAlert(Activity activity) {
		showDialog(noActive_DIALOG);
	}

	protected Dialog onCreateDialog(int id) {
		switch (id) {
		case DIALOG_WBE: {
			ProgressDialog dialog = new ProgressDialog(this);
			dialog.setMessage(getResources()
					.getString(R.string.alert_message10));
			dialog.setIndeterminate(true);
			dialog.setCancelable(true);
			return dialog;
		}
		}
		Dialog dialog = null;
		switch (id) {
		case CUSTOM_DIALOG:
			// CustomDialog customDialog=new CustomDialog(AdP.this);
			CustomDialog.Builder customBuilder = new CustomDialog.Builder(
					AdP.this, R.layout.alertdialog);
			// customBuilder.setContentView(v)
			customBuilder
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(getResources().getString(R.string.land_alert))
					.setNegativeButton(
							getResources().getString(R.string.cancel),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							})
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									Common.ChangeActivity(gift, AdP.this,
											Land.class);
									return;
								}
							});
			dialog = customBuilder.create();
			break;
		case Result_DIALOG:

			CustomDialog.Builder resultBuilder = new CustomDialog.Builder(
					AdP.this, R.layout.alertdialog);
			resultBuilder.setTitle(getResources().getString(R.string.alert));
			resultBuilder.setMessage(getResources().getString(
					R.string.land_alert));
			resultBuilder.setNegativeButton(
					getResources().getString(R.string.cancel),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							dialog.dismiss();
							return;
						}
					});
			resultBuilder.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							Common.ChangeActivity(gift, AdP.this, Land.class);
							return;
						}
					});
			dialog = resultBuilder.create();
			break;
		case Back_DIALOG:

			CustomDialog.Builder backBuilder = new CustomDialog.Builder(
					AdP.this, R.layout.alertdialog);
			backBuilder.setTitle(getResources().getString(R.string.quit));
			backBuilder.setMessage(getResources().getString(
					R.string.quit_affirm));
			backBuilder.setNegativeButton(
					getResources().getString(R.string.cancel),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							dialog.dismiss();
							return;
						}
					});
			backBuilder.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							votecount.setText(getResources().getString(
									R.string.my_popularity));
							voteranking.setText(getResources().getString(
									R.string.popularity));
							gift.finish();
							System.exit(0);
							AdP.this.finish();
						}
					});
			dialog = backBuilder.create();
			break;
		case noActive_DIALOG:
			CustomDialog.Builder wrongBuilder = new CustomDialog.Builder(
					AdP.this, R.layout.alertdialog);
			wrongBuilder
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("神秘夺宝活动筹划中，敬请期待")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = wrongBuilder.create();
			break;
		}

		return dialog;
	}

	private void WebFailureDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message11));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	// 兑奖、抽奖提示
	public void alertDialog(Activity activity, final int Item, int position) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		final int pos = position;
		switch (Item) {
		case 0:
			alertDialog.setTitle(getResources().getString(R.string.exchange)
					+ getResources().getString(R.string.alert));
			Log.v("Gift_vector.get(position).giftName",
					Gift_vector.get(position).giftName);
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message61)
					+ Gift_vector.get(position).giftName
					+ getResources().getString(R.string.alert_message62)
					+ Gift_vector.get(position).Score
					+ getResources().getString(R.string.alert_message63));
			break;
		case 1:
			alertDialog.setTitle(getResources().getString(R.string.lottery)
					+ getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message64)
					+ Str_lotteryScore
					+ getResources().getString(R.string.alert_message63));
			break;
		}
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						switch (Item) {
						case 0:
							GetExchangeTask(pos);
							break;
						case 1:
							GetLotteryTask();
							break;
						}
					}
				});
		alertDialog.setNegativeButton(
				getResources().getString(R.string.cancel),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	private void loadImage4(final String url, final int id) {
		// 如果缓存过就会从缓存中取出图像，ImageCallback接口中方法也不会被执行
		Drawable cacheImage = asyncImageLoader.loadDrawable(url,
				new AsyncImageLoader3.ImageCallback() {
					// 请参见实现：如果第一次加载url时下面方法会执行
					public void imageLoaded(Drawable imageDrawable) {
						((ImageView) findViewById(id))
								.setImageDrawable(imageDrawable);
					}
				});
		if (cacheImage != null) {
			((ImageView) findViewById(id)).setImageDrawable(cacheImage);
		}
	}

	// 按钮事件监听
	public class btnClickListener implements OnClickListener {

		@Override
		public void onClick(View v) {
			// TODO Auto-generated method stub
			switch (v.getId()) {
			case R.id.chou_jiang:
				alertDialog(gift, 1, 0);
				break;
			case R.id.invite:
				// 邀请好友事件监听
				
					// if (activesId.equals("0")) {
					// NoActiveAlert(gift);
					// }else{
					// // noActive
					// }
					if (AdPlatform.mainState != 10) {
						if (activesId.equals("10000")) {
							NoActiveAlert(gift);

						} else {
						Intent invite = new Intent(AdP.this, InviteFriend.class);
						Bundle bundle = new Bundle();
						bundle.putCharSequence("activesID", activesId);
						invite.putExtras(bundle);
						startActivity(invite);
						AdP.this.finish();
						}
					} else {
						// 返回登录界面
						Log.e("请求登录", "-------------");
						MainDo(gift);
					}
//				}
				break;
			case R.id.support:
				// 支持好友事件监听
				
					if (AdPlatform.mainState != 10) {
						if (activesId.equals("10000")) {
							NoActiveAlert(gift);
						} else {
						Intent support = new Intent(AdP.this,
								SupportFriend.class);
						Bundle bundle = new Bundle();
						bundle.putCharSequence("activesID", activesId);
						support.putExtras(bundle);
						startActivity(support);
						AdP.this.finish();
						}
					} else {
						// 返回登录界面
						Log.e("请求登录", "-------------");
						MainDo(gift);
					}
//				}
				break;
			}
		}
	}

}
