package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.Context;
import android.view.inputmethod.InputMethodManager;
import android.view.View.OnKeyListener; 
import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.tasks.RegisterTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class RegisterDialogActivity extends BaseActivity {
	public static final String TAG = "RegisterDialogActivity";
    protected static final int REQUEST_EDIT_USERINFO = 103;//设置用户名
    
	private LongButton mConfirmBtn;
	protected TaskResultListener mTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> result) {
			int res;
			if (success) {
				Toast.makeText(getApplicationContext(),
						R.string.register_success, Toast.LENGTH_LONG).show();

				Intent i = new Intent();
				i.putExtra("password", mPassword.getText().toString());
				setResult(RESULT_OK, i);
				finish();

			} else {

				if (result == null)
					res = R.string.error_http_timeout;
				else if (result.get("errno").equals("E198")){
					res = R.string.error_name_not_email;
				}
				else if (result.get("errno").equals("S600")){
					res = R.string.nicknameerror;
				}else if (result.get("errno").equals("E405")) {//设置用户名
					res = R.string.error_login_need_name;	
				}else
					res = R.string.register_fail;


				 if (res == R.string.error_login_need_name) {//设置用户名
						Toast.makeText(getApplicationContext(),
								R.string.register_success, Toast.LENGTH_LONG).show();
			            Intent i = new Intent(getApplicationContext(), EditNickNameDialogActivity.class);
			            i.putExtra("username", mUsername.getText().toString().trim().toLowerCase());
			            i.putExtra("password", mPassword.getText().toString().trim());
			            startActivityForResult(i, REQUEST_EDIT_USERINFO);	
				 }
				 else {
						Toast.makeText(getApplicationContext(), res, Toast.LENGTH_LONG)
						.show();		
				}
			}
		}

	};

	private OnClickListener mRegisterBtnListener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			if (checkInput()) {
				try {
					saveUserLog(1);

					new RegisterTask(RegisterDialogActivity.this,
							mTaskResultListener).execute(mUsername.getText()
							.toString().trim().toLowerCase(), mPassword
							.getText().toString().trim(),"" /*mNickname.getText()
							.toString().trim()*/);
				} catch (Exception e) {
					// MainActivity.login_results = "0";
				}

			}
		}
	};
	private EditText mUsername;
//	private EditText mNickname;
	private EditText mPassword;
	private EditText mRepeatPassword;

	protected boolean checkInput() {
		// for email, we translate input to lower case auto.
		String name = mUsername.getText().toString().trim().toLowerCase();
		String pwd = mPassword.getText().toString().trim();
		String repwd = mRepeatPassword.getText().toString().trim();
		int errmsg = -1;

		if (name == null || name.equals("")) {
			errmsg = R.string.error_null_name;
		} else if (pwd == null || pwd.equals("")) {
			errmsg = R.string.error_null_pwd;
		}else if(pwd.trim().length()<6){
			errmsg = R.string.error_short_pwd;
		}else if (!GeneralUtil.checkEmail(name)) {
			errmsg = R.string.error_name_not_email;
		} else if (!pwd.equals(repwd)) {
			errmsg = R.string.error_pwd_not_match;
		}

		if (errmsg == -1)
			return true;
		else {
			Toast.makeText(getApplicationContext(), errmsg, Toast.LENGTH_LONG)
					.show();
			return false;
		}

	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		 if (requestCode == REQUEST_EDIT_USERINFO) {//设置用户名后返回
				if (resultCode == RESULT_OK) {
					Intent i = new Intent();
					i.putExtra("password", mPassword.getText().toString());
					setResult(RESULT_OK, i);
					finish();
				}
				else {//未设置用户名
					finish();
				}
			}
		super.onActivityResult(requestCode, resultCode, data);
	}

	private int mPageNo;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,
				WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
		requestWindowFeature(Window.FEATURE_NO_TITLE);

		setContentView(R.layout.register_dialog);
		mPageNo = getIntent().getIntExtra("page_no", 0);
		saveUserLog(0);

		initViews();

	}
	OnKeyListener onKey=new OnKeyListener() {		

		@Override

		public boolean onKey(View v, int keyCode, KeyEvent event) {

			// TODO Auto-generated method stub

			if(keyCode == KeyEvent.KEYCODE_ENTER){

				  InputMethodManager imm = (InputMethodManager)v.getContext().getSystemService(Context.INPUT_METHOD_SERVICE);

					  if(imm.isActive()){

					  imm.hideSoftInputFromWindow(v.getApplicationWindowToken(), 0 );

				  }

			   return true;

			  }

			  return false;

		}

	};
	private boolean nickNameLocked = false;

	private void initViews() {// 注册界面
		mUsername = (EditText) findViewById(R.id.username_edit);
//		mNickname = (EditText) findViewById(R.id.nickname_edit);
		mPassword = (EditText) findViewById(R.id.password_edit);
		mRepeatPassword = (EditText) findViewById(R.id.repeat_password_edit);
		mRepeatPassword.setOnKeyListener(onKey);   
		mConfirmBtn = (LongButton) findViewById(R.id.confirm_btn);
		mConfirmBtn.setBackgroundResource(R.drawable.btn_long_selector);
		mConfirmBtn.setText(R.string.user_register);
		mConfirmBtn.setOnClickListener(mRegisterBtnListener);

		mUsername.addTextChangedListener(new TextWatcher() {

			@Override
			public void afterTextChanged(Editable s) {
				if (nickNameLocked)
					return;

				if (s == null)
					return;

				String username = s.toString();
				int i = username.indexOf("@");
//				if (i != -1)
//					mNickname.setText(username.substring(0, i));
//				else
//					mNickname.setText(username);
			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count,
					int after) {
			}

			@Override
			public void onTextChanged(CharSequence s, int start, int before,
					int count) {
			}

		});

//		mNickname.addTextChangedListener(new TextWatcher() {
//
//			@Override
//			public void afterTextChanged(Editable s) {
//				if (s == null)
//					return;
//				String nickname = s.toString();
//
//				String username = mUsername.getText().toString();
//				int i = username.indexOf("@");
//				if (i != -1) {
//					username = username.substring(0, i);
//				}
//
//				if (!nickname.equals(username))
//					nickNameLocked = true;
//
//			}
//
//			@Override
//			public void beforeTextChanged(CharSequence s, int start, int count,
//					int after) {
//			}
//
//			@Override
//			public void onTextChanged(CharSequence s, int start, int before,
//					int count) {
//			}
//
//		});
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			// save user log
			saveUserLog(2);
		}

		return super.onKeyDown(keyCode, event);
	}

	private void saveUserLog(int action) {
		if (mPageNo == 2) {
			// save user log
			// GeneralUtil.saveUserLogType2(RegisterDialogActivity.this,
			// mPageNo, action);
			// if (action==0) {
			// tracker.trackPageView("/"+TAG);
			// }
			// else {
			// tracker.trackEvent(""+2, ""+mPageNo, "", action);
			// }
		}
	}
}
