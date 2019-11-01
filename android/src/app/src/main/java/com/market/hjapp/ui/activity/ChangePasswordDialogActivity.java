
package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.Context;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.view.View.OnKeyListener;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.tasks.ChangePasswordTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class ChangePasswordDialogActivity extends BaseActivity {
    private static final String TAG = "ChangePasswordDialogActivity";
    
    private TaskResultListener mChangePasswordTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {
			
			if (!success) {
				int errMsg;
				
				if (res == null)
				{
					errMsg = R.string.error_http_timeout;
				}
				else
				{
					String error = (String)res.get("errno");
					
	                if (error.equals("E131")) {
				    	errMsg = R.string.error_change_pwd_wrong_old;
				    }
	                else
	                	errMsg = R.string.change_pwd_failed;
				}
                
				Toast.makeText(getApplicationContext(), errMsg, Toast.LENGTH_LONG).show();
			}
			else
			{	
				Toast.makeText(getApplicationContext(), R.string.change_pwd_successful, Toast.LENGTH_LONG).show();
				
				setResult(RESULT_OK);
				finish();
			}
		}
    	
    };
    
    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,  
                WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        
        setContentView(R.layout.change_password);
        
        saveUserLog(0);
        
        initViews();       
        
    }
    
    
    private EditText mOldPassword;
    private EditText mNewPassword;
    private EditText mNewPasswordConfirm;
    
    private LongButton mChangePwdBtn;
    
    
    private void initViews() {
    	mOldPassword = (EditText)findViewById(R.id.change_pwd_old_pwd_edit);
    	mNewPassword = (EditText)findViewById(R.id.change_pwd_new_pwd_edit);
    	mNewPasswordConfirm = (EditText)findViewById(R.id.change_pwd_new_pwd_confirm_edit);

    	mNewPasswordConfirm.setOnKeyListener(onKey);   
        
    	mChangePwdBtn = (LongButton)findViewById(R.id.change_password_btn);
    	mChangePwdBtn.setText(R.string.change_pwd);
    	mChangePwdBtn.setBackgroundResource(R.drawable.btn_long_selector);
    	mChangePwdBtn.setOnClickListener(mChangePwdBtnListener);
    	
    }
    OnKeyListener onKey=new OnKeyListener() {		

		@Override

		public boolean onKey(View v, int keyCode, KeyEvent event) {

			// TODO Auto-generated method stub

			if(keyCode == KeyEvent.KEYCODE_ENTER){

				  InputMethodManager imm = (InputMethodManager)v.getContext().getSystemService(Context.INPUT_METHOD_SERVICE);

					  if(imm.isActive()){

					  imm.hideSoftInputFromWindow(v.getApplicationWindowToken(), 0 );

				  }

			   return true;

			  }

			  return false;

		}

	};

    OnClickListener mChangePwdBtnListener = new OnClickListener() {

        @Override
        public void onClick(View arg0) {
        	
        	if (checkInput())
        	{
        		saveUserLog(1);
        		
        		try
        		{
        			new ChangePasswordTask(ChangePasswordDialogActivity.this, mChangePasswordTaskResultListener)
        			.execute(mOldPassword.getText().toString().trim(), mNewPassword.getText().toString().trim());
        		} catch (RejectedExecutionException e) {
                	MyLog.e(TAG, "Got exception when execute asynctask!", e);
                }
        	}
        }
        
    };
    
    protected boolean checkInput() {
    	String oldPasswd = mOldPassword.getText().toString().trim();
	    String newPasswd = mNewPassword.getText().toString().trim();
	    String newPasswdConfirm = mNewPasswordConfirm.getText().toString().trim();

    	int errmsg = -1;
    	
    	if (oldPasswd == null || oldPasswd.equals("") ||
    		newPasswd == null|| newPasswd.equals("") ||
    		newPasswdConfirm == null|| newPasswdConfirm.equals(""))
    	{
    		errmsg = R.string.error_null_pwd;
    	}else if(newPasswd.trim().length()<6){
			errmsg = R.string.error_short_pwd;
    	}
    	else if (!newPasswdConfirm.equals(newPasswd))
    	{
    		errmsg = R.string.error_pwd_not_match;
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
		GeneralUtil.saveUserLogType3(getApplicationContext(), 24, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+24, "", action);
//		} 
    }

}
