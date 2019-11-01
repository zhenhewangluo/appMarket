package com.market.hjapp.tyxl;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONObject;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ListView;


import com.market.hjapp.R;
import com.market.hjapp.tyxl.adapter.SupportFriendAdapter;
import com.market.hjapp.tyxl.object.CustomDialog;
import com.market.hjapp.tyxl.object.Friend;
import com.market.hjapp.tyxl.object.HttpUrl;
import com.market.hjapp.tyxl.object.MD5;
public class SupportFriend extends Activity {

	ListView listview;
	SupportFriendAdapter supportfriendadapter;
	// List<Map<String, Object>> list;
	MD5 md5;
	public static SupportFriend friends;
	// 活动Id
	public static String ActivesId;
	// 好友数据
	public static ArrayList<Friend> frieds_vector;
	private static final int DIALOG_WBE = 1;
	private static final int VoteSuccessDialog = 2;
	private static final int VoteFailDialog = 3;
	private static final int VoteOverDialog = 4;
	private static final int EmptyDialog = 5;
	public static ArrayList<Friend> vote_vector = new ArrayList<Friend>();
	protected static String numberStr = "";
	int position;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.support_friend);
		friends = this;
		Bundle bundle = this.getIntent().getExtras();
		ActivesId = bundle.get("activesID").toString();

		listview = (ListView) findViewById(android.R.id.list);
		// list = new ArrayList<Map<String, Object>>();
		frieds_vector = new ArrayList<Friend>();
		md5 = new MD5();
		// GetFriendsWeb();
		new GetVoteFriendsTask().execute("");
		listview.setAdapter(supportfriendadapter);
		listview.setOnItemClickListener(new listItemClickListener());
	}

	// 支持好友列表每一项的监听器
	class listItemClickListener implements OnItemClickListener {

		@Override
		public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
				long arg3) {
			// TODO Auto-generated method stub
			position = arg2;
			new GetVoteTask().execute("");
		}

	}

	/**
	 * 投票列表
	 * 
	 * @author Administrator
	 * 
	 */
	private class GetVoteFriendsTask extends AsyncTask<String, String, String> {
		public String doInBackground(String... params) {

			try {
				String votefriends_url = HttpUrl.httpStr
						+ "vote/voteusers.action?activeid=" + ActivesId
						+ "&userid=" + AdPlatform.userId + "&password="
						+ md5.getMD5ofStr(AdPlatform.password);
				String votefriends_out = HttpUrl.setGetUrl(votefriends_url);

				JSONObject votefriends_jsonObject = new JSONObject(
						votefriends_out);
				JSONArray votefriends_jsonArray = votefriends_jsonObject
						.getJSONArray("users");
				for (int i = 0; i < votefriends_jsonArray.length(); i++) {
					JSONObject votefriends_jsonObject_0 = (JSONObject) votefriends_jsonArray
							.opt(i);
					Friend friend = new Friend();
					friend.friedsId = votefriends_jsonObject_0.getString("id");
					friend.friedsName = votefriends_jsonObject_0
							.getString("nickname");
					friend.isSupport = Integer
							.parseInt(votefriends_jsonObject_0
									.getString("isclientuser"));
					if (friend.friedsName.equals("null")
							|| friend.friedsName.equals("\"null\"")) {
						friend.friedsName = "";
					}
					friend.friedsNumber = Common
							.deMaskPhone(votefriends_jsonObject_0
									.getString("phone"));
					// friend.isShield = false;
					// friend.isNewFriends = false;

					if (Common.IsUserNumber(friend.friedsNumber)
							&& !Common.IsContainFriends(vote_vector,
									friend.friedsNumber)) {
						vote_vector.add(friend);
					}
				}
			} catch (Exception e) {
				Common.WebFailureDialog(friends);
				Log.v("E-GetVoteFriendsTask", "" + e);
			}

			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (vote_vector.size() == 0) {
					EmptyDialog(friends, 0);
				} else {
					// 填充列表
					// for (int i = 0; i < vote_vector.size(); i++) {
					// Map<String, Object> map = new HashMap<String, Object>();
					// map.put("name", vote_vector.get(i).friedsName);
					// map.put("number", vote_vector.get(i).friedsNumber);
					// list.add(map);
					//
					// }

					supportfriendadapter = new SupportFriendAdapter(friends,
							vote_vector);
					listview.setAdapter(supportfriendadapter);
					listview.setTextFilterEnabled(true);
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 投票
	 * 
	 * @author user
	 * 
	 */
	private class GetVoteTask extends AsyncTask<String, String, String> {

		String str_vote_result;
		String str_vote_successcount;

		public String doInBackground(String... params) {
			try {
				String vote_url = HttpUrl.httpStr + "vote/vote.action";
				String vote_in_json = "{\"userid\":" + AdPlatform.userId
						+ ",\"password\":\""
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "\",\"actives\":[{\"activeid\":" + ActivesId
						+ "}],\"users\":[{\"userid\":"
						+ vote_vector.get(position).friedsId + "}]}";
				String md51 = md5.getMD5ofStr(vote_in_json + ",");
				String vote_in = vote_in_json.substring(0,
						vote_in_json.length() - 1)
						+ ",\"md\":\"" + md51 + "\"}";


				String vote_out = HttpUrl.setPostUrl(vote_url, vote_in);

				JSONObject vote_jsonObject = new JSONObject(vote_out);
				str_vote_result = vote_jsonObject.getString("result");
				str_vote_successcount = vote_jsonObject
						.getString("successcount");

			} catch (Exception e) {
				Common.WebFailureDialog(friends);
				Log.v("E-GetVoteTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			// showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				if (str_vote_result.equals("1")) {
					// 投票
					VoteDo(friends, 0);
				} else {
					if (str_vote_result.equals("2")) {
						VoteDo(friends, 3);
					} else {
						VoteDo(friends, 4);
					}
				}
				// removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}

	}

	private void VoteDo(Activity activity, final int Item) {
		// AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		switch (Item) {
		case 0:// 屏蔽拉票
			showDialog(VoteSuccessDialog);
			// alertDialog.setTitle(getResources()
			// .getString(R.string.alert_title3));
			// alertDialog.setMessage(getResources().getString(
			// R.string.alert_message37));
			break;
		case 3:
			showDialog(VoteOverDialog);
			// alertDialog.setTitle(getResources()
			// .getString(R.string.alert_title4));
			// alertDialog.setMessage(getResources().getString(
			// R.string.alert_message65));
			break;
		case 4:
			showDialog(VoteFailDialog);
			// alertDialog.setTitle(getResources()
			// .getString(R.string.alert_title4));
			// alertDialog.setMessage("拉票操作失败！");
			break;
		}
		// alertDialog.setPositiveButton(getResources().getString(R.string.ok),
		// new DialogInterface.OnClickListener() {
		// public void onClick(DialogInterface dialog, int which) {
		// vote_vector.clear();
		// list.clear();
		// new GetVoteFriendsTask().execute("");
		// }
		// }).create(); // 创建对话框selectNumber
		// alertDialog.show(); // 显示对话框
	}

	protected Dialog onCreateDialog(int id) {
		Dialog dialog = null;
		switch (id) {
		// case DIALOG_KEY: {
		// ProgressDialog dialog = new ProgressDialog(this);
		// dialog.setMessage("获取通讯录中...请稍候");
		// dialog.setIndeterminate(true);
		// dialog.setCancelable(true);
		// return dialog;
		// }
		case DIALOG_WBE: {
			ProgressDialog dialog1 = new ProgressDialog(this);
			dialog1.setMessage(getResources().getString(
					R.string.alert_message10));
			dialog1.setIndeterminate(true);
			dialog1.setCancelable(true);
			return dialog1;
		}
		case VoteSuccessDialog:
			CustomDialog.Builder voteSuccessBuilder = new CustomDialog.Builder(
					SupportFriend.this, R.layout.alertdialog);
			voteSuccessBuilder
					.setTitle(getResources().getString(R.string.alert_title3))
					.setMessage(
							getResources().getString(R.string.alert_message37))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									vote_vector.clear();
									// list.clear();
									new GetVoteFriendsTask().execute("");
									dialog.dismiss();
									return;
								}
							});
			dialog = voteSuccessBuilder.create();
			break;
		case VoteFailDialog:
			CustomDialog.Builder voteFailBuilder = new CustomDialog.Builder(
					SupportFriend.this, R.layout.alertdialog);
			voteFailBuilder
					.setTitle(getResources().getString(R.string.alert_title3))
					.setMessage(
							getResources().getString(R.string.alert_message38))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									vote_vector.clear();
									// list.clear();
									new GetVoteFriendsTask().execute("");
									dialog.dismiss();
									return;
								}
							});
			dialog = voteFailBuilder.create();
			break;
		case VoteOverDialog:
			CustomDialog.Builder voteOverBuilder = new CustomDialog.Builder(
					SupportFriend.this, R.layout.alertdialog);
			voteOverBuilder
					.setTitle(getResources().getString(R.string.alert_title3))
					.setMessage(
							getResources().getString(R.string.alert_message65))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									vote_vector.clear();
									// list.clear();
									new GetVoteFriendsTask().execute("");
									dialog.dismiss();
									return;
								}
							});
			dialog = voteOverBuilder.create();
			break;
		case EmptyDialog:
			CustomDialog.Builder emptyBuilder = new CustomDialog.Builder(
					SupportFriend.this, R.layout.alertdialog);
			emptyBuilder
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message42))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									// SupportFriend.this.finish();
									Common.ChangeActivity(friends,
											SupportFriend.this, AdP.class);
									SupportFriend.this.finish();
									dialog.dismiss();
									return;
								}
							});
			dialog = emptyBuilder.create();
			break;
		}
		return dialog;
	}

	/**
	 * 按键按下
	 * 
	 * @return null
	 */
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			// Common.Finish(Land);
			Common.ChangeActivity(friends, SupportFriend.this, AdP.class);
			this.finish();
		}
		return true;
	}

	private void EmptyDialog(Activity activity, final int Item) {
		// AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		// alertDialog.setTitle(getResources().getString(R.string.alert));
		switch (Item) {
		case 0:
			showDialog(EmptyDialog);
			// alertDialog.setMessage(getResources().getString(
			// R.string.alert_message42));
			break;
		// case 1:
		// alertDialog.setMessage(getResources().getString(
		// R.string.alert_message43));
		// break;
		}
		// alertDialog.setPositiveButton(getResources().getString(R.string.ok),
		// new DialogInterface.OnClickListener() {
		// public void onClick(DialogInterface dialog, int which) {
		// switch (Item) {
		// case 0:
		// SupportFriend.this.finish();
		// case 1:
		//
		// }
		// }
		// });
		// alertDialog.create(); // 创建对话框
		// alertDialog.show(); // 显示对话框
	}
}
