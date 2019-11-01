
package com.market.hjapp.ui.activity;

import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.TextView;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.R;
import com.market.hjapp.ui.view.LongButton;

public class AboutActivity extends BaseActivity {
    private static final String TAG = "AboutActivity";
	@Override
	protected void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    
//	    getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,  
//                WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        
	    setContentView(R.layout.about_activity);
	    
	    TextView versionTextView = (TextView)findViewById(R.id.version);
	    versionTextView.setText(getString(R.string.version_id, ConstantValues.CLIENT_VERSION_NAME));
	    
	    LongButton btn = (LongButton) findViewById(R.id.btn);
	    btn.setText(R.string.confirm);
	    btn.setBackgroundResource(R.drawable.btn_long_selector);
	    btn.setOnClickListener(new OnClickListener() {

			@Override
            public void onClick(View v) {
				finish();
            }
	    	
	    });
	    
	    saveUserLog(0);
	}
	
	private void saveUserLog(int action)
    {
    	// save user log		  	
		GeneralUtil.saveUserLogType3(this, 29, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+29, "", action);
//		}
    }

}
