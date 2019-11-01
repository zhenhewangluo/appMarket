package com.market.hjapp.ui.activity;

import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.R;
import com.market.hjapp.ui.view.LongButton;

/**
 * 欢迎指引页 提示必备软件 立即注册 跳过等字样
 * 
 * @author Administrator
 * 
 */
public class LeadActivity extends BaseActivity {
	public final String TAG = "LeadActivity";
	private Button mRegister;
	// private LongButton mLogin;
	private LongButton mSuggestedSoftware;
	private Button mNetPage;
	// private TextView mAbout;
	private static final int REQUEST_REGISTER = 101;
	private static final int REQUEST_LOGIN = 102;
	private static final int REQUEST_SUGGESTED_SOFTWARE = 103;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.lead);

		saveUserLog(0);

		init();
	}

	private void init() {
		mRegister = (Button) findViewById(R.id.register);
		mRegister.setText(R.string.register_welcome);
		mRegister.setBackgroundResource(R.drawable.btn_ok_selector);
		mRegister.setOnClickListener(mRegisterlistener);

		// mLogin = (LongButton) findViewById(R.id.login);
		// mLogin.setText(R.string.login_welcome);
		// mLogin.setOnClickListener(mLoginlistener);
		mSuggestedSoftware = (LongButton) findViewById(R.id.suggested_software);
		mSuggestedSoftware.setText(R.string.suggested_software);
		mSuggestedSoftware.setBackgroundResource(R.drawable.btn_long_selector);
		mSuggestedSoftware.setOnClickListener(mSuggestedSoftwarelistener);

		mNetPage = (Button) findViewById(R.id.use);
		mNetPage.setText(R.string.use_welcome);
		mNetPage.setBackgroundResource(R.drawable.btn_cancel_selector);
		mNetPage.setOnClickListener(mUselistener);
		// mAbout = (TextView) findViewById(R.id.about);
		// mAbout.setOnClickListener(new OnClickListener() {
		//
		// @Override
		// public void onClick(View v) {
		// // saveUserLog(2);
		// //
		// // Intent i = new
		// Intent(getApplicationContext(),AboutSilverActivity.class);
		// // startActivity(i);
		// }
		// });

	}

	private OnClickListener mRegisterlistener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			saveUserLog(1);

			Intent i = new Intent(getApplicationContext(),
					RegisterDialogActivity.class);
			i.putExtra("page_no", 2);
			startActivityForResult(i, REQUEST_REGISTER);
		}
	};

	private OnClickListener mSuggestedSoftwarelistener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			saveUserLog(5);

			Intent intent = new Intent(getApplicationContext(),
					BrowseSuggestedAppListActivity.class);
			Bundle bundle = new Bundle();
			bundle.putInt("suggested_software",
					ConstantValues.SUGGESTED_CATE_ID);
			intent.putExtras(bundle);
			startActivityForResult(intent, REQUEST_SUGGESTED_SOFTWARE);
			// finish();
		}
	};
	// private OnClickListener mLoginlistener = new OnClickListener() {
	//
	// @Override
	// public void onClick(View v) {
	// saveUserLog(3);
	//
	// Intent i = new Intent(getApplicationContext(), LeadLoginActivity.class);
	// startActivityForResult(i, REQUEST_LOGIN);
	// //finish();
	// }
	// };
	private OnClickListener mUselistener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			// save user log
			saveUserLog(4);

			switch (GeneralUtil.getUserGuideFunc(LeadActivity.this)) {
				case 0 :
					Intent i = new Intent(getApplicationContext(),
							Lead2Activity.class);
					startActivity(i);
					// finish();
					break;
				case 1 :
					// Intent intent1 = new Intent(getApplicationContext(),
					// BrowseSuggestedAppListActivity.class);
					Intent intent1 = new Intent(getApplicationContext(),
							RecommendActivity.class);
					startActivity(intent1);
					finish();
					break;
				default :
					break;
			}
		}
	};

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {

		if (requestCode == REQUEST_REGISTER || requestCode == REQUEST_LOGIN) {
			if (resultCode == RESULT_OK) {
				switch (GeneralUtil.getUserGuideFunc(LeadActivity.this)) {
					case 0 :
						Intent i = new Intent(getApplicationContext(),
								Lead2Activity.class);
						startActivity(i);
						// finish();
						break;
					case 1 :
						// Intent intent1 = new Intent(getApplicationContext(),
						// BrowseSuggestedAppListActivity.class);
						Intent intent1 = new Intent(getApplicationContext(),
								RecommendActivity.class);
						startActivity(intent1);
						finish();
						break;
					default :
						break;
				}
			} else {
				// finish();
			}
		}

		super.onActivityResult(requestCode, resultCode, data);
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			saveUserLog(5);
		}

		return super.onKeyDown(keyCode, event);
	}

	private void saveUserLog(int action) {
		// save user log
		// GeneralUtil.saveUserLogType2(LeadActivity.this, 1, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+2, ""+1, "", action);
		// }
	}

}
