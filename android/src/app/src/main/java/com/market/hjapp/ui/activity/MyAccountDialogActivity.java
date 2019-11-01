
package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.View.OnClickListener;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.UserInfo;
import com.market.hjapp.ui.tasks.LogoutTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

public class MyAccountDialogActivity extends Activity {
	
	public static final String TAG = "MyAccountDialogActivity";
	
    protected static final int REQUEST_EDIT_USERINFO = 101;
    protected static final int REQUEST_CHARGE = 102;
    protected static final int REQUEST_CHANGE_PASSWORD = 103;
    protected static final int REQUEST_INVITE = 104;

    private TextView mUsername;
    private TextView mPhone;
    private TextView mNickname;
//    private TextView mJifen;
//    private TextView mRenqi;
//    private TextView mPaiming;
    
    TaskResultListener mLogoutResultListener = new TaskResultListener() {

        @Override
        public void onTaskResult(boolean success, HashMap<String, Object> res) {
            if (success) {
                // save anonymous uid/sid
                GeneralUtil.saveLoginInfo(MyAccountDialogActivity.this, null, 
                        (String) res.get("sid"));
                
//                Intent i = new Intent(MyAccountDialogActivity.this, LoginAccountActivity.class);
                
//                startActivity(i);
                
                GeneralUtil.saveLoggedOut(MyAccountDialogActivity.this);
                
                finish();
            }
            else
            {
            	if (res == null)
            	{
            		Toast.makeText(MyAccountDialogActivity.this, R.string.error_http_timeout, Toast.LENGTH_LONG).show();
            	}
            }
        }
        
    };

    private OnClickListener mLogoutButtonListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	try
        	{
        		new LogoutTask(MyAccountDialogActivity.this, mLogoutResultListener).execute();
        	} catch (RejectedExecutionException e) {
            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
            }
        }
        
    };
    
    private OnClickListener mEditButtonListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	
            Intent i = new Intent(getApplicationContext(), EditUserInfoDialogActivity.class);
            
            startActivityForResult(i, REQUEST_EDIT_USERINFO);
        }
        
    };
    
    private OnClickListener mChangePasswordButtonListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
            
        	Intent i = new Intent(MyAccountDialogActivity.this, ChangePasswordDialogActivity.class);
            startActivityForResult(i, REQUEST_CHANGE_PASSWORD);
        }
        
    };
    

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND,  
                WindowManager.LayoutParams.FLAG_BLUR_BEHIND);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        MyLog.e("TAG", "MyAccountDialogActivity run.");
        setContentView(R.layout.my_account_dialog_activity);
        
        mNickname = (TextView)findViewById(R.id.usernickname);
        mUsername = (TextView)findViewById(R.id.username);
        mPhone = (TextView)findViewById(R.id.phone);
//        mJifen = (TextView)findViewById(R.id.jifentext);
//        mRenqi = (TextView)findViewById(R.id.renqitext);
//        mPaiming = (TextView)findViewById(R.id.paimingtext);
        
        LongButton editBtn = (LongButton)findViewById(R.id.edit_btn);
        editBtn.setText(R.string.user_info_editinfo);
        editBtn.setBackgroundResource(R.drawable.btn_ok_selector);
        editBtn.setOnClickListener(mEditButtonListener);
        
        
        LongButton changePwdBtn = (LongButton)findViewById(R.id.change_pwd_btn);
        changePwdBtn.setText(R.string.user_info_changepwd);
        changePwdBtn.setOnClickListener(mChangePasswordButtonListener);

        LongButton logoutBtn = (LongButton)findViewById(R.id.logout_btn);
        logoutBtn.setText(R.string.user_info__logout);
        logoutBtn.setOnClickListener(mLogoutButtonListener);
        
        
        updateUserInfo();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (requestCode == REQUEST_EDIT_USERINFO) {
            if (resultCode == RESULT_OK) {
                updateUserInfo();
            }
        }
        else if (requestCode == REQUEST_CHARGE) {
        	
        	if (resultCode == RESULT_OK) {
        		updateUserInfo();
            }
        	
        }
        else if (requestCode == REQUEST_INVITE){
        	
        }
        
        super.onActivityResult(requestCode, resultCode, data);
    }

    private void updateUserInfo() {
        UserInfo user = GeneralUtil.getUserInfo(this);

        mUsername.setText(getString(R.string.user_info_name, user.getEmail()));
        
       
        
        mNickname.setText(getString(R.string.user_info_nickname,user.getName()));
        
        String userPhone = user.getPhone();
        if (!TextUtils.isEmpty(userPhone)) {
            userPhone = getString(R.string.not_set);
        }
        String s = "null".equals(user.getPhone()) ? getString(R.string.not_set) : user.getPhone();

        mPhone.setText(getString(R.string.user_info_phone, s));
    }
    
}