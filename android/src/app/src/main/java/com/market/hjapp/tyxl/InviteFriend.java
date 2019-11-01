package com.market.hjapp.tyxl;

import java.util.ArrayList;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONObject;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.provider.Contacts.People;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnKeyListener;
import android.view.inputmethod.InputMethodManager;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.adapter.InviteFriendAdapter;
import com.market.hjapp.tyxl.adapter.InviteFriendAdapter.ListItem;
import com.market.hjapp.tyxl.object.CustomDialog;
import com.market.hjapp.tyxl.object.Friend;
import com.market.hjapp.tyxl.object.HttpUrl;
import com.market.hjapp.tyxl.object.MD5;

public class InviteFriend extends Activity {

	CheckBox checkbox;
	Button btn_add, invite, delete;// , btn_search;
	ListView listview;
	InviteFriendAdapter invitefriendadapter;
	// List<Map<String, Object>> list;
	EditText edt_search;
	Map<Integer, Boolean> isSelected;

	public static ArrayList<Friend> vote_vector = new ArrayList<Friend>();
	// int add_flag = 0;// 区别添加好友的方式 0：上传电话本 1：手动添加
	// 选中的手机号
	protected String numberStr = "";
	protected String name = "";
	protected String addfriendid = "";
	public static InviteFriend frieds;
	// 活动Id
	public static String ActivesId;
	MD5 md5;
	// 头像
	public Bitmap headImg;
	// 是否屏蔽好友拉票
	public Bitmap[] isChecked;
	// 好友的好友列表入口
	public Bitmap ConBitmap;
	// 好友数据
	public static ArrayList<Friend> frieds_vector;
	// 电话簿数据
	public static ArrayList<Friend> book_vector = new ArrayList<Friend>();
	protected Cursor mCursor = null;
	private static final int AddFriend_DIALOG = 0;
	private static final int DIALOG_WBE = 1;
	private static final int CUSTOM_DIALOG = 2;
	private static final int AddResult_DIALOG = 3;
	private static final int IsFriend_DIALOG = 4;
	private static final int NumberWrong_DIALOG = 5;
	private static final int NoneFriend_DIALOG = 6;
	private static final int VoteSuccess_DIALOG = 7;
	private static final int VoteFail_DIALOG = 8;
	private static final int Empty_DIALOG = 9;

	int num = 0, del_num = 0;
	int[] position;
	int AddResult = -1;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
//		requestWindowFeature(Window.FEATURE_NO_TITLE);
//		setContentView(R.layout.invite_friend);
//		Bundle bundle = this.getIntent().getExtras();
//		ActivesId = bundle.get("activesID").toString();
//		frieds = this;
//		md5 = new MD5();
//		listview = (ListView) findViewById(android.R.id.list);
//		checkbox = (CheckBox) findViewById(R.id.checkBox1);
//		btn_add = (Button) findViewById(R.id.button1);
//		invite = (Button) findViewById(R.id.invite_friend);
//		delete = (Button) findViewById(R.id.button2);
		// btn_search=(Button)findViewById(R.id.btn_search);
		// edt_search = (EditText) findViewById(R.id.search);
		// edt_search.addTextChangedListener(new TextWatcher() {
		// public void afterTextChanged(Editable arg0) {
		// Log.d("TAG", "afterTextChanged--------------->");
		// }
		//
		// public void beforeTextChanged(CharSequence arg0, int arg1,
		// int arg2, int arg3) {
		// Log.d("TAG", "beforeTextChanged--------------->");
		// }
		//
		// public void onTextChanged(CharSequence chars, int start,
		// int before, int count) {
		// // list.clear();
		// // frieds_vector.clear();
		// // invitefriendadapter.notifyDataSetChanged();
		//
		// }
		// });

//		btn_add.setOnClickListener(new btnClickListener());
//		invite.setOnClickListener(new btnClickListener());
//		delete.setOnClickListener(new btnClickListener());
//		// btn_search.setOnClickListener(new btnClickListener());
//		// list = new ArrayList<Map<String, Object>>();
//		frieds_vector = new ArrayList<Friend>();
//		new GetContactTask().execute("");
//		GetFriendsWeb();
//		// 列表点击事件监听
//		listview.setOnItemClickListener(new OnItemClickListener() {
//			public void onItemClick(AdapterView<?> parent, View view,
//					int position, long id) {
//				// 从子级中获得控件
//				ListItem item = (ListItem) view.getTag(); // 在每次获取点击的item时将对于的checkbox状态改变，同时修改map的值。
//				item.check.toggle();
//				frieds_vector.get(position).isChecked = item.check.isChecked();
//				// list.get(position).put("isChecked", item.check.isChecked());
//				Log.v("item.Id", item.Id);
//				Log.v("position", "" + position);
//			}
//		});
//		checkbox.setOnCheckedChangeListener(new OnCheckedChangeListener() {
//
//			@Override
//			public void onCheckedChanged(CompoundButton buttonView,
//					boolean isChecked) {
//				// TODO Auto-generated method stub
//				if (isChecked) {
//					for (int i = 0; i < frieds_vector.size(); i++) {
//						// list.get(i).put("isChecked", true);
//						frieds_vector.get(i).isChecked = true;
//					}
//					invitefriendadapter.notifyDataSetChanged();
//				} else {
//					for (int i = 0; i < frieds_vector.size(); i++) {
//						// invitefriendadapter.
//						// list.get(i).put("isChecked", false);
//						frieds_vector.get(i).isChecked = false;
//					}
//					invitefriendadapter.notifyDataSetChanged();
//				}
//			}
//		});

	}

	private void invite_friend() {
		int j = 0;
		for (int i = 0; i < frieds_vector.size(); i++) {
			if ((Boolean) frieds_vector.get(i).isChecked == true) {
				j++;
			}
		}
		num = j;
	}

	private void delete_friend() {
		int j = 0;
		for (int i = 0; i < frieds_vector.size(); i++) {
			if ((Boolean) frieds_vector.get(i).isChecked == true) {
				j++;
			}
		}
		del_num = j;
		Log.v("j11111", "" + j);
	}

	public class btnClickListener implements OnClickListener {

		@Override
		public void onClick(View v) {/*
			// TODO Auto-generated method stub
			switch (v.getId()) {
			// "+"按钮的事件监听
			case R.id.button1:
				// add_flag = 1;
				removeDialog(AddFriend_DIALOG);
				addItem();
				// add_flag = 0;
				break;
			case R.id.button2:// 删除好友按钮
				// DeleteAlert();
				removeDialog(CUSTOM_DIALOG);
				delete_friend();
				DeleteAlert();

				break;
			case R.id.invite_friend:
				invite_friend();
				if (num == 0) {
					showDialog(NoneFriend_DIALOG);

				} else {
					new GetAdviseVoteTask().execute();
				}
				break;
			}
		*/}
	}

	/**
	 * 获取服务端好友数据
	 */
	protected void GetFriendsWeb() {

		try {
			// 用户id、密码、好友id（如与用户id相同则获取本用户好友列表、如不同则获取friendid好友的好友列表）
			String friends_url = HttpUrl.httpStr
					+ "relation/friends.action?userid=" + AdPlatform.userId
					+ "&password=" + md5.getMD5ofStr(AdPlatform.password)
					+ "&friendid=" + AdPlatform.userId;
			Log.v("friends_url_MainActivity", "" + friends_url);
			String friends_out = HttpUrl.setGetUrl(friends_url);
			Log.v("friends_out", "" + friends_out);

			JSONObject friends_jsonObject = new JSONObject(friends_out);
			JSONArray friends_jsonArray = friends_jsonObject
					.getJSONArray("relations");

			for (int i = 0; i < friends_jsonArray.length(); i++) {
				JSONObject friends_jsonObject_0 = (JSONObject) friends_jsonArray
						.opt(i);
				Friend friend = new Friend();

				friend.friedsId = friends_jsonObject_0.getString("userid");

				friend.friedsName = friends_jsonObject_0.getString("name");
				if (friend.friedsName.equals("null")
						|| friend.friedsName.equals("\"null\"")) {
					friend.friedsName = "";
				}
				friend.friedsNumber = Common.deMaskPhone(friends_jsonObject_0
						.getString("phone"));
				if (Common.IsUserNumber(friend.friedsNumber)
						&& !Common.IsContainFriends(frieds_vector,
								friend.friedsNumber)) {
					// Map<String, Object> map = new HashMap<String, Object>();
					// map.put("Id", friend.friedsId);
					// map.put("name", friend.friedsName);
					// map.put("number", friend.friedsNumber);
					// map.put("isChecked", false);
					// list.add(map);
					frieds_vector.add(friend);
					// stringlist.add(friend.friedsName)
				}
			}
			invitefriendadapter = new InviteFriendAdapter(this, frieds_vector);
			listview.setAdapter(invitefriendadapter);
			listview.setFastScrollEnabled(true);
			listview.setTextFilterEnabled(true);

			// Log.v("list.size()", ""+list.size());
			// if (list.size() == 0) {
			// EmptyDialog(frieds);
			// }
		} catch (Exception e) {
			Common.WebFailureDialog(frieds);
			Log.v("E-GetFriendsWeb", "" + e);
		}

	}

	/**
	 * 搜索好友
	 */
	protected void SearchFriendsWeb() {

	}

	// 添加好友
	private void addItem() {
		showDialog(AddFriend_DIALOG);
		// LayoutInflater factory = LayoutInflater.from(InviteFriend.this);
		// final View AddAlert = factory.inflate(R.layout.add_alert, null);
		// AlertDialog.Builder alert = new
		// AlertDialog.Builder(InviteFriend.this)
		// .setTitle(getResources().getString(R.string.alert_title1))
		// .setView(AddAlert);
		// alert.setMessage(getResources().getString(R.string.alert_message34));
		// alert.setPositiveButton(getResources().getString(R.string.ok),
		// new DialogInterface.OnClickListener() {
		// public void onClick(DialogInterface dialog, int which) {
		// EditText add_name = (EditText) AddAlert
		// .findViewById(R.id.add_name);
		// Appendable valuename = add_name.getText();
		// name = String.valueOf(valuename);
		// EditText input = (EditText) AddAlert
		// .findViewById(R.id.add_phone);
		// Appendable value = input.getText();
		// Log.v("value", "" + value);
		// numberStr = String.valueOf(value);
		//
		// if (!Common.IsUserNumber(numberStr)) {
		// showDialog(NumberWrong_DIALOG);
		//
		// } else if (Common.IsContain(list, numberStr)) {
		// showDialog(IsFriend_DIALOG);
		// } else {
		// new GetAddFriendsTask().execute("");
		// }
		// }
		// });
		// alert.setNegativeButton(getResources().getString(R.string.cancel),
		// new DialogInterface.OnClickListener() {
		// public void onClick(DialogInterface dialog, int which) {
		// return;
		// }
		// });
		// alert.show();

	}

	private void DeleteAlert() {
		showDialog(CUSTOM_DIALOG);
	}

	/**
	 * 拉票
	 * 
	 * @author user
	 * 
	 */
	private class GetAdviseVoteTask extends AsyncTask<String, String, String> {
		//
		String str_advisevote_result;
		String str_advisevote_successcount;

		public String doInBackground(String... params) {
			try {

				String advisevote_url = HttpUrl.httpStr
						+ "vote/advisevote.action";
				Log.v("advisevote_url", advisevote_url);

				String advisevote_in = "{\"userid\":" + AdPlatform.userId
						+ ",\"password\":\""
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "\",\"actives\":[{\"activeid\":" + ActivesId
						+ "}],\"users\":[";
				for (int i = 0; i < frieds_vector.size(); i++) {
					// Log.v("num", "" + num);
					if ((Boolean) frieds_vector.get(i).isChecked == true)
						advisevote_in += "{\"userid\":"
								+ frieds_vector.get(i).friedsId + "},";
					// invite_selectId[i] + "},";
				}
				advisevote_in += "]}";
				// Log.v(" invite_selectId.length", "" + list.get(i).get("Id"));
				Log.v("advisevote_in", advisevote_in);

				String advisevote_out = HttpUrl.setPostUrl(advisevote_url,
						advisevote_in);
				Log.v("advisevote_out", "" + advisevote_out);

				JSONObject advisevote_jsonObject = new JSONObject(
						advisevote_out);
				str_advisevote_result = advisevote_jsonObject
						.getString("result");
				str_advisevote_successcount = advisevote_jsonObject
						.getString("successcount");

				Log.v("str_advisevote_result", "" + str_advisevote_result);
				Log.v("str_advisevote_successcount", ""
						+ str_advisevote_successcount);
			} catch (Exception e) {
				Common.WebFailureDialog(frieds);
				Log.v("E-GetAdviseVoteTask", "" + e);
			}
			return null;
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			try {
				removeDialog(DIALOG_WBE);
				if (str_advisevote_result.equals("1")) {
					VoteDo(frieds, 2);
				} else {
					VoteDo(frieds, 3);
				}
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	private void VoteDo(Activity activity, final int Item) {
		// AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		switch (Item) {
		// case 0:// 屏蔽拉票
		// alertDialog.setTitle(getResources()
		// .getString(R.string.alert_title3));
		// alertDialog.setMessage(getResources().getString(
		// R.string.alert_message37));
		// break;
		// case 1:
		// alertDialog.setTitle(getResources()
		// .getString(R.string.alert_title3));
		// alertDialog.setMessage(getResources().getString(
		// R.string.alert_message38));
		// break;
		case 2:
			showDialog(VoteSuccess_DIALOG);
			// alertDialog.setTitle(getResources()
			// .getString(R.string.alert_title4));
			// alertDialog.setMessage(getResources().getString(
			// R.string.alert_message39));
			break;
		case 3:
			showDialog(VoteFail_DIALOG);
			// alertDialog.setTitle(getResources()
			// .getString(R.string.alert_title4));
			// alertDialog.setMessage(getResources().getString(
			// R.string.alert_message40));
			break;
		}
		// alertDialog.setPositiveButton(getResources().getString(R.string.ok),
		// new DialogInterface.OnClickListener() {
		// public void onClick(DialogInterface dialog, int which) {
		//
		// }
		// }).create(); // 创建对话框
		// alertDialog.show(); // 显示对话框
	}

	/**
	 * 添加好友 同步通讯录 联网
	 * 
	 * @author user
	 * 
	 */
	private class GetSyntelbookTask extends AsyncTask<String, String, String> {

		// 可变长的输入参数，与AsyncTask.exucute()对应
		public String doInBackground(String... params) {

			try {
				String syntelbook_url = HttpUrl.httpStr
						+ "user/syntelbook.action";
				String syntelbook_in;

				syntelbook_in = "{\"userid\":" + AdPlatform.userId
						+ ",\"password\":\""
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "\",\"users\":[";
				for (int i = 0; i < book_vector.size(); i++) {
					if (i < book_vector.size() - 1) {
						syntelbook_in += "{\"phone\":"
								+ Common.maskPhone(book_vector.get(i).friedsNumber)
								+ ",\"toname\":\""
								+ book_vector.get(i).friedsName + "\"},";
					} else {
						syntelbook_in += "{\"phone\":"
								+ Common.maskPhone(book_vector.get(i).friedsNumber)
								+ ",\"toname\":\""
								+ book_vector.get(i).friedsName + "\"}";
					}
				}
				syntelbook_in += "]}";

				Log.v("syntelbook_in", syntelbook_in);

				String syntelbook_out = HttpUrl.setPostUrl(syntelbook_url,
						syntelbook_in);
				Log.v("syntelbook_out", syntelbook_out);

				JSONObject syntelbook_userid_jsonObject = new JSONObject(
						syntelbook_out);
				String str_syntelbook_userid = syntelbook_userid_jsonObject
						.getString("userid");
				Log.v("str_syntelbook_userid", str_syntelbook_userid);

				JSONObject syntelbook_users_jsonObject = new JSONObject(
						syntelbook_out);
				JSONArray syntelbook_users_jsonArray = syntelbook_users_jsonObject
						.getJSONArray("users");
				// Log.v("syntelbook_users_jsonArray.size()",
				// syntelbook_users_jsonArray);

				for (int i = 0; i < syntelbook_users_jsonArray.length(); i++) {
					JSONObject syntelbook_users_jsonObject_0 = (JSONObject) syntelbook_users_jsonArray
							.opt(i);
					Friend friend = new Friend();
					friend.friedsId = syntelbook_users_jsonObject_0
							.getString("id");
					friend.friedsName = "";
					// friend.friedsNumber = Common
					// .deMaskPhone(syntelbook_users_jsonObject_0
					// .getString("phone"));
					friend.friedsNumber = syntelbook_users_jsonObject_0
							.getString("phone");
					// friend.isShield = false;
					// friend.isNewFriends = true;

					if (Common.IsUserNumber(friend.friedsNumber)
							&& !Common.IsContainFriends(frieds_vector,
									friend.friedsNumber)) {
						frieds_vector.add(friend);
					}
				}

			} catch (Exception e) {
				Common.WebFailureDialog(frieds);
				Log.v("E-GetSyntelbookTask", "" + e);
			}

			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			// if (add_flag == 0) {
			// list.clear();
			try {
				removeDialog(DIALOG_WBE);
				frieds_vector.clear();
				GetFriendsWeb();
				invitefriendadapter.notifyDataSetChanged();
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 添加好友
	 * 
	 * @author user
	 * 
	 */
	private class GetAddFriendsTask extends AsyncTask<String, String, String> {

		public String doInBackground(String... params) {
			String str_addfriend_result, str_addfriend_successcount;
			try {
				// userid用户id、password密码、touid所选的好友id、toname所选得好友昵称
				String addfriend_in = "{\"userid\":" + AdPlatform.userId
						+ ",\"password\":\""
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "\",\"users\":[";
				addfriend_in += "{\"phone\":" + Common.maskPhone(numberStr)
						+ ",\"toname\":\"" + name + "\"}";
				addfriend_in += "]}";
				Log.v("addfriend_in", addfriend_in);
				String addfriend_url = HttpUrl.httpStr
						+ "relation/addfriend.action";
				Log.v("addfriend_url", addfriend_url);
				String addfriend_out = HttpUrl.setPostUrl(addfriend_url,
						addfriend_in);
				Log.v("addfriend_out", "" + addfriend_out);

				JSONObject addfriend_jsonObject = new JSONObject(addfriend_out);
				// 添加结果 1成功、0失败
				str_addfriend_result = addfriend_jsonObject
						.getString("addresult");
				AddResult = Integer.parseInt(addfriend_jsonObject
						.getString("addresult"));
				// 成功添加的数量
				str_addfriend_successcount = addfriend_jsonObject
						.getString("successcount");
				addfriendid = addfriend_jsonObject.getString("friendid");

				Log.v("str_addfriend_result", "" + str_addfriend_result);
				Log.v("str_addfriend_successcount", ""
						+ str_addfriend_successcount);
			} catch (Exception e) {
				Common.WebFailureDialog(frieds);
				Log.v("E-GetAddFriendsTask", "" + e);
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
				removeDialog(AddResult_DIALOG);
				AddDoMask(frieds);
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}

	}

	protected Dialog onCreateDialog(int id) {
		Dialog dialog = null;
		switch (id) {
		case DIALOG_WBE: {
			ProgressDialog dialog1 = new ProgressDialog(this);
			dialog1.setMessage(getResources().getString(
					R.string.alert_message10));
			dialog1.setIndeterminate(true);
			dialog1.setCancelable(true);
			return dialog1;
		}
		case CUSTOM_DIALOG:

			CustomDialog.Builder customBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			LayoutInflater factory = LayoutInflater.from(InviteFriend.this);
			final View delertAlert = factory.inflate(R.layout.delete_alert,
					null);
			customBuilder.setTitle("删除好友");

			TextView delete_alert = (TextView) delertAlert
					.findViewById(R.id.delete_alert);
			if (del_num == 0) {
				delete_alert.setText("请选中要删除的好友");
			} else {
				delete_alert.setText("确定要删除这" + del_num + "位好友吗？");
			}
			customBuilder.setContentView(delertAlert);// (friends);
			TextView tv = (TextView) delertAlert.findViewById(R.id.tv);
			String friends = "";
			for (int j = 0; j < frieds_vector.size(); j++) {
				if ((Boolean) frieds_vector.get(j).isChecked == true) {
					friends += getResources().getString(R.string.name)
							+ frieds_vector.get(j).friedsName + "\n"
							+ getResources().getString(R.string.number)
							+ frieds_vector.get(j).friedsNumber + "\n\n";
				}
			}
			tv.setText(friends);
			customBuilder

			.setNegativeButton(getResources().getString(R.string.cancel),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							dialog.dismiss();
							return;
						}
					}).setPositiveButton(getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							if (del_num != 0) {
								new GetRemoveTask().execute();

							}
							dialog.dismiss();
							return;
						}
					});
			dialog = customBuilder.create();
			break;
		case NumberWrong_DIALOG:
			CustomDialog.Builder wrongBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			wrongBuilder
					.setTitle(getResources().getString(R.string.alert_title2))
					.setMessage(
							getResources().getString(R.string.alert_message35))
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
		case IsFriend_DIALOG:
			CustomDialog.Builder IsFriendBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			IsFriendBuilder
					.setTitle(getResources().getString(R.string.alert_title1))
					.setMessage(
							getResources().getString(R.string.alert_message36))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = IsFriendBuilder.create();
			break;
		case NoneFriend_DIALOG:
			CustomDialog.Builder noFriendBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			noFriendBuilder
					.setTitle("邀请好友")
					.setMessage("请选择至少一个好友进行邀请")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = noFriendBuilder.create();
			break;
		case VoteSuccess_DIALOG:
			CustomDialog.Builder voteSuccessBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			voteSuccessBuilder
					.setTitle(getResources().getString(R.string.alert_title4))
					.setMessage(
							getResources().getString(R.string.alert_message39))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = voteSuccessBuilder.create();
			break;
		case VoteFail_DIALOG:
			CustomDialog.Builder voteFailBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			voteFailBuilder
					.setTitle(getResources().getString(R.string.alert_title4))
					.setMessage(
							getResources().getString(R.string.alert_message40))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = voteFailBuilder.create();
			break;
		case AddResult_DIALOG:
			CustomDialog.Builder addResultBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			addResultBuilder.setTitle(getResources().getString(
					R.string.alert_title5));
			switch (AddResult) {
			case 0:
				addResultBuilder.setMessage("添加好友操作失败！");
				break;
			case 1:
				addResultBuilder.setMessage(getResources().getString(
						R.string.alert_message41));
				break;
			case 2:
				addResultBuilder.setMessage("您不能添加自己为好友！");
				break;
			}
			addResultBuilder.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							// list.clear();
							frieds_vector.clear();
							GetFriendsWeb();
							invitefriendadapter.notifyDataSetChanged();
							dialog.dismiss();
							return;
						}
					});
			dialog = addResultBuilder.create();
			break;
		case Empty_DIALOG:
			CustomDialog.Builder emptyBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			emptyBuilder
					.setTitle("提示")
					.setMessage("您的好友列表现在为空,快去添加朋友吧！")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = emptyBuilder.create();
			break;
		case AddFriend_DIALOG:
			CustomDialog.Builder addFriendBuilder = new CustomDialog.Builder(
					InviteFriend.this, R.layout.alertdialog);
			LayoutInflater factory1 = LayoutInflater.from(InviteFriend.this);
			final View AddAlert = factory1.inflate(R.layout.add_alert, null);
			addFriendBuilder
					.setTitle(getResources().getString(R.string.alert_title1))
					.setMessage(
							getResources().getString(R.string.alert_message36))
					.setContentView(AddAlert)
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									EditText add_name = (EditText) AddAlert
											.findViewById(R.id.add_name);
									Appendable valuename = add_name.getText();
									name = String.valueOf(valuename);
									EditText input = (EditText) AddAlert
											.findViewById(R.id.add_phone);
									input.setOnKeyListener(onKey);
									Appendable value = input.getText();
									Log.v("value", "" + value);
									numberStr = String.valueOf(value);
									if (!Common.IsUserNumber(numberStr)) {
										showDialog(NumberWrong_DIALOG);

									} else if (Common.IsContainFriends(
											frieds_vector, numberStr)) {
										showDialog(IsFriend_DIALOG);
									} else {
										new GetAddFriendsTask().execute("");
									}
									dialog.dismiss();
									return;
								}
							})
					.setNegativeButton(
							getResources().getString(R.string.cancel),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = addFriendBuilder.create();
			break;
		}
		return dialog;

	}

	// 获取通讯录进程
	private class GetContactTask extends AsyncTask<String, String, String> {
		public String doInBackground(String... params) {
			/*
			 * try{ Thread.sleep(4000); } catch(Exception e){}
			 */
			Log.v("", "通讯录");
			// 从本地手机中获取
			GetLocalContact();
			// 从SIM卡中获取
			GetSimContact("content://icc/adn");
			// 发现有得手机的SIM卡联系人在这个路径...所以都取了(每次验证是否已存在)
			GetSimContact("content://sim/adn");
			Log.v("", "通讯录");
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_WBE);
		}

		@Override
		public void onPostExecute(String Re) {
			// 同步通讯录
			try {
				removeDialog(DIALOG_WBE);
				new GetSyntelbookTask().execute("");
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	// 从本机中取号
	private void GetLocalContact() {
		// 读取手机中手机号
		Cursor cursor = getContentResolver().query(People.CONTENT_URI, null,
				null, null, null);

		while (cursor.moveToNext()) {
			Friend friend = new Friend();
			friend.friedsId = "";
			// 取得联系人名字
			int nameFieldColumnIndex = cursor.getColumnIndex(People.NAME);
			friend.friedsName = cursor.getString(nameFieldColumnIndex);
			// 取得电话号码
			int numberFieldColumnIndex = cursor.getColumnIndex(People.NUMBER);
			friend.friedsNumber = cursor.getString(numberFieldColumnIndex);
			friend.friedsNumber = GetNumber(friend.friedsNumber);
			// 是否屏蔽用户拉票
			// friend.isShield = false;
			// 是否是新好友
			// friend.isNewFriends = true;

			if (Common.IsUserNumber(friend.friedsNumber)
					&& !Common.IsContainFriends(frieds_vector,
							friend.friedsNumber)) {
				book_vector.add(friend);
			}

		}
		cursor.close();
	}

	// 从SIM卡中取号
	private void GetSimContact(String add) {
		// 读取SIM卡手机号,有两种可能:content://icc/adn与content://sim/adn
		try {
			Intent intent = new Intent();
			intent.setData(Uri.parse(add));
			Uri uri = intent.getData();
			mCursor = getContentResolver().query(uri, null, null, null, null);
			if (mCursor != null) {
				while (mCursor.moveToNext()) {
					Friend friend = new Friend();
					friend.friedsId = "";

					// 取得联系人名字
					int nameFieldColumnIndex = mCursor.getColumnIndex("name");
					friend.friedsName = mCursor.getString(nameFieldColumnIndex);
					// 取得电话号码
					int numberFieldColumnIndex = mCursor
							.getColumnIndex("number");
					friend.friedsNumber = mCursor
							.getString(numberFieldColumnIndex);
					friend.friedsNumber = GetNumber(friend.friedsNumber);
					// 是否屏蔽用户拉票
					// friend.isShield = false;
					// 是否是新好友
					// friend.isNewFriends = true;

					if (Common.IsUserNumber(friend.friedsNumber)
							&& !Common.IsContainFriends(book_vector,
									friend.friedsNumber)) {
						book_vector.add(friend);
					}
				}
				mCursor.close();
			}
		} catch (Exception e) {
			Log.v("E-GetSimContact", "" + e);
		}
	}

	private void AddDoMask(Activity activity) {
		showDialog(AddResult_DIALOG);

	}

	// 还原11位手机号
	public static String GetNumber(String num) {
		if (num != null) {
			if (num.startsWith("+86")) {
				num = num.substring(3);
			} else if (num.startsWith("86")) {
				num = num.substring(2);
			}
		} else {
			num = "";
		}
		return num;
	}

	/**
	 * 删除好友
	 * 
	 * @author user
	 * 
	 */
	private class GetRemoveTask extends AsyncTask<String, String, String> {
		// 删除好友--删除结果 0 失败、1 成功
		String str_remove_result;

		public String doInBackground(String... params) {
			try {
				String remove_url = HttpUrl.httpStr
						+ "relation/delfriend.action";
				String remove_in = "{\"userid\":\""
						+ AdPlatform.userId.toString() + "\",\"password\":\""
						+ md5.getMD5ofStr(AdPlatform.password)
						+ "\",\"touid\":\"";// + System.currentTimeMillis()"}";
				for (int i = 0; i < frieds_vector.size(); i++) {
					if ((Boolean) frieds_vector.get(i).isChecked == true) {
						remove_in += frieds_vector.get(i).friedsId + ";";
					}
				}

				remove_in += "\"}";
				Log.v("remove_in", "" + remove_in);
				String remove_out = HttpUrl.setPostUrl(remove_url, remove_in);// (remove_url);
				Log.v("remove_out", "" + remove_out);

				JSONObject remove_jsonObject = new JSONObject(remove_out);
				str_remove_result = remove_jsonObject.getString("result");

				Log.v("str_remove_result", "" + str_remove_result);

			} catch (Exception e) {
				Common.WebFailureDialog(frieds);
				Log.v("E-GetRemoveTask", "" + e);
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
				if (str_remove_result.equals("1")) {
					// list.clear();
					frieds_vector.clear();
					GetFriendsWeb();
					invitefriendadapter.notifyDataSetChanged();
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	private void EmptyDialog(Activity activity) {
		showDialog(Empty_DIALOG);
	}

	/**
	 * 按键按下
	 * 
	 * @return null
	 */
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			// Common.Finish(Land);
			Common.ChangeActivity(frieds, InviteFriend.this, AdP.class);
			this.finish();
		}
		return true;
	}

	OnKeyListener onKey = new OnKeyListener() {
		@Override
		public boolean onKey(View v, int keyCode, KeyEvent event) {
			// TODO Auto-generated method stub
			if (keyCode == KeyEvent.KEYCODE_ENTER) {
				InputMethodManager imm = (InputMethodManager) v.getContext()
						.getSystemService(Context.INPUT_METHOD_SERVICE);
				if (imm.isActive()) {
					imm.hideSoftInputFromWindow(v.getApplicationWindowToken(),
							0);
				}
				return true;
			}
			return false;
		}
	};
}
