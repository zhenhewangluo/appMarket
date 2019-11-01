package com.market.hjapp.tyxl;

import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnKeyListener;
import android.view.inputmethod.InputMethodManager;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.EditText;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.object.CustomDialog;
import com.market.hjapp.tyxl.object.HttpUrl;
import com.market.hjapp.tyxl.object.MD5;
import com.market.hjapp.tyxl.object.Records;

public class Land extends Activity {

	static Land Land;
	MD5 md5;
	// 登录提示文字
	TextView land_up_text;
	// 手机号及密码输入框
	EditText phone_info, password_info;
	// 记住密码选择框
	CheckBox check;
	// 手机号、密码
	protected String numberStr = "";
	protected String passwordStr = "";
	String deviceid;
	String tel;
	String imei;
	String imsi;

	// Boolean onlineOrnot;

	// AdP adp;Login

	// 本机的手机号
	// protected String numberStr = "15210040049";

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		Log.e("初始化LAND", "--------------");
		setContentView(R.layout.land);
		TelephonyManager tm = (TelephonyManager) this
				.getSystemService(Context.TELEPHONY_SERVICE);
		deviceid = tm.getDeviceId();
		tel = tm.getLine1Number();
		imei = tm.getSimSerialNumber();
		imsi = tm.getSubscriberId();
		Log.v("imei", imei + "    " + imsi);

		Land = this;
		Log.v("", "Land");
		md5 = new MD5();
		// 说明文字
		land_up_text = (TextView) findViewById(R.id.land_up_text);
		land_up_text.setText(getResources().getString(R.string.land_text));

		phone_info = (EditText) findViewById(R.id.phone_info);
		password_info = (EditText) findViewById(R.id.password_info);
		password_info.setOnKeyListener(onKey);
		getWindow().setSoftInputMode(
				WindowManager.LayoutParams.SOFT_INPUT_STATE_HIDDEN);

		check = (CheckBox) findViewById(R.id.check);
		// 读取记录 是否选中记住密码
		if (Records.loadIntRecord(Land, AdPlatform.Rms_JiZhuPassword) == 1) {
			// 选中
			check.setChecked(true);
			// 读取记录 手机号、密码
			if (!Records.loadStringRecord(Land, AdPlatform.Rms_Registration)
					.equals("")) {
				String[] Str = Common.mySplict(Records.loadStringRecord(Land,
						AdPlatform.Rms_Registration), '|'); // 上次登录时 点了记住密码选项
															// str就能读到文字
				if (Str.length > 0) {
					AdPlatform.phone = Str[0];
					AdPlatform.password = Str[1];
					Log.v("phone", "" + AdPlatform.phone);
					Log.v("password", "" + AdPlatform.password);
				}
				phone_info.setText(AdPlatform.phone);
				password_info.setText(AdPlatform.password);
			}
		} else {
			// 读取记录未选中
			Log.v("check", "false");
			check.setChecked(false);
		}

		// 给CheckBox设置事件监听
		check.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
			@Override
			public void onCheckedChanged(CompoundButton buttonView,
					boolean isChecked) {
				// TODO Auto-generated method stub
				if (isChecked) {
					Log.v("", "选中");
					// 手机号、密码输入框，数据提取
					hadPhoneOrPassword();
					// 存储记住密码
					if (Common.IsUserNumber(AdPlatform.phone)) {
						Records.saveIntRecord(Land,
								AdPlatform.Rms_JiZhuPassword, 1);
						// 存储 phone、password
						Records.saveStringRecord(Land,
								AdPlatform.Rms_Registration, AdPlatform.phone
										+ "|" + AdPlatform.password);
					}
				} else {
					Log.v("", "取消选中");
					// 存储不记住密码
					Records.saveIntRecord(Land, AdPlatform.Rms_JiZhuPassword, 0);
					// Records.saveStringRecord(Land,
					// AdPlatform.Rms_Registration,
					// "");
					// Records.deletStringRecord(Land,)
				}
			}
		});

		// 获取Button 资源
		Button btn_registration = (Button) findViewById(R.id.registration);// 注册
		Button btn_land = (Button) findViewById(R.id.land);// 确定
		Button btn_reset = (Button) findViewById(R.id.reset);// 重置密码
		btn_registration.setBackgroundResource(R.drawable.btn_long_selector);
		btn_land.setBackgroundResource(R.drawable.btn_ok_selector);
		btn_reset.setBackgroundResource(R.drawable.btn_ok_selector);
		
		
		btn_registration.setOnClickListener(btnClick);
		btn_land.setOnClickListener(btnClick);
		btn_reset.setOnClickListener(btnClick);
	}

	// 按钮监听
	private OnClickListener btnClick = new OnClickListener() {
		public void onClick(View v) {
			switch (v.getId()) {
			case R.id.registration:
				if (Common.onlineOrnot) {
					Common.ChangeActivity(Land, Land.this, Registration.class);
					// 注册、设置切换注册账号功能
					Registration.setIsReset(0);
				} else {
					Common.WebFailureDialog(Land);
					// showDialog();
				}
				break;
			case R.id.land:
				// 手机号、密码输入框，数据提取
				if (!hadPhoneOrPassword()) {
					showDialog(PhoneOrPasswordNull);
				} else {
					// 联网登陆
					Log.v("numberStr", numberStr + "a");
					if (IsUserNumber(numberStr.trim())) {
						if (check.isChecked()) {
							Records.saveIntRecord(Land,
									AdPlatform.Rms_JiZhuPassword, 1);
							// 存储 phone、password
							Records.saveStringRecord(Land,
									AdPlatform.Rms_Registration,
									AdPlatform.phone + "|"
											+ AdPlatform.password);
						}
						new LandTask().execute("");
					} else {
						showDialog(PhoneWrong);
					}
				}
				break;
			case R.id.reset:
				if (Common.onlineOrnot) {
					Common.ChangeActivity(Land, Land.this, Registration.class);
					// 重置、设置切换重置密码功能
					Registration.setIsReset(2);
				} else {
					Common.WebFailureDialog(Land);
				}
				break;
			}
		}
	};

	/**
	 * 手机号、密码输入框，数据提取
	 */
	private Boolean hadPhoneOrPassword() {
		Boolean result = true;
		Appendable phone_value = phone_info.getText();
		Appendable password_value = password_info.getText();
		Log.v("phone_value", "" + phone_value);
		Log.v("password_value", "" + password_value);
		if (!String.valueOf(phone_value).equals("")
				&& !String.valueOf(phone_value).equals(null)) {
			numberStr = String.valueOf(phone_value);
		}
		if (!String.valueOf(password_value).equals("")
				&& !String.valueOf(password_value).equals(null)) {
			passwordStr = String.valueOf(password_value);
		}
		AdPlatform.phone = numberStr;
		AdPlatform.password = passwordStr;
		if (numberStr.equals("") || passwordStr.equals("")) {
			result = false;
		}
		Log.v("phone", "" + AdPlatform.phone);
		Log.v("password", "" + AdPlatform.password);
		return result;
	}

	// 是否为手机号码
	public static boolean IsUserNumber(String num) {
		boolean re = false;
		if (num.length() == 11) {
			if (num.startsWith("13")) {
				re = true;
			} else if (num.startsWith("15")) {
				re = true;
			} else if (num.startsWith("18")) {
				re = true;
			}
		}
		return re;
	}

	private static final int DIALOG_KEY = 0;
	private static final int Password_wrong = 1;
	private static final int Change_phone = 2;
	private static final int PhoneOrPasswordNull = 3;
	private static final int PhoneWrong = 4;
	private static final int UnRegistration = 5;
	private static final int LandFailureDialog = 6;

	private String land_results, password_results;

	/**
	 * 登陆账号
	 * 
	 * @author Administrator
	 * 
	 */
	private class LandTask extends AsyncTask<String, String, String> {

		public String doInBackground(String... params) {
			try {
				String login_url = HttpUrl.httpStr + "user/login.action";
				Log.v("login_url", login_url);

				String login_in = "{\"phone\":" + numberStr
						+ ",\"password\":\"" + md5.getMD5ofStr(passwordStr)
						+ "\",\"imeino\":\"" + imei + "\",\"imsino\":\"" + imsi
						+ "\"}";
				Log.v("login_in", login_in);

				String login_out = HttpUrl.setPostUrl(login_url, login_in);
				Log.v("login_out", login_out);

				if (!login_out.equals(null) && !login_out.equals("null")) {
					JSONObject jsonObject = new JSONObject(login_out);
					land_results = jsonObject.getString("loginflag");
					AdPlatform.userId = jsonObject.getString("userid");
					password_results = jsonObject.getString("password");
					// AdPlatform.mainState = 0;
					Log.v("AdPlatform.mainState", "" + AdPlatform.mainState);
					Log.v("land_results", "" + land_results);
				}
				// MainActivity.login_results
			} catch (Exception e) {
				// MainActivity.login_results = "0";
				// Common.WebFailureDialog(Land);
				Log.v("E-Login", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_KEY);
		}

		// land_results:-1 用户没有注册 -2 密码错误 0登录失败 1登录成功 2 更换imsi
		@Override
		public void onPostExecute(String Re) {

			try {
				removeDialog(DIALOG_KEY);
				if (land_results.equals("1")) {
					// 联网结束
					// 登陆成功 处理
					// 切换Activity 主界面
					Common.ChangeActivity(Land, Land.this, AdP.class);
					// Land.finish();
					// 传递登陆结果 确定 主界面状态
					AdP.setLogin(land_results);
					AdPlatform.mainState = 0;
				} else {
					if (land_results.equals("-2")) {
						showDialog(Password_wrong);
					} else {
						if (land_results.equals("2")) {
							showDialog(Change_phone);
						} else {
							if (land_results.equals("-1")) {
								showDialog(UnRegistration);
							} else {
								// 登陆失败 处理
								showDialog(LandFailureDialog);
							}
						}
					}
				}
			} catch (Exception e) {
				// MainActivity.login_results = "0";
				// Common.WebFailureDialog(Land);
				Log.v("E-Login", "" + e);
			}

		}
	}

	// 弹出"查看"对话框
	@Override
	protected Dialog onCreateDialog(int id) {
		Dialog dialog = null;
		switch (id) {
		case DIALOG_KEY: {
			ProgressDialog dialog1 = new ProgressDialog(this);
			dialog1.setMessage(getResources().getString(
					R.string.alert_message10));
			dialog1.setIndeterminate(true);
			dialog1.setCancelable(true);
			return dialog1;
		}
		case Password_wrong:
			CustomDialog.Builder PasswordwrongBuilder = new CustomDialog.Builder(
					Land.this, R.layout.alertdialog);
			PasswordwrongBuilder
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message8))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									passwordStr = "";
									password_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = PasswordwrongBuilder.create();
			break;
		case Change_phone:
			CustomDialog.Builder ChangephoneBuilder = new CustomDialog.Builder(
					Land.this, R.layout.alertdialog);
			ChangephoneBuilder
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message21))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									Records.saveIntRecord(Land,
											AdPlatform.Rms_JiZhuPassword, 0);
									numberStr = "";
									passwordStr = "";
									phone_info.setText("");
									password_info.setText("");
									check.setChecked(false);
									dialog.dismiss();
									return;
								}
							});
			dialog = ChangephoneBuilder.create();
			break;
		case PhoneOrPasswordNull:
			CustomDialog.Builder phoneOrpassworduull = new CustomDialog.Builder(
					Land.this, R.layout.alertdialog);
			phoneOrpassworduull
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("手机号和密码不能为空")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
//									numberStr = "";
//									passwordStr = "";
//									phone_info.setText("");
//									password_info.setText("");
//									check.setChecked(false);
									dialog.dismiss();
									return;
								}
							});
			dialog = phoneOrpassworduull.create();
			break;
		case PhoneWrong:
			CustomDialog.Builder PhoneWrong = new CustomDialog.Builder(
					Land.this, R.layout.alertdialog);
			PhoneWrong
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message9))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									numberStr = "";
									passwordStr = "";
									phone_info.setText("");
									password_info.setText("");
									check.setChecked(false);
									dialog.dismiss();
									return;
								}
							});
			dialog = PhoneWrong.create();
			break;
		case UnRegistration:
			CustomDialog.Builder unRegistration = new CustomDialog.Builder(
					Land.this, R.layout.alertdialog);
			unRegistration
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("您还不是夺宝用户，请先注册！")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									numberStr = "";
									passwordStr = "";
									phone_info.setText("");
									password_info.setText("");
									check.setChecked(false);
									dialog.dismiss();
									return;
								}
							});
			dialog = unRegistration.create();
			break;
		case LandFailureDialog:
			CustomDialog.Builder landFailure = new CustomDialog.Builder(
					Land.this, R.layout.alertdialog);
			landFailure
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("注册失败")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									dialog.dismiss();
									return;
								}
							});
			dialog = landFailure.create();
			break;
		}

		return dialog;
	}

	// private void WebFailureDialog(Activity activity) {
	// AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
	// alertDialog.setTitle(getResources().getString(R.string.alert));
	// alertDialog.setMessage(getResources()
	// .getString(R.string.alert_message8));
	// alertDialog.setPositiveButton(getResources().getString(R.string.ok),
	// new DialogInterface.OnClickListener() {
	// public void onClick(DialogInterface dialog, int which) {
	// // 切换Activity 主界面
	// // Common.ChangeActivity(Land, Land.this, AdP.class);
	// // AdP.setLogin(land_results);
	// return;
	// }
	// });
	//
	// alertDialog.create(); // 创建对话框
	// alertDialog.show(); // 显示对话框
	// }
	//
	// private void ImeiChangDialog(Activity activity) {
	// AlertDialog.Builder alertDialog1 = new AlertDialog.Builder(activity);
	// alertDialog1.setTitle(getResources().getString(R.string.alert));
	// alertDialog1.setMessage(getResources().getString(
	// R.string.alert_message21));
	// alertDialog1.setPositiveButton(getResources().getString(R.string.ok),
	// new DialogInterface.OnClickListener() {
	// public void onClick(DialogInterface dialog, int which) {
	// // 切换Activity 主界面
	// Common.ChangeActivity(Land, Land.this,
	// Registration.class);
	// // 重置、设置切换重置密码功能
	// Registration.setIsReset(2);
	// return;
	// }
	// });
	// alertDialog1.setNegativeButton(getResources()
	// .getString(R.string.cancel),
	// new DialogInterface.OnClickListener() {
	// public void onClick(DialogInterface dialog, int which) {
	// return;
	// }
	// });
	// alertDialog1.create(); // 创建对话框
	// alertDialog1.show(); // 显示对话框
	// }

	/**
	 * 按键按下
	 * 
	 * @return null
	 */
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			// Common.Finish(Land);
			Common.ChangeActivity(Land, Land.this, AdP.class);
			this.finish();
		}
		return true;
	}

	/**
	 * 按键弹起
	 * 
	 * @return null
	 */
	public boolean onKeyUp(int keyCode, KeyEvent event) {
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
