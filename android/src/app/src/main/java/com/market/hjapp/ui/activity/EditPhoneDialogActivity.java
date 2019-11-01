package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.os.Bundle;
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
import com.market.hjapp.UserInfo;
import com.market.hjapp.ui.tasks.EditPhoneTask;
import com.market.hjapp.ui.tasks.EditUserinfoTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.SendVerifyTask;
import com.market.hjapp.ui.view.LongButton;

public class EditPhoneDialogActivity extends BaseActivity {

	public final String TAG = "EditUserInfoDiaLogActivity";
	private EditText mPhone;
	private EditText mValidate;

	protected TaskResultListener mResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> result) {
			int res;
			if (success) {
				res = R.string.set_userinfo_success;

				UserInfo user = GeneralUtil
						.getUserInfo(EditPhoneDialogActivity.this);
				user.setPhone(mPhone.getText().toString());

				GeneralUtil.saveUserInfo(user, getApplicationContext());

				setResult(RESULT_OK);
			} else {
				if (result == null)
					res = R.string.error_http_timeout;
				else {
					String errno = (String) result.get("errno");
					if (errno.equals("E209"))
						res = R.string.error_phone_not_phone;
					else if(errno.equals("E408"))
					{
						res = R.string.validate_error;
					}else if(errno.equals("E404"))
					{
						res = R.string.phone_already_exist;
					}
					else
						res = R.string.set_userinfo_fail;
				}

				//setResult(RESULT_CANCELED);
			}

			Toast.makeText(getApplicationContext(), getString(res),
					Toast.LENGTH_LONG).show();
			if (success)
				finish();
		}

	};
	protected TaskResultListener mResultListener_Verify = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> result) {
			int res;
			if (success) {
				res = R.string.send_validate_success;

			} else {
				if (result == null)
					res = R.string.error_http_timeout;
				else {
					String errno = (String) result.get("errno");
					if (errno.equals("E209"))
						res = R.string.error_phone_not_phone;
					else if(errno.equals("E406"))
					{
						res = R.string.send_validate_fail_frequently;
					}
					else
						res = R.string.send_validate_fail;
				}
				
			}

			Toast.makeText(getApplicationContext(), getString(res),
					Toast.LENGTH_LONG).show();
		}

	};
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,
				WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
		requestWindowFeature(Window.FEATURE_NO_TITLE);


		saveUserLog(0);

		setContentView(R.layout.edit_phone_dialog);
		UserInfo user = GeneralUtil.getUserInfo(this);

		mPhone = (EditText) findViewById(R.id.phone_edit);
		mValidate = (EditText) findViewById(R.id.validate_code);

//		mName.setText(user.getName());
//		mPhone.setText(user.getPhone());

		LongButton confirm = (LongButton) findViewById(R.id.confirm_btn);
		confirm.setText(R.string.upload_user_info_Btn);
		confirm.setBackgroundResource(R.drawable.btn_long_selector);
		confirm.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				if (checkInput())
				{
					saveUserLog(1);

					String phone = mPhone.getText().toString().trim();
					String Validate = mValidate.getText().toString().trim();
					if(Validate.equals(""))
					{
						Toast.makeText(getApplicationContext(), getString(R.string.send_validate_null),
								Toast.LENGTH_LONG).show();
						return;
					}
						try {
							new EditPhoneTask(
									EditPhoneDialogActivity.this,
									mResultListener).execute(phone, Validate);
						} catch (RejectedExecutionException e) {
							MyLog.e(TAG,
									"Got exception when execute asynctask!", e);
						}
	
				}
			}

		});

		LongButton send = (LongButton) findViewById(R.id.send_validate_btn);
		send.setText(R.string.send_validate_Btn);
		send.setBackgroundResource(R.drawable.btn_long_selector);
		send.setOnClickListener(new OnClickListener() {	
			@Override
			public void onClick(View v) {
				if (checkInput())
				{
					//saveUserLog(1);

					String phone = mPhone.getText().toString().trim();
						try {
							new SendVerifyTask(
									EditPhoneDialogActivity.this,
									mResultListener_Verify).execute(phone);
						} catch (RejectedExecutionException e) {
							MyLog.e(TAG,
									"Got exception when execute asynctask!", e);
						}
	
				}
			}

		});
		
	}

	protected boolean checkInput() {
		String phone = mPhone.getText().toString();

		int errmsg = -1;

		if (!GeneralUtil.checkPhone(phone)) {
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

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			saveUserLog(2);
		}

		return super.onKeyDown(keyCode, event);
	}

	private void saveUserLog(int action) {
		// save user log
		GeneralUtil.saveUserLogType3(getApplicationContext(), 22, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+3, ""+22, "", action);
		// }
	}

}
