package com.market.hjapp.tyxl;
import java.util.ArrayList;

import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Application;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.pm.ApplicationInfo;
import android.content.pm.PackageManager;
import android.os.AsyncTask;
import android.os.Bundle;
import android.telephony.SmsManager;
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
import android.widget.EditText;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.object.CustomDialog;
import com.market.hjapp.tyxl.object.HttpUrl;
import com.market.hjapp.tyxl.object.MD5;
import com.market.hjapp.tyxl.object.Records;

public class Registration extends Activity {

	static Registration Reg;
	MD5 md5;
	// Land land;
	TextView up_text;
	EditText phone_info, password1_info, password2_info;

	public static final String SM_CMD_REGISTER = "1"; // 短信命令 1：注册
	// public static final String SM_CMD_REG_SUCCESS = "12"; //短信命令 12：注册成功
	public static final String SM_CMD_VOTE = "2"; // 短信命令 2：投票
	public static final String SM_CMD_RESET_PWD = "3"; // 短信命令 3：重置密码

	// 本机的手机号
	protected String numberStr = "";
	protected String passwordStr1 = "";
	protected String passwordStr2 = "";
	protected String passwordStr = "";
	protected String Instruction = "";

	private String password_results = "";

	protected static int isReset;// 0:注册 1:重置密码 2:更换手机卡及手机

	String deviceid;
	String tel;
	String imei;
	String imsi;
	String land_results;

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.registration);

		TelephonyManager tm = (TelephonyManager) this
				.getSystemService(Context.TELEPHONY_SERVICE);
		deviceid = tm.getDeviceId();
		tel = tm.getLine1Number();
		imei = tm.getSimSerialNumber();
		imsi = tm.getSubscriberId();

		Reg = this;
		Log.v("", "Registration");
		md5 = new MD5();

		up_text = (TextView) findViewById(R.id.up_text);
		// if (isReset == 1) {
		// up_text.setText(getResources().getString(R.string.reset_password));
		// } else {
		if (isReset == 0) {
			up_text.setText(getResources().getString(R.string.register));
		} else {
			up_text.setText(getResources().getString(R.string.reset_password));
		}

		// }
		phone_info = (EditText) findViewById(R.id.phone_info);
		password1_info = (EditText) findViewById(R.id.password1_info);
		password2_info = (EditText) findViewById(R.id.password2_info);
		password2_info.setOnKeyListener(onKey);
		getWindow().setSoftInputMode(
				WindowManager.LayoutParams.SOFT_INPUT_STATE_HIDDEN);

		// 获取Button 资源
		Button btn_ok = (Button) findViewById(R.id.ok);
		Button btn_back = (Button) findViewById(R.id.back);

		btn_ok.setOnClickListener(btnClick);
		btn_back.setOnClickListener(btnClick);
	}

	// 按钮监听
	private OnClickListener btnClick = new OnClickListener() {
		public void onClick(View v) {
			switch (v.getId()) {
			case R.id.ok:
				if (!getEditText()) {
					showDialog(CanNotNull);
				} else {
					if (Common.IsUserNumber(numberStr)) {
						if (passwordStr1.length() >= 6) {
							if (passwordStr1.equals(passwordStr2)) {
								passwordStr = passwordStr1;
								String mobile = "";
								String content = "";
								// 确定对应短信通道号码
								if (IsYidongNumber(numberStr)) {
									mobile = "106575000646032";// 移动106575000646032黑
								} else if (IsLiantongNumber(numberStr)) {
									mobile = "1065505995032";// 联通
								} else if (IsDianxinNumber(numberStr)) {
									mobile = "1065905760202032";// 电信
								} else {
									return;
								}
								if (isReset == 1) {
									Log.v("--------", "重置密码");
									content = SM_CMD_RESET_PWD + passwordStr;
								} else {
									if (isReset == 0 || isReset == 2) {
										Log.v("--------", "注册");
										content = SM_CMD_REGISTER + passwordStr;
									}
								}
								try {
									// 获得发送短信的管理器，使用的是android.telephony.SmsManager
									SmsManager smsManager = SmsManager
											.getDefault();
									// 如果短信内容过长则分段发送
									if (content.length() > 70) {
										// 使用短信管理器进行短信内容的分段，返回分成的段
										ArrayList<String> contents = smsManager
												.divideMessage(content);
										for (String msg : contents) {
											// 使用短信管理器发送短信内容
											// 参数一为短信接收者
											// 参数三为短信内容
											// 其他可以设为null
											smsManager.sendTextMessage(mobile,
													null, msg, null, null);
										}
										// 否则一次过发送
									} else {
										smsManager.sendTextMessage(mobile,
												null, content, null, null);
									}
									
									
									// if (isReset == 1) {
									// // 重置密码
									// new ResetTask().execute("");
									// } else {
									if (isReset == 0) {
										// 注册
										new RegistrationTask().execute("");
									} else {
										// 重置imei
										new ResetImeiTask().execute("");
									}
									// }
								} catch (Exception e) {
									removeDialog(DIALOG_KEY);
									showDialog(HasPermission);
								}

							} else {
								// 密码和验证密码不一致
								showDialog(PasswordWrong);
							}
						}else{
							showDialog(PasswordLess);
						}
					} else {
						// 输入手机号不正确
						showDialog(PhoneWrong);
					}
				}
				break;
			case R.id.back:
				Log.v("--------", "返回");
				// 获取主界面相关数据
				// 跳转主界面
				// 切换Activity 主界面
				Common.ChangeActivity(Reg, Registration.this, Land.class);
				break;
			}
		}

		/**
		 * 获取输入数据
		 */
		private Boolean getEditText() {
			Boolean result = true;
			Appendable phone_value = phone_info.getText();
			Appendable password1_value = password1_info.getText();
			Appendable password2_value = password2_info.getText();
			if (!String.valueOf(phone_value).equals("")
					&& !String.valueOf(phone_value).equals(null)) {
				numberStr = String.valueOf(phone_value);
			} else {
				result = false;
			}
			if (!String.valueOf(password1_value).equals("")
					&& !String.valueOf(password1_value).equals(null)) {
				passwordStr1 = String.valueOf(password1_value);
			} else {
				result = false;
			}
			if (!String.valueOf(password2_value).equals("")
					&& !String.valueOf(password2_value).equals(null)) {
				passwordStr2 = String.valueOf(password2_value);
			} else {
				result = false;
			}
			return result;
		}

		private void passwordWrong() {
			AlertDialog.Builder alertDialog = new AlertDialog.Builder(Reg);
			alertDialog.setTitle(getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message8));
			alertDialog.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							passwordStr1 = "";
							passwordStr2 = "";
							password1_info.setText("");
							password2_info.setText("");
							return;
						}
					});
			alertDialog.create(); // 创建对话框
			alertDialog.show(); // 显示对话框
		}

		private void phoneWrong() {
			AlertDialog.Builder alertDialog = new AlertDialog.Builder(Reg);
			alertDialog.setTitle(getResources().getString(R.string.alert));
			alertDialog.setMessage(getResources().getString(
					R.string.alert_message9));
			alertDialog.setPositiveButton(
					getResources().getString(R.string.ok),
					new DialogInterface.OnClickListener() {
						public void onClick(DialogInterface dialog, int which) {
							numberStr = "";
							phone_info.setText("");
							return;
						}
					});
			alertDialog.create(); // 创建对话框
			alertDialog.show(); // 显示对话框
		}
	};

	private static final int DIALOG_KEY = 0;
	private static final int RegistrationSuccessDialog = 1;
	private static final int RegistrationFailureDialog = 2;
	private static final int PasswordWrong = 3;
	private static final int PhoneWrong = 4;
	private static final int RegistrationNotSuccessDialog = 5;
	private static final int ResetSuccessDialog = 6;
	private static final int ResetFailureDialog = 7;
	private static final int PhoneFailureDialog = 8;
	private static final int HasPermission = 9;
	private static final int CanNotNull = 10;
	private static final int ResetNotSuccessDialog = 11;
	private static final int PasswordLess=12;

	// 注册账号
	private class RegistrationTask extends AsyncTask<String, String, String> {
		String result;

		public String doInBackground(String... params) {
			try {
				// 联网 获取数据
				// 其他协议的ip：prot都替换为：192.168.1.220:8081
				// 地址里的kxdb改为kxdbsvr
				String Reg_url = HttpUrl.httpStr + "user/register.action";
				Log.v("Reg_url", Reg_url);

				String Reg_in = "{\"phone\":" + numberStr + ",\"password\":\""
						+ md5.getMD5ofStr(passwordStr) + "\",\"ts\":"
						+ System.currentTimeMillis() + ",\"imeino\":" + imei
						+ ",\"imsino\":" + imsi + "}";
				Log.v("Reg_in", Reg_in);
				String Reg_out = HttpUrl.setPostUrl(Reg_url, Reg_in);

				Log.v("Reg_out", "" + Reg_out);
				if (!Reg_out.equals(null) && !Reg_out.equals("null")) {
					JSONObject jsonObject = new JSONObject(Reg_out);
					// .getJSONObject("regUser");
					result = jsonObject.getString("result");
					// if (result.equals("1")) {
					AdPlatform.userId = jsonObject.getString("userid");
					String password = jsonObject.getString("password");
					Log.v("userId", "" + AdPlatform.userId);
					Log.v("password", "" + AdPlatform.password);
					// }
				}

			} catch (Exception e) {
				WebFailureDialog(Reg);
				Log.v("E-RegistrationTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_KEY);
		}

		@Override
		public void onPostExecute(String Re) {
			// 联网结束
			try {
				Log.e("result========================", "" + result);
				removeDialog(DIALOG_KEY);
				if (result.equals("5")) {
					showDialog(RegistrationNotSuccessDialog);
				} else if (result.equals("1")) {
					// 注册成功
					showDialog(RegistrationSuccessDialog);
				} else {
					// 失败
					showDialog(RegistrationFailureDialog);
				}
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	// 重置imei
	private class ResetImeiTask extends AsyncTask<String, String, String> {
		String result;

		public String doInBackground(String... params) {
			try {
				// 联网 获取数据
				// 其他协议的ip：prot都替换为：192.168.1.220:8081
				// 地址里的kxdb改为kxdbsvr
				String Reg_url = HttpUrl.httpStr + "user/register1.action";
				Log.v("Reg_url", Reg_url);

				String Reg_in = "{\"phone\":" + numberStr + ",\"password\":\""
						+ md5.getMD5ofStr(passwordStr) + "\",\"ts\":"
						+ System.currentTimeMillis() + ",\"imeino\":" + imei
						+ ",\"imsino\":" + imsi + "}";
				Log.v("Reg_in", Reg_in);
				String Reg_out = HttpUrl.setPostUrl(Reg_url, Reg_in);

				Log.v("Reg_out", "" + Reg_out);
				if (!Reg_out.equals(null) && !Reg_out.equals("null")) {
					JSONObject jsonObject = new JSONObject(Reg_out);
					// .getJSONObject("regUser");
					result = jsonObject.getString("result");
					// if (result.equals("1")) {
					AdPlatform.userId = jsonObject.getString("userid");
					String password = jsonObject.getString("password");
					Log.v("userId", "" + AdPlatform.userId);
					Log.v("password", "" + AdPlatform.password);
					// }
				}

			} catch (Exception e) {
				WebFailureDialog(Reg);
				Log.v("E-RegistrationTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_KEY);
		}

		@Override
		public void onPostExecute(String Re) {
			// 联网结束
			Log.e("result========================", "" + result);
			try {
				removeDialog(DIALOG_KEY);
				if (result.equals("5")) {
					showDialog(ResetNotSuccessDialog);
				} else if (result.equals("1")) {
					// 注册成功
					showDialog(ResetSuccessDialog);
				} else {
					// 失败 result:0
					showDialog(ResetFailureDialog);
				}
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
			}
		}
	}

	/**
	 * 重置密码
	 * 
	 * @author Administrator
	 * 
	 */
	private class ResetTask extends AsyncTask<String, String, String> {
		String result;

		public String doInBackground(String... params) {
			try {
				// 联网 获取数据
				// 其他协议的ip：prot都替换为：192.168.1.220:8081
				// 地址里的kxdb改为kxdbsvr
				String Reset_url = HttpUrl.httpStr + "user/resetpwd.action";
				Log.v("Reset_url", "" + Reset_url);
				String Reset_in = "{\"phone\":" + numberStr
						+ ",\"password\":\"" + md5.getMD5ofStr(passwordStr)
						+ "\",\"ts\":" + System.currentTimeMillis()
						+ ",\"imeino\":" + imei + ",\"imsino\":" + imsi + "}";
				Log.v("Reset_in", "" + Reset_in);
				String Reset_out = HttpUrl.setPostUrl(Reset_url, Reset_in);

				Log.v("Reset_out", "" + Reset_out);
				if (!Reset_out.equals(null) && !Reset_out.equals("null")) {
					JSONObject jsonObject = new JSONObject(Reset_out);
					// .getJSONObject("regUser");
					result = jsonObject.getString("result");
					AdPlatform.userId = jsonObject.getString("userid");
					String password = jsonObject.getString("password");
					Log.v("userId", "" + AdPlatform.userId);
					Log.v("password", "" + AdPlatform.password);
					Log.v("password1111111111111", "" + password);
				}
			} catch (Exception e) {
				WebFailureDialog(Reg);
				Log.v("E-ResetTask", "" + e);
			}
			return "";
		}

		@Override
		protected void onPreExecute() {
			showDialog(DIALOG_KEY);
		}

		@Override
		public void onPostExecute(String Re) {
			// 联网结束
			try {
				removeDialog(DIALOG_KEY);
				if (result.equals("1")) {
					// 重置密码成功
					showDialog(ResetSuccessDialog);
				} else {
					if (result.equals("2")) {

					}
					// 失败
					showDialog(ResetFailureDialog);
				}
			} catch (Exception e) {
				Log.v("E-ResetTask", "" + e);
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
		case PasswordWrong:
			CustomDialog.Builder PasswordWrong = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			PasswordWrong
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("两次输入的密码不一致！")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									passwordStr1 = "";
									passwordStr2 = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = PasswordWrong.create();
			break;
		case PasswordLess:
			CustomDialog.Builder passwordless = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			passwordless
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("密码不能少于六位！")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									passwordStr1 = "";
									passwordStr2 = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = passwordless.create();
			break;
		case PhoneWrong:
			CustomDialog.Builder PhoneWrong = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			PhoneWrong
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message9))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									numberStr = "";
									phone_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = PhoneWrong.create();
			break;
		case RegistrationNotSuccessDialog:
			CustomDialog.Builder ReNotSuccess = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			ReNotSuccess
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message12))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									Common.ChangeActivity(Reg,
											Registration.this, Land.class);
									passwordStr1 = "";
									passwordStr2 = "";
									passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = ReNotSuccess.create();
			break;

		case RegistrationSuccessDialog:
			CustomDialog.Builder ReSuccess = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			ReSuccess
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message13)
									+ numberStr
									+ getResources().getString(
											R.string.alert_message14)
									+ passwordStr
									+ getResources().getString(
											R.string.alert_message15))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									Records.saveIntRecord(Reg,
											AdPlatform.Rms_JiZhuPassword, 0);
									AdPlatform.password = passwordStr;
									AdPlatform.phone = numberStr;
									// Common.ChangeActivity(Reg,
									// Registration.this, AdP.class);
									new LandTask().execute("");
									passwordStr1 = "";
									passwordStr2 = "";
									// passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");

									dialog.dismiss();
									return;
								}
							});
			dialog = ReSuccess.create();
			break;
		case RegistrationFailureDialog:
			CustomDialog.Builder ReFailure = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			ReFailure
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message16))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									passwordStr1 = "";
									passwordStr2 = "";
									passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = ReFailure.create();
			break;
		case ResetSuccessDialog:
			CustomDialog.Builder ResetSuccess = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			ResetSuccess
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message17)
									+ passwordStr
									+ getResources().getString(
											R.string.alert_message18))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									Records.saveIntRecord(Reg,
											AdPlatform.Rms_JiZhuPassword, 0);
									AdPlatform.password = passwordStr;
									AdPlatform.phone = numberStr;
									// Common.ChangeActivity(Reg,
									// Registration.this, Land.class);
									new LandTask().execute("");
									passwordStr1 = "";
									passwordStr2 = "";
									// passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = ResetSuccess.create();
			break;
		case ResetNotSuccessDialog:
			CustomDialog.Builder ResetNotSuccess = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			ResetNotSuccess
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("您还没有注册，请先注册！")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									Common.ChangeActivity(Reg,
											Registration.this, Land.class);
									passwordStr1 = "";
									passwordStr2 = "";
									passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = ResetNotSuccess.create();
			break;
		case ResetFailureDialog:
			CustomDialog.Builder ResetFailure = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			ResetFailure
					.setTitle(getResources().getString(R.string.alert))
					.setMessage(
							getResources().getString(R.string.alert_message19))
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									passwordStr1 = "";
									passwordStr2 = "";
									passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = ResetFailure.create();
			break;
		case HasPermission:
			CustomDialog.Builder HasPermission = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			HasPermission
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("您禁用了程序发送短信的权限，操作失败！")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									passwordStr1 = "";
									passwordStr2 = "";
									passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = HasPermission.create();
			break;
		case CanNotNull:
			CustomDialog.Builder CannotNull = new CustomDialog.Builder(
					Registration.this, R.layout.alertdialog);
			CannotNull
					.setTitle(getResources().getString(R.string.alert))
					.setMessage("手机号或密码不能为空！")
					.setPositiveButton(getResources().getString(R.string.ok),
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int which) {
									passwordStr1 = "";
									passwordStr2 = "";
									passwordStr = "";
									password1_info.setText("");
									password2_info.setText("");
									dialog.dismiss();
									return;
								}
							});
			dialog = CannotNull.create();
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

	private void RegistrationDonotSuccessDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message12));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						// 切换Activity 主界面
						Common.ChangeActivity(Reg, Registration.this,
								Land.class);
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框

	}

	private void RegistrationSuccessDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message13)
				+ numberStr
				+ getResources().getString(R.string.alert_message14)
				+ passwordStr
				+ getResources().getString(R.string.alert_message15));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						// 切换Activity 主界面
						Common.ChangeActivity(Reg, Registration.this,
								Land.class);
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	private void RegistrationFailureDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message16));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
						return;
					}
				});
		alertDialog.setPositiveButton(
				getResources().getString(R.string.cancel),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	private void ResetSuccessDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message17)
				+ passwordStr
				+ getResources().getString(R.string.alert_message18));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						// 切换Activity 主界面
						Common.ChangeActivity(Reg, Registration.this,
								Land.class);
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	private void ResetFailureDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message19));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
						return;
					}
				});
		alertDialog.setPositiveButton(
				getResources().getString(R.string.cancel),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						// 切换Activity 主界面
						Common.ChangeActivity(Reg, Registration.this,
								Land.class);
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	private void PhoneFailureDialog(Activity activity) {
		AlertDialog.Builder alertDialog = new AlertDialog.Builder(activity);
		alertDialog.setTitle(getResources().getString(R.string.alert));
		alertDialog.setMessage(getResources().getString(
				R.string.alert_message20));
		alertDialog.setPositiveButton(getResources().getString(R.string.ok),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
						return;
					}
				});
		alertDialog.setPositiveButton(
				getResources().getString(R.string.cancel),
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						// 切换Activity 主界面
						Common.ChangeActivity(Reg, Registration.this,
								Land.class);
						passwordStr1 = "";
						passwordStr2 = "";
						passwordStr = "";
						password1_info.setText("");
						password2_info.setText("");
						return;
					}
				});
		alertDialog.create(); // 创建对话框
		alertDialog.show(); // 显示对话框
	}

	// 是否为电信手机号码
	public static boolean IsDianxinNumber(String num) {
		boolean re = false;
		if (num.length() == 11) {
			if (num.startsWith("133")) {
				re = true;
			} else if (num.startsWith("153")) {
				re = true;
			} else if (num.startsWith("189")) {
				re = true;
			} else if (num.startsWith("180")) {
				re = true;
			}
		}
		return re;
	}

	// 是否为联通手机号码
	public static boolean IsLiantongNumber(String num) {
		boolean re = false;
		if (num.length() == 11) {
			if (num.startsWith("130")) {
				re = true;
			} else if (num.startsWith("131")) {
				re = true;
			} else if (num.startsWith("132")) {
				re = true;
			} else if (num.startsWith("155")) {
				re = true;
			} else if (num.startsWith("156")) {
				re = true;
			} else if (num.startsWith("186")) {
				re = true;
			} else if (num.startsWith("185")) {
				re = true;
			}
		}
		return re;
	}

	// 是否为移动手机号码
	public static boolean IsYidongNumber(String num) {
		boolean re = false;
		if (num.length() == 11) {
			if (num.startsWith("139")) {
				re = true;
			} else if (num.startsWith("138")) {
				re = true;
			} else if (num.startsWith("137")) {
				re = true;
			} else if (num.startsWith("136")) {
				re = true;
			} else if (num.startsWith("135")) {
				re = true;
			} else if (num.startsWith("134")) {
				re = true;
			} else if (num.startsWith("159")) {
				re = true;
			} else if (num.startsWith("158")) {
				re = true;
			} else if (num.startsWith("157")) {
				re = true;
			} else if (num.startsWith("152")) {
				re = true;
			} else if (num.startsWith("151")) {
				re = true;
			} else if (num.startsWith("150")) {
				re = true;
			} else if (num.startsWith("188")) {
				re = true;
			} else if (num.startsWith("187")) {
				re = true;
			} else if (num.startsWith("147")) {
				re = true;
			} else if (num.startsWith("182")) {
				re = true;
			}
		}
		return re;
	}

	public static int getIsReset() {
		return isReset;
	}

	public static void setIsReset(int reset) {
		isReset = reset;
	}

	/**
	 * 按键按下
	 * 
	 * @return null
	 */
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			Common.ChangeActivity(Reg, Registration.this, Land.class);
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
				Log.v("login_url1111111111", login_url);
				Log.v("222222222222222222222", "");
				Log.v("passwordStr11111111111", "aaaaaaaaa" + passwordStr);
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
					Common.ChangeActivity(Reg, Registration.this, AdP.class);
					// Land.finish();
					// 传递登陆结果 确定 主界面状态
					AdP.setLogin(land_results);
					AdPlatform.mainState = 0;
				} else {
					// if (land_results.equals("-2")) {
					// showDialog(Password_wrong);
					// } else {
					// if (land_results.equals("2")) {
					// showDialog(Change_phone);
					// } else {
					// if (land_results.equals("-1")) {
					// showDialog(UnRegistration);
					// } else {
					// // 登陆失败 处理
					// showDialog(LandFailureDialog);
					// }
					// }
					// }
					Common.WebFailureDialog(Reg);
				}
			} catch (Exception e) {
				// MainActivity.login_results = "0";
				Common.WebFailureDialog(Reg);
				Log.v("E-Login", "" + e);
			}

		}
	}
}
