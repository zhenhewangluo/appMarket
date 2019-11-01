package com.market.hjapp.tyxl;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ExpandableListActivity;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.ExpandableListView;
import android.widget.ExpandableListView.OnChildClickListener;
import android.widget.ExpandableListView.OnGroupExpandListener;


import com.market.hjapp.R;
import com.market.hjapp.tyxl.adapter.ExpandableListAdapter;
import com.market.hjapp.tyxl.object.HttpUrl;
import com.market.hjapp.tyxl.object.MD5;
public class DuiJiangHistory extends ExpandableListActivity {

	String company = null; // 快递公司名称
	String number = null; // 订单编号
	String sign_time = null; // 签收时间
	String sign_name = null; // 签收人
	ExpandableListView elv;
	List<Map<String, String>> groupData;
	private static final int DIALOG_WBE = 1;
	MD5 md5;
	public static ArrayList<History> history_vector1 = new ArrayList<History>();
	public static ArrayList<History> history_vector2 = new ArrayList<History>();
	public static ArrayList<History> history_vector3 = new ArrayList<History>();

	// 创建第一个一级条目下的的二级条目
	List<Map<String, Object>> child1 = new ArrayList<Map<String, Object>>();

	// 创建第一个一级条目下的的二级条目
	List<Map<String, Object>> child2 = new ArrayList<Map<String, Object>>();

	// 创建第一个一级条目下的的二级条目
	List<Map<String, Object>> child3 = new ArrayList<Map<String, Object>>();

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.history);
		md5 = new MD5();
		elv = (ExpandableListView) findViewById(android.R.id.list);

		new GetDuiJiangListTask().execute("");
		// 定义一个List，该List对象为一级条目提供数据
		groupData = new ArrayList<Map<String, String>>();
		Map<String, String> groupData1 = new HashMap<String, String>();
		groupData1.put("groupTextView",
				getResources().getString(R.string.active_history));
		Map<String, String> groupData2 = new HashMap<String, String>();
		groupData2.put("groupTextView",
				getResources().getString(R.string.exchange_history));
		Map<String, String> groupData3 = new HashMap<String, String>();
		groupData3.put("groupTextView",
				getResources().getString(R.string.lottery_hisyory));
		groupData.add(groupData1);
		groupData.add(groupData2);
		groupData.add(groupData3);

		List<List<Map<String, Object>>> childData = new ArrayList<List<Map<String, Object>>>();
		childData.add(child1);
		childData.add(child2);
		childData.add(child3);

		ExpandableListAdapter adapter = new ExpandableListAdapter(
				getApplicationContext(), groupData, R.layout.groups,
				new String[] { "groupTextView" }, new int[] { R.id.group_tv },
				childData, R.layout.dui_history, new String[] { "createon",
						// "id",
						"name", "picurl", "status" },
				// , "useraddr" },
				new int[] { R.id.time, R.id.name, R.id.jiangpin, R.id.state },
				elv);
		elv.setAdapter(adapter);
		elv.setOnGroupExpandListener(new MyGroupExpandListener());
		elv.setOnChildClickListener(new ChildClickListener());
	}

	// 为expandlistview的每一组设置监听器
	class MyGroupExpandListener implements OnGroupExpandListener {

		@Override
		public void onGroupExpand(int arg0) {
			// TODO Auto-generated method stub

		}
	}

	// 为expandlistview的每一组的每一项设置监听器
	class ChildClickListener implements OnChildClickListener {

		@Override
		public boolean onChildClick(ExpandableListView parent, View v,
				int groupPosition, int childPosition, long id) {
			// TODO Auto-generated method stub
			if (groupPosition == 0) {

			}
			if (groupPosition == 1) {
				int flag = Integer.parseInt((String) child2.get(childPosition)
						.get("status"));
				if (flag == 0) {
					Intent i = new Intent(DuiJiangHistory.this,
							UpdateAddress.class);
					startActivity(i);
					UpdateAddress.setLogId((String) child2.get(childPosition)
							.get("status"));
					UpdateAddress.setType("1");
				}
				if (flag == 1) {
					// alertResultDialog(1);
				}
				if (flag == 2) {
					// alertResultDialog(2);
				}
			}
			if (groupPosition == 2) {
				int flag = Integer.parseInt((String) child3.get(childPosition)
						.get("status"));
				if (flag == 0) {
					Intent i = new Intent(DuiJiangHistory.this,
							UpdateAddress.class);
					startActivity(i);
					UpdateAddress.setLogId((String) child3.get(childPosition)
							.get("status"));
					UpdateAddress.setType("2");
				}
				if (flag == 1) {
					// alertResultDialog(1);
				}
				if (flag == 2) {
					// alertResultDialog(2);
				}
			}
			return false;
		}
	}

	/**
	 * 获取兑奖历史列表
	 * 
	 * @author user
	 * 
	 */
	private class GetDuiJiangListTask extends AsyncTask<String, String, String> {

		String Province, City;

		public String doInBackground(String... params) {

			try {
				String ActiveList_url = HttpUrl.httpStr
						+ "active/userwinnerinfo.action?userid="
						+ AdPlatform.userId;// + "&password="
				// + md5.getMD5ofStr(AdPlatform.password);

				String ActiveList_out = HttpUrl.setGetUrl(ActiveList_url);
				JSONObject ActiveList_jsonObject = new JSONObject(
						ActiveList_out);
				JSONArray ActiveList_jsonArray = ActiveList_jsonObject
						.getJSONArray("userPrizeInfo");
				for (int i = 0; i < ActiveList_jsonArray.length(); i++) {
					JSONObject ActiveList_jsonObject_0 = (JSONObject) ActiveList_jsonArray
							.opt(i);
					History Active_history = new History();
					Active_history.createon = ActiveList_jsonObject_0
							.getString("createon");
					Active_history.id = ActiveList_jsonObject_0.getString("id");
					Active_history.name = ActiveList_jsonObject_0
							.getString("name");
					Active_history.picurl = ActiveList_jsonObject_0
							.getString("picurl");
					Active_history.status = ActiveList_jsonObject_0
							.getString("status");
					Active_history.useraddr = ActiveList_jsonObject_0
							.getString("useraddr");
					if (!IsInHistoryList(history_vector1, Active_history.id)) {
						history_vector1.add(Active_history);
					}
				}

				String historyList_url = HttpUrl.httpStr
						+ "active/exchangeloglist.action?userid="
						+ AdPlatform.userId + "&password="
						+ md5.getMD5ofStr(AdPlatform.password);

				String historyList_out = HttpUrl.setGetUrl(historyList_url);

				JSONObject historyList_jsonObject = new JSONObject(
						historyList_out);
				JSONArray historyList_jsonArray = historyList_jsonObject
						.getJSONArray("exchangelogProductionInfo");
				for (int i = 0; i < historyList_jsonArray.length(); i++) {
					JSONObject historyList_jsonObject_0 = (JSONObject) historyList_jsonArray
							.opt(i);
					History history = new History();
					history.createon = historyList_jsonObject_0
							.getString("createon");
					history.id = historyList_jsonObject_0.getString("id");
					history.name = historyList_jsonObject_0.getString("name");
					history.picurl = historyList_jsonObject_0
							.getString("picurl");
					history.status = historyList_jsonObject_0
							.getString("status");
					history.useraddr = historyList_jsonObject_0
							.getString("useraddr");
					if (!IsInHistoryList(history_vector2, history.id)) {
						history_vector2.add(history);
					}
				}

				String lotteryhistoryList_url = HttpUrl.httpStr
						+ "active/lotteryloglist.action?userid="
						+ AdPlatform.userId + "&password="
						+ md5.getMD5ofStr(AdPlatform.password);
				String lotteryhistoryList_out = HttpUrl
						.setGetUrl(lotteryhistoryList_url);
				JSONObject lotteryhistoryList_jsonObject = new JSONObject(
						lotteryhistoryList_out);
				JSONArray lotteryhistoryList_jsonArray = lotteryhistoryList_jsonObject
						.getJSONArray("lotterylogProductInfo");
				for (int i = 0; i < lotteryhistoryList_jsonArray.length(); i++) {
					JSONObject lotteryhistoryList_jsonObject_0 = (JSONObject) lotteryhistoryList_jsonArray
							.opt(i);
					History lottery_history = new History();
					lottery_history.createon = lotteryhistoryList_jsonObject_0
							.getString("createon");
					lottery_history.id = lotteryhistoryList_jsonObject_0
							.getString("id");
					lottery_history.name = lotteryhistoryList_jsonObject_0
							.getString("name");
					lottery_history.picurl = lotteryhistoryList_jsonObject_0
							.getString("picurl");
					lottery_history.status = lotteryhistoryList_jsonObject_0
							.getString("status");
					lottery_history.useraddr = lotteryhistoryList_jsonObject_0
							.getString("useraddr");
					if (!IsInHistoryList(history_vector3, lottery_history.id)) {
						history_vector3.add(lottery_history);
					}
				}

			} catch (Exception e) {
				Common.WebFailureDialog(DuiJiangHistory.this);
				Log.v("E-GetAddressListTask", "" + e);
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
				if (history_vector1.size() == 0) {
				} else {
					for (int i = 0; i < history_vector1.size(); i++) {
						Map<String, Object> childData1 = new HashMap<String, Object>();
						childData1.put("createon",
								history_vector1.get(i).createon);
						childData1.put("id", history_vector1.get(i).id);
						childData1.put("name", history_vector1.get(i).name);
						childData1.put("picurl", history_vector1.get(i).picurl);
						childData1.put("status", history_vector1.get(i).status);
						childData1.put("useraddr",
								history_vector1.get(i).useraddr);
						child1.add(childData1);
					}
				}

				if (history_vector2.size() == 0) {
				} else {
					for (int i = 0; i < history_vector2.size(); i++) {
						Map<String, Object> childData2 = new HashMap<String, Object>();
						childData2.put("createon",
								history_vector2.get(i).createon);
						childData2.put("id", history_vector2.get(i).id);
						childData2.put("name", history_vector2.get(i).name);
						childData2.put("picurl", history_vector2.get(i).picurl);
						childData2.put("status", history_vector2.get(i).status);
						childData2.put("useraddr",
								history_vector2.get(i).useraddr);
						child2.add(childData2);
					}
				}
				if (history_vector3.size() == 0) {
				} else {
					for (int i = 0; i < history_vector3.size(); i++) {
						Map<String, Object> childData3 = new HashMap<String, Object>();
						childData3.put("createon",
								history_vector3.get(i).createon);
						childData3.put("id", history_vector3.get(i).id);
						childData3.put("name", history_vector3.get(i).name);
						childData3.put("picurl", history_vector3.get(i).picurl);
						childData3.put("status", history_vector3.get(i).status);
						childData3.put("useraddr",
								history_vector3.get(i).useraddr);
						child3.add(childData3);
					}
				}
				removeDialog(DIALOG_WBE);
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	// 是否在LIST有值
	private boolean IsInHistoryList(ArrayList<History> list, String un) {
		for (int i = 0; i < list.size(); i++) {
			if (un.equals(list.get(i).id)) {
				return true;
			}
		}
		return false;
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
		return null;
	}

	// 奖品状态提示框
	private void alertResultDialog(int state) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(
				DuiJiangHistory.this);
		switch (state) {
		case 0:
			alertDialog.setTitle(getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message22));
			alertDialog.setPositiveButton(
					getResources().getString(R.string.alert_message23),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {

						}
					});
			alertDialog.setNegativeButton(
					getResources().getString(R.string.cancel),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {

						}
					});
			break;
		case 1:
			alertDialog.setTitle(getResources().getString(
					R.string.alert_message24));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message25)
					+ company
					+ "\n"
					+ getResources().getString(R.string.alert_message26)
					+ number);
			alertDialog.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {

						}
					});
			break;
		case 2:
			alertDialog.setTitle(getResources().getString(
					R.string.alert_message27));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message25)
					+ company
					+ "\n"
					+ getResources().getString(R.string.alert_message26)
					+ number
					+ getResources().getString(R.string.alert_message28)
					+ sign_time
					+ getResources().getString(R.string.alert_message29)
					+ sign_name);
			alertDialog.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {

						}
					});
			break;
		}
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

}
