package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;
import android.os.Bundle;

import com.market.hjapp.MyLog;
import com.market.hjapp.ui.tasks.AuthenticateTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;

public class AuthenticateActivity extends BaseActivity {
	private static final String TAG = "AuthenticateActivity";

	@SuppressWarnings("unchecked")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		Bundle params = getIntent().getExtras();
		int appid = Integer.parseInt(params.getString("appid"));

		try {
			new AuthenticateTask(AuthenticateActivity.this,
					mAuthenticateTaskResultListener).execute(appid + "");
		} catch (RejectedExecutionException e) {
			MyLog.e(TAG, "Got exception when execute asynctask!", e);
		}
	}

	private TaskResultListener mAuthenticateTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				setResult(RESULT_CANCELED);
			} else {
				setResult(RESULT_OK);
			}

			finish();
		}

	};

}
