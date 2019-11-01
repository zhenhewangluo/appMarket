package com.market.hjapp.ui.activity;

import java.util.HashMap;

import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.UserInfo;
import com.market.hjapp.ui.tasks.SendSuggestionTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class SuggestionActivity extends BaseActivity{

	private static final String TAG="SuggestionActivity";
	private EditText mSuggestion;
	private EditText mEmail;
	private LongButton mSubmit;
	
	 private TaskResultListener mSuggestionResultListener = new TaskResultListener() {

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
					errMsg = R.string.sendsuggestion_failed;
				}
				
				Toast.makeText(getApplicationContext(), errMsg, Toast.LENGTH_LONG).show();
			}
			else
			{
				MyLog.d(TAG, "send Succesful!!");

				Toast.makeText(getApplicationContext(), R.string.sendsuggestion_susseccful, Toast.LENGTH_LONG).show();
				finish();
			}
		}
	    	
    };
	    
	    
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		setContentView(R.layout.suggestion);
		
		saveUserLog(0);
		init();
		
		if (GeneralUtil.getHasLoggedIn(SuggestionActivity.this)) {
			 updateUserInfo();
        } 
	}
	
	
	private void init() {
		mSuggestion = (EditText) findViewById(R.id.suggestion_edit);		
		mEmail = (EditText) findViewById(R.id.email_edit);
		mSubmit = (LongButton) findViewById(R.id.submit_btn);
		mSubmit.setBackgroundResource(R.drawable.btn_long_selector);
		mSubmit.setText(R.string.submit);
		mSubmit.setOnClickListener(mSubmitBtnListener);
	}
	
	String suggestion;
	String email;
	
	OnClickListener mSubmitBtnListener = new OnClickListener() {

        @Override
        public void onClick(View arg0) {
        	
        	saveUserLog(1);
        	
        	email = mEmail.getText().toString().trim();
        	suggestion = mSuggestion.getText().toString().trim();
        	if(suggestion ==null||suggestion.equals("")){
        		 Toast.makeText(getApplicationContext(), R.string.content_null, Toast.LENGTH_LONG).show();
        	}else{
        		new SendSuggestionTask(SuggestionActivity.this,mSuggestionResultListener).execute(suggestion,email);
        	}
        }
        
    };
    
    private void updateUserInfo() {
        UserInfo user = GeneralUtil.getUserInfo(this);

        mEmail.setText(user.getEmail());
        
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
		GeneralUtil.saveUserLogType3(SuggestionActivity.this, 30, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+30, "", action);
//		}
    }
}
