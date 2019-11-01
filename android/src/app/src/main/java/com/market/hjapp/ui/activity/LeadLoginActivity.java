package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.tasks.LoginTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.Toast;

public class LeadLoginActivity extends BaseActivity {
	private static final String TAG ="LeadLoginActivity";
	
	private EditText mUserName;
	private EditText mPassword;
	private LongButton mLogin;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,  
                WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.login_lead);
		
		// save user log
		saveUserLog(0);
		
		init();
	}

	private void init() {
		mUserName = (EditText) findViewById(R.id.username_edit);
		mPassword = (EditText) findViewById(R.id.password_edit);
		mLogin = (LongButton) findViewById(R.id.confirm_btn);
		mLogin.setBackgroundResource(R.drawable.btn_long_selector);
		mLogin.setText(R.string.user_login);
		mLogin.setOnClickListener(mLoginBtnListener);
	}
	
	OnClickListener mLoginBtnListener = new OnClickListener() {

        @Override
        public void onClick(View arg0) {
        	if (checkInput()) {
            	try
            	{
            		// save user log
            		saveUserLog(1);
        			
            		new LoginTask(LeadLoginActivity.this, mLoginListener)
                    	.execute(mUserName.getText().toString().trim().toLowerCase(), mPassword.getText().toString().trim());
            	} catch (RejectedExecutionException e) {
                	MyLog.e(TAG, "Got exception when execute asynctask!", e);
                }
            }
        }
	        
    };
	    
    TaskResultListener mLoginListener = new TaskResultListener() {

        @Override
        public void onTaskResult(boolean success, HashMap<String, Object> result) {
        	
        	if (success)
        	{
      
            	Intent i = new Intent();
               //	i.putExtra("password", mPassword.getText().toString());
                setResult(RESULT_OK, i);
                finish();
    	
        	}else
    		{
        		int errMsg;
        		if(result == null)
        		{
        			errMsg = R.string.error_http_timeout;
        		}
        		else
        		{
        			String error = (String)result.get("errno");
    				
                    if (error.equals("E130")) {
                    	errMsg = R.string.error_login_wrong_name;
                    }
                    else if (error.equals("E131")) {
                    	errMsg = R.string.error_login_wrong_pwd;
                    }
                    else
                    	errMsg = R.string.login_failed;
        		}
        		
				Toast.makeText(getApplicationContext(), errMsg, Toast.LENGTH_LONG).show();
        	}
        	
        }
    };

    protected boolean checkInput() {
    	// for email, we translate input to lower case auto.
    	String name = mUserName.getText().toString().trim().toLowerCase();
    	String pwd = mPassword.getText().toString().trim();

    	int errmsg = -1;
    	
    	if (name == null || name.equals(""))
    	{
    		errmsg = R.string.error_null_name;
    	}
    	else if (pwd == null || pwd.equals(""))
    	{
    		errmsg = R.string.error_null_pwd;
    	}else if(pwd.trim().length()<6){
			errmsg = R.string.error_short_pwd;
    	}
    	else if (!GeneralUtil.checkEmail(name))
    	{
    		errmsg = R.string.error_name_not_email;
    	}
    	
    	if (errmsg == -1)
    		return true;
    	else
    	{
    		Toast.makeText(getApplicationContext(), errmsg, Toast.LENGTH_LONG).show();
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

    private void saveUserLog(int action)
    {
    	// save user log
//		GeneralUtil.saveUserLogType2(LeadLoginActivity.this, 3, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+2, ""+3, "", action);
//		}
    }
}
