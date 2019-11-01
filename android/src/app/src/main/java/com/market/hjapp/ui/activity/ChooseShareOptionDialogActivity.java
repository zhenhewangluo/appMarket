
package com.market.hjapp.ui.activity;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.R;
import com.market.hjapp.UserInfo;
import com.market.hjapp.ui.view.LongButton;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
@SuppressWarnings("unused")
public class ChooseShareOptionDialogActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		
		setContentView(R.layout.choose_share_option_dialog);
		
		
		Intent intent = getIntent();
//		final String appId = intent.getStringExtra("appid");
//		final String appName = intent.getStringExtra("appName");
		
		setTitle(getString(R.string.dialog_title_choose_share_option));
		LongButton smsBtn = (LongButton) findViewById(R.id.share_via_sms);
		smsBtn.setText(R.string.share_via_sms);
		smsBtn.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				Intent sendIntent = new Intent(Intent.ACTION_VIEW);
//				sendIntent.putExtra("sms_body", getString(R.string.share_via_sms_content, appName)); 
				sendIntent.setType("vnd.android-dir/mms-sms");
				startActivity(sendIntent);
				finish();
			}
			
		});
		
		LongButton mailBtn = (LongButton) findViewById(R.id.share_via_mail);
		mailBtn.setText(R.string.share_via_email);
		mailBtn.setOnClickListener(new OnClickListener() {

			@SuppressWarnings("static-access")
			@Override
			public void onClick(View v) {
				final Intent emailIntent = new Intent(Intent.ACTION_SEND);
				 
//				emailIntent .setType("plain/text");
				emailIntent.setType("message/rfc822;image/*");
				 
				UserInfo user = GeneralUtil.getUserInfo(ChooseShareOptionDialogActivity.this);
				String userName = user.getName();
				if(userName == null)
					userName = getString(R.string.share_via_email_sharer);
				
//				emailIntent .putExtra(android.content.Intent.EXTRA_SUBJECT, getString(R.string.share_via_email_subject, appName, userName));
//				emailIntent .putExtra(android.content.Intent.EXTRA_TEXT, 
//					getString(R.string.share_via_email_content, appName));
				 
				startActivity(emailIntent.createChooser(emailIntent, getString(R.string.choose_email_client)));
				finish();
			}
		});
	}
}
