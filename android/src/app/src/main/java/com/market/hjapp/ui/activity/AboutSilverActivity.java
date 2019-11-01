package com.market.hjapp.ui.activity;

import com.market.hjapp.R;
import com.market.hjapp.ui.view.LongButton;

import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.view.View.OnClickListener;

public class AboutSilverActivity extends BaseActivity {
	private LongButton mGetit;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
	
		super.onCreate(savedInstanceState);
		 requestWindowFeature(Window.FEATURE_NO_TITLE);
		 setContentView(R.layout.aboutsilver);
		mGetit = (LongButton) findViewById(R.id.get_it);
		mGetit.setText(R.string.get_it);
		mGetit.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				finish();
			}
		});
	}
}
