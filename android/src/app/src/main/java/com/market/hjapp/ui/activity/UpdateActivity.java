
package com.market.hjapp.ui.activity;

import java.io.File;

import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.TextView;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.service.download.FileManager;
import com.market.hjapp.ui.view.LongButton;

public class UpdateActivity extends BaseActivity {

	private static final String TAG = "UpdateActivity";
	
	private boolean haveFile = false; 
	
	private int appid;
	private String url;
	
	private String filename;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    
	    saveUserLog(0);
	    
	    Bundle params = getIntent().getExtras();
	    String version = params.getString("version");
	    String changelog = params.getString("changelog");
	    appid = params.getInt("appid");
	    url = params.getString("url");
	    
	    setContentView(R.layout.update_activity);
	    
	    TextView versionTextView = (TextView)findViewById(R.id.version);
	    versionTextView.setText(getString(R.string.version_id, version));
	    
	    TextView changeLogTextView = (TextView)findViewById(R.id.change_log);
	    changeLogTextView.setText(changelog);
	    
	    filename = DatabaseUtils.getInstallingAppLocalPath(UpdateActivity.this, appid);
	    
	    MyLog.d(TAG, "filename " + filename);
	    
	    File apkFile = new File(filename);
        if (apkFile.exists()) {
        	haveFile = true;
        }
        else
        	haveFile = false;
	     
	    LongButton btn = (LongButton) findViewById(R.id.btn);
	    btn.setText(haveFile ? R.string.update_install : R.string.update_download);
	    btn.setBackgroundResource(R.drawable.btn_long_selector);
	    btn.setOnClickListener(new OnClickListener() {

			@Override
            public void onClick(View v) {
				
				saveUserLog(1);
				
				if (haveFile)
				{
					installMarket();
				}
				else
				{
					Intent intent = new Intent(getApplicationContext(), AppService.class);
		            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_DOWNLOAD);
		            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, ConstantValues.CLIENT_DBID);
		            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_ID, "");
		            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_URL, url);
		            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPID, appid);
		            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPNAME, getString(R.string.app_name));
		            
		            startService(intent);
		            
//					downloadApp(ConstantValues.CLIENT_DBID, url, appid + ".apk", null, 
//				            getResources().getString(R.string.app_name), appid);
				}
				
				finish();
            }
	    	
	    });
	}
	
	private void installMarket()
	{
		//  Start installing manual
        try {
            FileManager.install(filename, UpdateActivity.this);
        } catch (Exception e) {
            MyLog.e(TAG, "Failed to install the file " + filename);
        }
	}
	
	@Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
        	saveUserLog(2);
        }

        return super.onKeyDown(keyCode, event);
    }
	
	private void saveUserLog(int action)
    {
    	// save user log
		GeneralUtil.saveUserLogType3(UpdateActivity.this, 38, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+38, "", action);
//		}
    }
	
}
