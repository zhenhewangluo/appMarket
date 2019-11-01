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
import com.market.hjapp.ui.tasks.EditUserinfoTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class EditUserInfoDialogActivity extends BaseActivity {

	public final String TAG = "EditUserInfoDiaLogActivity";
	private EditText mPhone;
	private EditText mName;

	protected TaskResultListener mResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> result) {
			int res;
			if (success) {
				res = R.string.set_userinfo_success;

				UserInfo user = GeneralUtil
						.getUserInfo(EditUserInfoDialogActivity.this);
				user.setPhone(mPhone.getText().toString());
				user.setName(mName.getText().toString());

				GeneralUtil.saveUserInfo(user, getApplicationContext());

				setResult(RESULT_OK);
			} else {
				if (result == null)
					res = R.string.error_http_timeout;
				else {
					String errno = (String) result.get("errno");
					if (errno.equals("E209"))
						res = R.string.error_phone_not_phone;
					else if (result.get("errno").equals("S600")){
						res = R.string.nicknameerror;
					}
					else
						res = R.string.set_userinfo_fail;
				}

				setResult(RESULT_CANCELED);
			}

			Toast.makeText(getApplicationContext(), getString(res),
					Toast.LENGTH_LONG).show();
			finish();
		}

	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,
				WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
		requestWindowFeature(Window.FEATURE_NO_TITLE);


		saveUserLog(0);

		setContentView(R.layout.edit_userinfo_dialog);
		UserInfo user = GeneralUtil.getUserInfo(this);

		mPhone = (EditText) findViewById(R.id.phone_edit);
		mName = (EditText) findViewById(R.id.name_edit);

		mName.setText(user.getName());
		mPhone.setText(user.getPhone());

		LongButton confirm = (LongButton) findViewById(R.id.confirm_btn);
		confirm.setText(R.string.upload_user_info_Btn);
		confirm.setBackgroundResource(R.drawable.btn_long_selector);
		confirm.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				if (true)// checkInput())
				{
					saveUserLog(1);

					String phone = mPhone.getText().toString().trim();
					String nickname = mName.getText().toString().trim();

					UserInfo user = GeneralUtil
							.getUserInfo(getApplicationContext());
					if (phone.equals(user.getPhone())
							&& nickname.equals(user.getName())) {
						// nothing changed
						finish();
					} else {
						try {
							new EditUserinfoTask(
									EditUserInfoDialogActivity.this,
									mResultListener).execute(phone, nickname,EditUserinfoTask.USER_BOTH);
						} catch (RejectedExecutionException e) {
							MyLog.e(TAG,
									"Got exception when execute asynctask!", e);
						}
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
