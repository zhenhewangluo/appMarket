package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.tasks.LoginTask;
import com.market.hjapp.ui.tasks.ProcessInstallTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class LoginDialogActivity1 extends BaseActivity {
	private static final String TAG = "LoginDialogActivity1";

	protected static final int REQUEST_REGISTER = 101;
	/** 注册 */
	protected static final int REQUEST_FIND_PASSWD = 102;
	/** 找回密码 */
    protected static final int REQUEST_EDIT_USERINFO = 103;//设置用户名
	private LongButton mLoginBtn;
	private LongButton mFindPwdBtn;
	private LongButton mRegisterBtn;

	OnClickListener mRegisterBtnListener = new OnClickListener() {

		@Override
		public void onClick(View arg0) {
			saveUserLog(2);

			Intent i = new Intent(getApplicationContext(),
					RegisterDialogActivity.class);

			i.putExtra("page_no", 13);

			startActivityForResult(i, REQUEST_REGISTER);
		}

	};

	TaskResultListener mLoginListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> result) {

			if (success) {
				if (!GeneralUtil
						.hasEnteredUserGuidePageBefore(LoginDialogActivity1.this)) {
					MyLog.d(TAG, "!hasEnteredUserGuidePageBefore");
					switch (GeneralUtil
							.getUserGuideFunc(LoginDialogActivity1.this)) {
					case 0:
						Intent i = new Intent(getApplicationContext(),
								Lead2Activity.class);
						startActivity(i);
						finish();
						break;
					case 1:
						Intent intent1 = new Intent(getApplicationContext(),
								BrowseSuggestedAppListActivity.class);
						startActivity(intent1);
						finish();
						break;
					default:
						break;
					}
				} else {
					MyLog.d(TAG, "************ login sucess ********");
					Intent i = new Intent();
					i.putExtra("password", mPassword.getText().toString());
					setResult(success ? RESULT_OK : RESULT_CANCELED, i);
					finish();
				}
			} else {
				int errMsg;
				if (result == null) {
					errMsg = R.string.error_http_timeout;
				} else {
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

	OnClickListener mLoginBtnListener = new OnClickListener() {

		@Override
		public void onClick(View arg0) {
			if (checkInput()) {
				try {
					saveUserLog(1);

					new LoginTask(LoginDialogActivity1.this, mLoginListener)
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

			i.putExtra("page_no", 14);
			startActivityForResult(i, REQUEST_FIND_PASSWD);
		}

	};

	private EditText mUsername;
	private EditText mPassword;
	private TextView mLoginHint;

	private String loginHint;

	private int mPageNo;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		setContentView(R.layout.login_dialog1);

		loginHint = getIntent().getStringExtra("hint");

		if (loginHint == null) {
			loginHint = getString(R.string.login_hint_session_timeout);
		}

		mPageNo = getIntent().getIntExtra("page_no", 0);
		MyLog.d(TAG, "=== mPageNo ====" + mPageNo);
		saveUserLog(0);

		initViews();

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

	private void initViews() {

		mUsername = (EditText) findViewById(R.id.username_edit);
		mPassword = (EditText) findViewById(R.id.password_edit);

		if ((GeneralUtil.getUserEmail() != null)) {
			mUsername.setText(GeneralUtil.getUserEmail());
		}

		mLoginBtn = (LongButton) findViewById(R.id.login_btn);
		mLoginBtn.setText(R.string.user_login);
		mLoginBtn.setBackgroundResource(R.drawable.btn_long_selector);
		mLoginBtn.setOnClickListener(mLoginBtnListener);

		mFindPwdBtn = (LongButton) findViewById(R.id.find_pwd_btn);
		mFindPwdBtn.setText(R.string.user_findback_pwd);
		mFindPwdBtn.setBackgroundResource(R.drawable.btn_ok_selector);
		mFindPwdBtn.setOnClickListener(mFindPwdBtnListener);

		mRegisterBtn = (LongButton) findViewById(R.id.register_btn);
		mRegisterBtn.setText(R.string.register_welcome);
		mRegisterBtn.setBackgroundResource(R.drawable.btn_cancel_selector);
		mRegisterBtn.setOnClickListener(mRegisterBtnListener);

		mLoginHint = (TextView) findViewById(R.id.login_hint);

		mLoginHint.setText(loginHint);
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		switch (requestCode) {
		case REQUEST_REGISTER:
			if (resultCode == RESULT_OK) {

				setResult(RESULT_OK, data);
				finish();
			}
			break;

		case REQUEST_FIND_PASSWD:
			if (resultCode == RESULT_OK) {
			}
			break;
		case REQUEST_EDIT_USERINFO:
			if (resultCode == RESULT_OK) {
				MyLog.d(TAG, "************ login sucess ********");
				Intent i = new Intent();
				i.putExtra("password", mPassword.getText().toString());
				setResult(RESULT_OK , i);
				finish();
			}
			break;
		default:
			throw new RuntimeException("Unknown request code: " + requestCode);
		}

		super.onActivityResult(requestCode, resultCode, data);
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			saveUserLog(4);
		}

		return super.onKeyDown(keyCode, event);
	}

	private void saveUserLog(int action) {
		// save user log
		GeneralUtil.saveUserLogType3(LoginDialogActivity1.this, mPageNo, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+3, ""+mPageNo, "", action);
		// }
	}

}
