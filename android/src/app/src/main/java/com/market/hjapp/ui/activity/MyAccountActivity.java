
package com.market.hjapp.ui.activity;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.TextView;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.UserInfo;
import com.market.hjapp.ui.view.LongButton;

public class MyAccountActivity extends BaseBottomTabActivity {
	
	public static final String TAG = "MyAccountActivity";
	
    protected static final int REQUEST_EDIT_USERINFO = 101;
    protected static final int REQUEST_CHARGE = 102;
    protected static final int REQUEST_CHANGE_PASSWORD = 103;
//    protected static final int REQUEST_INVITE = 104;
    

    private TextView mUsername;
    private TextView mPhone;
//    private TextView mGold;
//    private TextView mScore;
    private TextView mNickname;
    private LongButton mEditBtn ;
//    private LongButton mEditNickBtn ;
//    private TextView mWhatisGold;
//    private TextView mWhatisScore;
    
//    TaskResultListener mLogoutResultListener = new TaskResultListener() {
//
//        @Override
//        public void onTaskResult(boolean success, HashMap<String, Object> res) {
//            if (success) {
//                // save anonymous uid/sid
//                GeneralUtil.saveLoginInfo(MyAccountActivity.this, null, 
//                        (String) res.get("sid"));
//                
//                Intent i = new Intent(MyAccountActivity.this, LoginAccountActivity.class);
//                
//                startActivity(i);
//                
//                GeneralUtil.saveLoggedOut(MyAccountActivity.this);
//                
//                finish();
//            }
//            else
//            {
//            	if (res == null)
//            	{
//            		Toast.makeText(MyAccountActivity.this, R.string.error_http_timeout, Toast.LENGTH_LONG).show();
//            	}
//            }
//        }
//        
//    };

//    private OnClickListener mLogoutButtonListener = new OnClickListener() {
//
//        @Override
//        public void onClick(View v) {
//        	try
//        	{
//        		new LogoutTask(MyAccountActivity.this, mLogoutResultListener).execute();
//        	} catch (RejectedExecutionException e) {
//            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
//            }
//        }
//        
//    };
    
    private OnClickListener mEditButtonListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	saveUserLog(3);
        	
            Intent i = new Intent(getApplicationContext(), EditPhoneDialogActivity.class);
            
            startActivityForResult(i, REQUEST_EDIT_USERINFO);
        }

    };
    
//    private OnClickListener mEditNickButtonListener = new OnClickListener() {
//
//        @Override
//        public void onClick(View v) {
//        	saveUserLog(3);
//        	
//            Intent i = new Intent(getApplicationContext(), EditNickNameDialogActivity.class);
//            
//            startActivityForResult(i, REQUEST_EDIT_USERINFO);
//        }
//
//    };
//    private OnClickListener mChargeListener = new OnClickListener(){
//
//		@Override
//		public void onClick(View v) {
//			
//			saveUserLog(4);
//			
//			Intent i = new Intent(getApplicationContext(), ChargeDialogActivity.class);
//            startActivityForResult(i, REQUEST_CHARGE);
//			
//		}
//    	
//    };
    
   
//    private OnClickListener mChangePasswordButtonListener = new OnClickListener() {
//
//        @Override
//        public void onClick(View v) {
//            
//        	Intent i = new Intent(MyAccountActivity.this, ChangePasswordDialogActivity.class);
//            startActivityForResult(i, REQUEST_CHANGE_PASSWORD);
//        }
//        
//    };
    
//    private OnClickListener mInviteListener = new OnClickListener(){
//
//		@Override
//		public void onClick(View v) {
//			saveUserLog(6);
//			Toast.makeText(getApplicationContext(), R.string.coming_soon, Toast.LENGTH_LONG).show();
//			
////			Intent i = new Intent(MyAccountActivity.this, InviteInfoActivity.class);
////            startActivityForResult(i, REQUEST_CHARGE);
//			
//		}
//    	
//    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        saveUserLog(0);
        MyLog.e("TAG", "MyAccountActivity run.");
        mNickname = (TextView)findViewById(R.id.usernickname);
        mUsername = (TextView)findViewById(R.id.username);
        mPhone = (TextView)findViewById(R.id.phone);
//        mGold = (TextView)findViewById(R.id.gold);
//        mScore = (TextView)findViewById(R.id.score);
//        mWhatisGold = (TextView)findViewById(R.id.what_is_gold);
//        mWhatisScore = (TextView)findViewById(R.id.what_is_score);
        
//        mWhatisGold.setOnClickListener(new OnClickListener()
//        {
//
//			@Override
//			public void onClick(View v) {
//				saveUserLog(1);
//				// TODO Auto-generated method stub
//				Toast.makeText(getApplicationContext(), R.string.coming_soon, Toast.LENGTH_LONG).show();
//			}
//        	
//        }
//        );
//        
//        mWhatisScore.setOnClickListener(new OnClickListener()
//        {
//
//			@Override
//			public void onClick(View v) {
//				saveUserLog(2);
//				// TODO Auto-generated method stub
//				Toast.makeText(getApplicationContext(), R.string.coming_soon, Toast.LENGTH_LONG).show();
//			}
//        	
//        }
//        );
        
        
        mEditBtn = (LongButton)findViewById(R.id.edit_btn);
        mEditBtn.setText(R.string.user_info_editphone);
        mEditBtn.setBackgroundResource(R.drawable.btn_ok_selector);
        mEditBtn.setOnClickListener(mEditButtonListener);
        
//        mEditNickBtn = (LongButton)findViewById(R.id.edit_nick_btn);
//        mEditNickBtn.setText(R.string.user_info_editnick);
//        mEditNickBtn.setOnClickListener(mEditNickButtonListener);

          
        LongButton mXiugai = (LongButton)findViewById(R.id.user_info_xiugai);
    	mXiugai.setText(R.string.user_info_xiugai);
    	mXiugai.setBackgroundResource(R.drawable.btn_ok_selector);
    	mXiugai.setOnClickListener(mXiugai_onkeydown);
//        LongButton chargeBtn = (LongButton)findViewById(R.id.charge_btn);
//        chargeBtn.setText(R.string.user_info_charge);
//        chargeBtn.setOnClickListener(mChargeListener);
        
        setSelectedFooterTab(5);
        
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
//        else if (requestCode == REQUEST_INVITE){
//        	
//        }
        
        super.onActivityResult(requestCode, resultCode, data);
    }
    OnClickListener mXiugai_onkeydown = new OnClickListener() {//修改密码按钮功能

        @Override
        public void onClick(View arg0) {
        	Intent i ;
        	i = new Intent(MyAccountActivity.this, ChangePasswordDialogActivity.class);
            
            startActivity(i);
        }
        
    };
    private void updateUserInfo() {
        UserInfo user = GeneralUtil.getUserInfo(this);

        mUsername.setText(getString(R.string.user_info_name, user.getEmail()));
        
//        mGold.setText(getString(R.string.user_info_gold, user.getBalance()));
        
//        mScore.setText(getString(R.string.user_info_score, "12"));
        
        mNickname.setText(getString(R.string.user_info_nickname,user.getName()));
        
        String userPhone = user.getPhone();
        if (!TextUtils.isEmpty(userPhone)) {
            userPhone = getString(R.string.not_set);
        }
        String s = "null".equals(user.getPhone()) ? getString(R.string.not_set) : user.getPhone();

        mPhone.setText(getString(R.string.user_info_phone, s));
        
//        if(user.getName().length()>0)
//        	mEditNickBtn.setVisibility(View.GONE);
        if(user.getPhone().length()>=11)
        	mEditBtn.setVisibility(View.GONE);
             
    }

    @Override
    protected int getLayout() {
        return R.layout.my_account_activity;
    }
    
    private void saveUserLog(int action)
    {
    	// save user log
		GeneralUtil.saveUserLogType3(MyAccountActivity.this, 21, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+21, "", action);
//		}
    }

}
