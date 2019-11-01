package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.Toast;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.tasks.LoginTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

/**
 *  个人中心登陆
 * @author Administrator
 *
 */
public class LoginAccountActivity extends BaseBottomTabActivity {

	public static final String TAG = "LoginAccountActivity";

	protected static final int REQUEST_REGISTER = 101;// 注册
	protected static final int REQUEST_FIND_PASSWD = 102;// 找回密码
    protected static final int REQUEST_EDIT_USERINFO = 103;//设置用户名
	private LongButton mLoginBtn;
	/** 登录 */
	private LongButton mFindPwdBtn;
	/** 找回密码 */
	private LongButton mRegisterBtn;
	/** 注册 */

	OnClickListener mRegisterBtnListener = new OnClickListener() {

		@Override
		public void onClick(View arg0) {

			saveUserLog(2);

			Intent i = new Intent(getApplicationContext(),
					RegisterDialogActivity.class);
			i.putExtra("page_no", 35);
			startActivityForResult(i, REQUEST_REGISTER);
		}

	};
	protected TaskResultListener mLoginListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> result) {
			if (success) {
				gotoUserInfoPage();
			} else {
				int errMsg;
				if (result == null)
					errMsg = R.string.error_http_timeout;
				else {
					String error = (String) result.get("errno");
					if (error.equals("E130")) {
						errMsg = R.string.error_login_wrong_name;
					} else if (error.equals("E131")) {
						errMsg = R.string.error_login_wrong_pwd;
					} else if (error.equals("E405")) {//设置用户名
						errMsg = R.string.error_login_need_name;
					}else
						errMsg = R.string.login_failed;
				}

				Toast.makeText(getApplicationContext(), errMsg,
						Toast.LENGTH_LONG).show();
				
				 if (errMsg == R.string.error_login_need_name) {//设置用户名
			            Intent i = new Intent(getApplicationContext(), EditNickNameDialogActivity.class);
			            i.putExtra("username", mUsername.getText().toString().trim().toLowerCase());
			            i.putExtra("password", mPassword.getText().toString().trim());
			            startActivityForResult(i, REQUEST_EDIT_USERINFO);
				 }
				
			}
		}

	};

	private void gotoUserInfoPage() {
		Intent i = new Intent(getApplicationContext(), MyAccountActivity.class);
		startActivity(i);
		finish();
	}

	OnClickListener mLoginBtnListener = new OnClickListener() {

		@Override
		public void onClick(View arg0) {
			if (checkInput()) {
				try {
					saveUserLog(1);

					new LoginTask(LoginAccountActivity.this, mLoginListener)
							.execute(mUsername.getText().toString().trim()
									.toLowerCase(), mPassword.getText()
									.toString().trim());
				} catch (RejectedExecutionException e) {
					MyLog.e(TAG, "Got exception when execute asynctask!", e);
				}
			}
		}

	};

	OnClickListener mFindPwdBtnListener = new OnClickListener() {

		@Override
		public void onClick(View arg0) {

			saveUserLog(3);

			Intent i = new Intent(getApplicationContext(),
					FindPasswordDialogActivity.class);
			startActivityForResult(i, REQUEST_FIND_PASSWD);
		}

	};

	private EditText mUsername;

	private EditText mPassword;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		MyLog.e("TAG", "LoginAccountActivity run.");
		saveUserLog(0);

		setSelectedFooterTab(6);
		init();
	}

	protected boolean checkInput() {
		// for email, we translate input to lower case auto.
		String name = mUsername.getText().toString().trim().toLowerCase();
		String pwd = mPassword.getText().toString().trim();  

		int errmsg = -1;

		if (name == null || name.equals("")) {
			errmsg = R.string.error_null_name;
		} else if (pwd == null || pwd.equals("")) {
			errmsg = R.string.error_null_pwd;
		}else if(pwd.trim().length()<6){
			errmsg = R.string.error_short_pwd;
		} else if(name.indexOf("@")!=-1){ 
			if (!GeneralUtil.checkEmail(name)) {
				errmsg = R.string.error_name_not_email;
			}
		}else if (!GeneralUtil.checkPhone(name)) {
			errmsg = R.string.error_phone_not_phone;
		}

		if (errmsg == -1)
			return true;
		else {
			Toast.makeText(getApplicationContext(), errmsg, Toast.LENGTH_LONG)
					.show();
			return false;
		}

	}

	private void init() {
		mUsername = (EditText) findViewById(R.id.username_edit);
		mPassword = (EditText) findViewById(R.id.password_edit);

		mLoginBtn = (LongButton) findViewById(R.id.login_btn);
		mLoginBtn.setText(R.string.user_login);
		mLoginBtn.setBackgroundResource(R.drawable.btn_long_selector);
		mLoginBtn.setOnClickListener(mLoginBtnListener);

		mFindPwdBtn = (LongButton) findViewById(R.id.find_pwd_btn);
		mFindPwdBtn.setText(R.string.user_findback_pwd);
		mFindPwdBtn.setBackgroundResource(R.drawable.btn_ok_selector);
		mFindPwdBtn.setOnClickListener(mFindPwdBtnListener);

		mRegisterBtn = (LongButton) findViewById(R.id.register_btn);
		mRegisterBtn.setText(R.string.zhucezhanghu);
		mRegisterBtn.setBackgroundResource(R.drawable.btn_ok_selector);
		mRegisterBtn.setOnClickListener(mRegisterBtnListener);

		if (GeneralUtil.getUserEmail() != null) {
			mUsername.setText(GeneralUtil.getUserEmail());
		}
	}

	@Override
	protected int getLayout() {
		return R.layout.login_account_activity;
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (requestCode == REQUEST_REGISTER) {
			if (resultCode == RESULT_OK) {
				gotoUserInfoPage();
			}
		} else if (requestCode == REQUEST_FIND_PASSWD) {

		}else if (requestCode == REQUEST_EDIT_USERINFO) {//设置用户名后返回
			if (resultCode == RESULT_OK) {
				gotoUserInfoPage();
			}
		}
		super.onActivityResult(requestCode, resultCode, data);
	}

	private void saveUserLog(int action) {
		// save user log
		GeneralUtil.saveUserLogType3(LoginAccountActivity.this, 20, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+3, ""+20, "", action);
		// }
	}

}
