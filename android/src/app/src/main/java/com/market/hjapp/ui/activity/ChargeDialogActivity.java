
package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.tasks.ChargeTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class ChargeDialogActivity extends BaseActivity {
    private static final String TAG = "ChargeActivity";
    
    private static final int REQUEST_CHARGE_LIST = 101;
    
    private TaskResultListener mChargeTaskResultListener = new TaskResultListener() {

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
					
	                if (error.equals("E163")) {
				    	errMsg = R.string.error_charge_no_balance;
				    }
				    else if (error.equals("E164")) {
				    	errMsg = R.string.error_charge_wrong_amount;
				    }
				    else if (error.equals("E165")) {
				    	errMsg = R.string.error_charge_wrong_type;
				    }
	                else
	                	errMsg = R.string.error_charge_failed;
				}
				
				Toast.makeText(getApplicationContext(), errMsg, Toast.LENGTH_LONG).show();
			}
			else
			{
				MyLog.d(TAG, "Charge Succesful!!");
				
				String balance = (String)res.get("balance");
				GeneralUtil.saveBalance(getApplicationContext(), balance);				
				
				Toast.makeText(getApplicationContext(), R.string.charge_successful, Toast.LENGTH_LONG).show();
				
				setResult(success ? RESULT_OK : RESULT_CANCELED);
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
        
        setContentView(R.layout.charge_dialog);
        
        saveUserLog(0);
        
        initViews();       
        
    }
    
    private TextView mChargeChannel;
    
    private EditText mCardNumber;
    private EditText mCardPassword;
    private EditText mCardAmount;
    
    private LongButton mChargeBtn;
    
    
    private void initViews() {
    	mChargeChannel = (TextView)findViewById(R.id.charge_channel);
    	
    	String chargeName = GeneralUtil.getDefaultChargeName();
    	if (chargeName != null)
    	{
    		mChargeChannel.setText(chargeName);
    	}
    	
    	mCardNumber = (EditText)findViewById(R.id.charge_card_number_edit);
    	mCardPassword = (EditText)findViewById(R.id.charge_card_pwd_edit);
    	mCardAmount = (EditText)findViewById(R.id.charge_card_amount_edit);
        
    	mChargeBtn = (LongButton)findViewById(R.id.charge_btn);
    	mChargeBtn.setText(R.string.charge);
    	mChargeBtn.setOnClickListener(mChargeBtnListener);
    	
    	findViewById(R.id.charge_list).setOnClickListener(mChargeListListener);
    }
    
    OnClickListener mChargeListListener = new OnClickListener() {
    	
    	 @Override
         public void onClick(View arg0) {
    		 
    		 saveUserLog(1);
    		 
    		 Intent i = new Intent(getApplicationContext(), ChargeListDialogActivity.class);
             startActivityForResult(i, REQUEST_CHARGE_LIST);
    	 }
    };
    
    OnClickListener mChargeBtnListener = new OnClickListener() {

        @Override
        public void onClick(View arg0) {
        	
        	if (checkInput())
        	{
        		try
        		{
        			saveUserLog(2);
        			
        			new ChargeTask(ChargeDialogActivity.this, mChargeTaskResultListener)
	            	.execute(GeneralUtil.getDefaultChargeID(), 
	            				mCardAmount.getText().toString().trim(),
	            				mCardAmount.getText().toString().trim(),
	            				mCardNumber.getText().toString().trim(),
	            				mCardPassword.getText().toString().trim());
        		} catch (RejectedExecutionException e) {
                	MyLog.e(TAG, "Got exception when execute asynctask!", e);
                }
        	}
        }
        
    };
    
    protected boolean checkInput() {
    	String cardAmount = mCardAmount.getText().toString().trim();
	    String cardNumber = mCardNumber.getText().toString().trim();
	    String cardPwd = mCardPassword.getText().toString().trim();

    	int errmsg = -1;
    	
    	if (cardNumber == null || cardNumber.equals(""))
    	{
    		errmsg = R.string.error_null_card_number;
    	}
    	else if (cardPwd == null || cardPwd.equals(""))
    	{
    		errmsg = R.string.error_null_card_pwd;
    	}
    	else if (cardAmount == null || cardAmount.equals(""))
    	{
    		errmsg = R.string.error_null_card_amount;
    	}
    	else if (GeneralUtil.getDefaultChargeID() == null)
    	{
    		errmsg = R.string.error_select_channel;
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
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
    	
    	if (requestCode == REQUEST_CHARGE_LIST) {
			if (resultCode == RESULT_OK) {
				
				String chargeName = GeneralUtil.getDefaultChargeName();
		    	if (chargeName != null)
		    	{
		    		mChargeChannel.setText(chargeName);
		    	}
			}
    	}

        super.onActivityResult(requestCode, resultCode, data);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
        	saveUserLog(3);
        }

        return super.onKeyDown(keyCode, event);
    }

    private void saveUserLog(int action)
    {
    	// save user log
		GeneralUtil.saveUserLogType3(getApplicationContext(), 23, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+23, "", action);
//		}
    }

}
