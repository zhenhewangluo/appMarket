package com.market.hjapp.ui.activity;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.text.TextUtils;
import android.text.method.ScrollingMovementMethod;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.AbsoluteLayout.LayoutParams;
import android.widget.AdapterView.OnItemClickListener;

import com.market.hjapp.App;
import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.ui.tasks.AnonymousLoginTask;
import com.market.hjapp.ui.tasks.ChargeTask;
import com.market.hjapp.ui.tasks.FindPasswordTask;
import com.market.hjapp.ui.tasks.GetChargeListTask;
import com.market.hjapp.ui.tasks.PayTask;
import com.market.hjapp.ui.tasks.ProcessInstallTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.FancyProgressBar;
import com.market.hjapp.ui.view.LongButton;
import com.market.hjapp.ui.view.RatingStars;

public class FindPasswordDialogActivity extends BaseActivity {
	private static final String TAG = "FindPasswordDialogActivity";

	private LongButton mFindPassword;
	private EditText mUserEmail;

	private TaskResultListener mFindPasswordTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {
			if (!success) {
				int errMsg;

				if (res == null) {
					errMsg = R.string.error_http_timeout;
				} else {
					String error = (String) res.get("errno");

					if (error.equals("E130")) {
						errMsg = R.string.error_find_pwd_wrong_email;
					} else
						errMsg = R.string.find_pwd_failed;
				}

				Toast.makeText(getApplicationContext(), errMsg,
						Toast.LENGTH_LONG).show();
			} else {
				Toast.makeText(getApplicationContext(),
						R.string.find_pwd_successful, Toast.LENGTH_LONG).show();

				setResult(RESULT_OK);
				finish();

			}
		}

	};

	private int mPageNo;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,
				WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
		requestWindowFeature(Window.FEATURE_NO_TITLE);

		setContentView(R.layout.find_password_dialog);

		mUserEmail = (EditText) findViewById(R.id.username_edit);

		if (GeneralUtil.getUserEmail() != null) {
			mUserEmail.setText(GeneralUtil.getUserEmail());
		}

		mFindPassword = (LongButton) findViewById(R.id.find_pwd_btn);
		mFindPassword.setText(R.string.user_findback_pwd);
		mFindPassword.setBackgroundResource(R.drawable.btn_long_selector);
		mFindPassword.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				if (checkInput()) {
					saveUserLog(1);
					try {
						new FindPasswordTask(FindPasswordDialogActivity.this,
								mFindPasswordTaskResultListener)
								.execute(mUserEmail.getText().toString().trim()
										.toLowerCase());
					} catch (RejectedExecutionException e) {
						MyLog.e(TAG, "Got exception when execute asynctask!", e);
					}
				}
			}
		});

		mPageNo = getIntent().getIntExtra("page_no", 0);
		saveUserLog(0);
	}

	protected boolean checkInput() {
		// for email, we translate input to lower case auto.
		String name = mUserEmail.getText().toString().trim().toLowerCase();

		int errmsg = -1;

		if (name == null || name.equals("")) {
			errmsg = R.string.error_null_email;
		} else if (!GeneralUtil.checkEmail(name)) {
			errmsg = R.string.error_email_not_email;
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
		GeneralUtil.saveUserLogType3(FindPasswordDialogActivity.this, mPageNo,
				action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+3, ""+mPageNo, "", action);
		// }
	}

}
